<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Location;

class TripPlannerService
{
    protected $openRouterModel;
    protected $openRouterBaseUrl;

    public function __construct()
    {
        $this->openRouterModel = env('OPENROUTER_MODEL', 'google/gemini-2.5-flash-lite');
        $this->openRouterBaseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
    }

    /**
     * Lấy danh sách các API Key từ .env (hỗ trợ nhiều key phân cách bằng dấu phẩy)
     */
    protected function getApiKeys(): array
    {
        $rawKeys = env('OPENROUTER_API_KEYS') ?: env('OPENROUTER_API_KEY');
        if (!$rawKeys) return [];
        $keys = array_map('trim', explode(',', $rawKeys));
        return array_values(array_filter($keys));
    }

    /**
     * Lấy danh sách địa điểm trong DB làm ngữ cảnh
     */
    protected function getLocationContext(): string
    {
        try {
            $locations = Location::with('category')
                ->where('status', 'published')
                ->get();

            if ($locations->isEmpty()) {
                $locations = Location::with('category')->get();
            }

            if ($locations->isEmpty()) return "";

            $context = "\n--- DỮ LIỆU ĐỊA ĐIỂM DU LỊCH NINH BÌNH / HÀ NAM TRONG HỆ THỐNG ---\n";
            foreach ($locations as $loc) {
                $catName = $loc->category->name ?? 'Địa điểm';
                $context .= "- [{$loc->name}](loc:{$loc->id}) [Loại: {$catName}]: " . ($loc->short_description ?? 'Chưa có mô tả ngắn') . ". ";
                if ($loc->address) $context .= "Địa chỉ: {$loc->address}. ";
                if ($loc->average_rating) $context .= "Đánh giá: {$loc->average_rating}/5. ";
                $context .= "\n";
            }
            $context .= "---------------------------------------------------------\n";
            return $context;
        } catch (\Exception $e) {
            Log::warning('Cannot fetch locations for TripPlanner: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Gọi OpenRouter API hỗ trợ xoay vòng nhiều Key và tự động fallback model
     */
    protected function callAI(string $systemPrompt, string $userPrompt, int $maxTokens = 800, float $temperature = 0.7): ?string
    {
        $apiKeys = $this->getApiKeys();
        if (empty($apiKeys)) {
            Log::error('TripPlannerService: No API Key configured.');
            return null;
        }

        $modelsToTry = array_unique([
            $this->openRouterModel,
            'google/gemini-2.5-flash-lite',
            'openrouter/auto'
        ]);

        foreach ($apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'HTTP-Referer' => 'http://localhost',
                        'X-Title' => 'Ninh Binh POI Trip Planner',
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(35)
                    ->post($this->openRouterBaseUrl . '/chat/completions', [
                        'model' => $modelName,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $content = $data['choices'][0]['message']['content'] ?? '';
                        if (!empty($content)) {
                            $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
                            $content = preg_replace('/\s*```$/i', '', $content);
                            return $content;
                        }
                    }

                    Log::warning("TripPlanner Key #" . ($keyIndex + 1) . " model {$modelName} warning: " . $response->body());
                } catch (\Exception $e) {
                    Log::warning("TripPlanner Key #" . ($keyIndex + 1) . " model {$modelName} exception: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Làm sạch và giải mã chuỗi JSON từ kết quả trả về của AI
     */
    protected function cleanAndDecodeJson(string $rawContent): ?array
    {
        $clean = trim($rawContent);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Bóc tách khối JSON {...} bằng Regex nếu có văn bản thừa xung quanh
        if (preg_match('/\{[\s\S]*\}/', $clean, $matches)) {
            $jsonCandidate = $matches[0];
            $decoded = json_decode($jsonCandidate, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // Loại bỏ trailing commas (dấu phẩy thừa trước ] hoặc })
            $sanitized = preg_replace('/,\s*([\]\}])/', '$1', $jsonCandidate);
            $decoded = json_decode($sanitized, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * AI sinh câu hỏi tiếp theo dựa trên hồ sơ các lựa chọn trước đó
     */
    public function generateNextQuestion(array $answers, int $stepNumber): array
    {
        $answersText = '';
        if (!empty($answers)) {
            foreach ($answers as $a) {
                $q = $a['question'] ?? '';
                $ans = $a['answer'] ?? '';
                if ($q && $ans) {
                    $answersText .= "- {$q}: {$ans}\n";
                }
            }
        }

        $systemPrompt = 'Bạn là chuyên gia thiết kế lịch trình du lịch Ninh Bình thông minh.
Nhiệm vụ của bạn là phân tích các lựa chọn hiện tại của người dùng và sinh ra CÂU HỎI TIẾP THEO phù hợp để hiểu rõ hơn nhu cầu của họ.

QUY TẮC BẮT BUỘC:
1. Trả về ĐÚNG 1 ĐỐI TƯỢNG JSON thuần túy (không kèm markdown, không kèm lời giải thích).
2. Cấu trúc JSON bắt buộc:
{
  "done": false,
  "greeting": "Lời nhận xét ngắn gọn hoặc nhận xét ấm áp về lựa chọn trước (ví dụ: Thật tuyệt khi đi cùng gia đình! Hoặc Chuyến đi tâm linh 2 ngày rất phù hợp.)",
  "question": "Câu hỏi tiếp theo (ví dụ: Trong đoàn có trẻ em dưới 6 tuổi hay người lớn tuổi không?)",
  "type": "single" hoặc "multi",
  "options": [
    { "value": "ma_gia_tri_1", "label": "Nhãn hiển thị 1" },
    { "value": "ma_gia_tri_2", "label": "Nhãn hiển thị 2" },
    { "value": "ma_gia_tri_3", "label": "Nhãn hiển thị 3" }
  ]
}

3. Nếu thông tin đã ĐỦ (đã hỏi qua 3-5 câu hỏi bao gồm kiểu chuyến đi, thời gian, đối tượng, sở thích/ưu tiên), hãy trả về JSON thông báo xong:
{
  "done": true,
  "greeting": "Mình đã nắm đủ thông tin chuyến đi của bạn rồi! Hãy bấm Tạo lịch trình để mình thiết kế nhé."
}

4. Tuyệt đối KHÔNG lặp lại các câu hỏi đã có trong danh sách bên dưới.
5. Câu hỏi sinh ra phải thích ứng theo ngữ cảnh:
   - Nếu là Gia đình ➔ Hỏi về có trẻ nhỏ/người lớn tuổi không.
   - Nếu là Couple ➔ Hỏi về phong cách (Lãng mạn, chụp ảnh, ngắm hoàng hôn...).
   - Nếu là Tâm linh ➔ Hỏi thích Chùa, Đền, Phủ hay kết hợp.
   - Nếu là Food Tour ➔ Hỏi thích ăn đặc sản địa phương, ăn vặt hay nhà hàng sang trọng.
   - Nếu chưa có thông tin thời gian ➔ Hỏi thời lượng (Nửa ngày, 1 ngày, 2 ngày 1 đêm...).
   - Nếu chưa có thông tin ngân sách ➔ Hỏi mức ngân sách.';

        $userPrompt = "Thông tin chuyến đi đã chọn đến hiện tại (Bước {$stepNumber}):\n{$answersText}\nHãy sinh ra câu hỏi tiếp theo ở dạng JSON.";

        $rawResponse = $this->callAI($systemPrompt, $userPrompt, 400, 0.7);

        if (!$rawResponse) {
            return [
                'done' => true,
                'greeting' => 'Đã thu thập đủ thông tin cơ bản. Bạn có thể bấm Tạo lịch trình ngay!'
            ];
        }

        $decoded = $this->cleanAndDecodeJson($rawResponse);
        if (is_array($decoded) && (isset($decoded['question']) || isset($decoded['done']))) {
            return $decoded;
        }

        return [
            'done' => true,
            'greeting' => 'Đã ghi nhận thông tin chuyến đi. Hãy bấm Tạo lịch trình nhé!'
        ];
    }

    /**
     * AI sinh lịch trình Timeline hoàn chỉnh
     */
    public function generateItinerary(array $answers): array
    {
        $locationInfo = $this->getLocationContext();

        $answersText = '';
        foreach ($answers as $a) {
            $q = $a['question'] ?? '';
            $ans = $a['answer'] ?? '';
            if ($q && $ans) {
                $answersText .= "- {$q}: {$ans}\n";
            }
        }

        $systemPrompt = "Bạn là chuyên gia lập kế hoạch du lịch Ninh Bình / Hà Nam chuyên nghiệp.
Nhiệm vụ của bạn là lập LỊCH TRÌNH DU LỊCH CHI TIẾT theo dạng Timeline dựa trên hồ sơ mong muốn của người dùng.

QUY TẮC BẮT BUỘC:
1. Trả về ĐÚNG 1 ĐỐI TƯỢNG JSON thuần túy (không kèm markdown):
{
  \"title\": \"Tiêu đề chuyến đi (ví dụ: HÀNH TRÌNH KHÁM PHÁ NINH BÌNH 2 NGÀY 1 ĐÊM)\",
  \"summary\": \"Tóm tắt ngắn gọn 1-2 câu về chuyến đi\",
  \"estimated_cost\": \"Ước tính chi phí (ví dụ: 1.200.000 - 1.800.000 VNĐ / người)\",
  \"days\": [
    {
      \"day\": 1,
      \"title\": \"Ngày 1: Tiêu đề ngày 1\",
      \"slots\": [
        {
          \"time\": \"08:00 - 10:00\",
          \"activity\": \"Mô tả chi tiết hoạt động\",
          \"location\": \"Tên địa điểm\",
          \"location_id\": 1,
          \"type\": \"visit\" (chọn 1 trong: visit, food, transport, rest, photo),
          \"tip\": \"Mẹo nhỏ cho hoạt động này (nếu có)\"
        }
      ]
    }
  ],
  \"tips\": [
    \"Lưu ý quan trọng 1\",
    \"Lưu ý quan trọng 2\"
  ]
}

2. Ưu tiên sử dụng các địa điểm thực tế trong hệ thống bên dưới và gán `location_id` chính xác nếu có.
3. Phân bổ thời gian thực tế, hợp lý, không bắt di chuyển quá xa liên tục.
4. Nếu người dùng không nêu điểm xuất phát, mặc định lịch trình bắt đầu trực tiếp tại điểm tham quan Ninh Bình, không tự tiện mặc định di chuyển từ Hà Nội.

{$locationInfo}";

        $userPrompt = "Hồ sơ mong muốn của người dùng:\n{$answersText}\nHãy sinh lịch trình dạng JSON chi tiết.";

        $rawResponse = $this->callAI($systemPrompt, $userPrompt, 1500, 0.7);

        if ($rawResponse) {
            $decoded = $this->cleanAndDecodeJson($rawResponse);
            if (is_array($decoded) && (isset($decoded['days']) || isset($decoded['title']))) {
                return ['success' => true, 'itinerary' => $decoded];
            }

            return ['success' => true, 'raw' => $rawResponse];
        }

        return ['success' => false, 'error' => 'Không thể tạo lịch trình lúc này. Vui lòng thử lại.'];
    }
}
