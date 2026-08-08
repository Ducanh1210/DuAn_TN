<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    protected $apiKeys;
    protected $model;
    protected $baseUrl;

    public function __construct()
    {
        $this->model = env('OPENROUTER_MODEL', 'google/gemini-2.5-flash');
        $this->baseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
        $this->apiKeys = $this->getApiKeys();
    }

    protected function getApiKeys(): array
    {
        $rawKeys = env('OPENROUTER_API_KEYS') ?: env('OPENROUTER_API_KEY');
        if (!$rawKeys) {
            return [];
        }
        $keys = array_map('trim', explode(',', $rawKeys));
        return array_values(array_filter($keys));
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKeys);
    }

    /**
     * Phân loại một loạt bình luận.
     *
     * @param array $items Mảng [['id' => int, 'content' => string], ...]
     * @return array [id => ['flag' => 'safe|suspect|violation', 'score' => 0-100, 'reason' => string]]
     */
    public function moderateBatch(array $items): array
    {
        $results = [];
        if (empty($items) || !$this->isConfigured()) {
            return $results;
        }

        // Chia nhỏ để prompt ổn định và JSON dễ parse
        $chunks = array_chunk($items, 12);

        foreach ($chunks as $chunk) {
            $chunkResult = $this->moderateChunk($chunk);
            foreach ($chunkResult as $id => $data) {
                $results[$id] = $data;
            }
        }

        return $results;
    }

    protected function moderateChunk(array $chunk): array
    {
        $listText = '';
        foreach ($chunk as $item) {
            $content = trim((string) ($item['content'] ?? ''));
            $content = str_replace(["\r", "\n"], ' ', $content);
            $content = mb_substr($content, 0, 500);
            $listText .= "ID {$item['id']}: \"{$content}\"\n";
        }

        $systemPrompt = <<<PROMPT
Bạn là hệ thống kiểm duyệt nội dung tiếng Việt cho một website đánh giá địa điểm du lịch Ninh Bình.
Nhiệm vụ: đánh giá từng bình luận và phát hiện nội dung VI PHẠM hoặc NGHI NGỜ vi phạm.

Các loại vi phạm cần chú ý:
- Ngôn từ thù ghét, xúc phạm, lăng mạ, tục tĩu, chửi bới.
- Spam, quảng cáo, rao vặt, chèn link/số điện thoại/website để câu kéo.
- Lừa đảo, thông tin sai lệch gây hại, dụ dỗ chuyển tiền.
- Nội dung người lớn, khiêu dâm, bạo lực.
- Nội dung không liên quan, phá hoại (ký tự vô nghĩa, spam lặp lại).

Phân loại mỗi bình luận vào 1 trong 3 nhãn:
- "violation": chắc chắn vi phạm, nên ẩn/xử lý.
- "suspect": có dấu hiệu đáng ngờ, cần người kiểm tra lại.
- "safe": bình thường, không vấn đề.

Kèm "score" là mức độ rủi ro 0-100 (0 = hoàn toàn an toàn, 100 = vi phạm nghiêm trọng).
Kèm "reason" ngắn gọn bằng tiếng Việt (tối đa 15 từ) giải thích vì sao; nếu safe thì reason để chuỗi rỗng "".

CHỈ trả về JSON hợp lệ, KHÔNG kèm giải thích, KHÔNG markdown, theo đúng định dạng:
[{"id": 1, "flag": "safe", "score": 0, "reason": ""}, {"id": 2, "flag": "violation", "score": 90, "reason": "..."}]
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Đánh giá các bình luận sau:\n{$listText}"],
        ];

        $modelsToTry = array_unique([
            $this->model,
            'google/gemini-2.5-flash-lite',
            'openrouter/auto',
        ]);

        foreach ($this->apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'HTTP-Referer' => 'http://localhost',
                        'X-Title' => 'Ninh Binh POI Moderation',
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(60)
                    ->post($this->baseUrl . '/chat/completions', [
                        'model' => $modelName,
                        'messages' => $messages,
                        'temperature' => 0.1,
                        'max_tokens' => 1500,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $content = $data['choices'][0]['message']['content'] ?? '';
                        $parsed = $this->parseJsonArray($content);
                        if (!empty($parsed)) {
                            return $parsed;
                        }
                    }

                    Log::warning("ModerationService Key #" . ($keyIndex + 1) . " model {$modelName} failed: " . $response->body());
                } catch (\Exception $e) {
                    Log::warning("ModerationService Key #" . ($keyIndex + 1) . " model {$modelName} exception: " . $e->getMessage());
                }
            }
        }

        return [];
    }

    /**
     * Bóc tách JSON array từ phản hồi AI (có thể lẫn ```json ... ```).
     */
    protected function parseJsonArray(string $content): array
    {
        $content = trim($content);
        // Gỡ code fences nếu có
        $content = preg_replace('/^```(?:json)?/i', '', $content);
        $content = preg_replace('/```$/', '', trim($content));
        $content = trim($content);

        // Lấy đoạn từ [ đầu tiên đến ] cuối cùng
        $start = strpos($content, '[');
        $end = strrpos($content, ']');
        if ($start === false || $end === false || $end <= $start) {
            return [];
        }
        $json = substr($content, $start, $end - $start + 1);

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        $out = [];
        foreach ($decoded as $row) {
            if (!isset($row['id'])) {
                continue;
            }
            $id = (int) $row['id'];
            $flag = strtolower((string) ($row['flag'] ?? 'safe'));
            if (!in_array($flag, ['safe', 'suspect', 'violation'], true)) {
                $flag = 'safe';
            }
            $score = (int) ($row['score'] ?? 0);
            $score = max(0, min(100, $score));
            $reason = trim((string) ($row['reason'] ?? ''));
            $reason = mb_substr($reason, 0, 490);

            $out[$id] = [
                'flag' => $flag,
                'score' => $score,
                'reason' => $reason,
            ];
        }

        return $out;
    }
}
