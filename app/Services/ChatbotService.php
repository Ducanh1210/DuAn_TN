<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Location;

/**
 * Dịch vụ trợ lý ảo (chatbot) tư vấn du lịch Ninh Bình.
 * Nạp danh sách địa điểm thật trong DB làm ngữ cảnh cho AI (chống bịa địa điểm),
 * gọi Gemini để trả lời và làm sạch các link địa điểm [Tên](loc:ID) do AI sinh ra.
 */
class ChatbotService
{
    protected GeminiClient $gemini;

    /** @var \Illuminate\Support\Collection<int, Location>|null */
    protected $locationIndex = null;

    public function __construct(?GeminiClient $gemini = null)
    {
        $this->gemini = $gemini ?? new GeminiClient();
    }

    /**
     * Lấy và cache danh sách địa điểm dùng cho ngữ cảnh AI + làm sạch link.
     */
    protected function getLocations()
    {
        if ($this->locationIndex !== null) {
            return $this->locationIndex;
        }

        try {
            $locations = Location::with('category')
                ->where('status', 'published')
                ->get();

            if ($locations->isEmpty()) {
                $locations = Location::with('category')->get();
            }

            $this->locationIndex = $locations;
        } catch (\Exception $e) {
            Log::warning('Không lấy được danh sách địa điểm cho chatbot: ' . $e->getMessage());
            $this->locationIndex = collect();
        }

        return $this->locationIndex;
    }

    /**
     * Lấy danh sách địa điểm từ Database để nạp kiến thức cho AI
     */
    protected function getLocationContext(): string
    {
        $locations = $this->getLocations();

        if ($locations->isEmpty()) {
            return "";
        }

        $context = "\n--- DANH SÁCH ĐỊA ĐIỂM TRONG HỆ THỐNG (CHỈ ĐƯỢC DÙNG CÁC MỤC NÀY) ---\n";
        $context .= "Mỗi dòng: ID | Tên chính xác | Loại | Tọa độ | Địa chỉ | Mô tả ngắn\n";

        foreach ($locations as $loc) {
            $catName = $loc->category->name ?? 'Địa điểm';
            $lat = $loc->lat !== null ? (string) $loc->lat : '?';
            $lng = $loc->lng !== null ? (string) $loc->lng : '?';
            $desc = trim((string) ($loc->short_description ?? ''));
            if ($desc === '') {
                $desc = 'Chưa có mô tả';
            }
            $addr = trim((string) ($loc->address ?? ''));

            $context .= "- ID {$loc->id} | {$loc->name} | {$catName} | lat={$lat}, lng={$lng}";
            if ($addr !== '') {
                $context .= " | {$addr}";
            }
            $context .= " | {$desc}";
            if ($loc->opening_hours) {
                $context .= " | Giờ: {$loc->opening_hours}";
            }
            if ($loc->average_rating) {
                $context .= " | Đánh giá: {$loc->average_rating}/5";
            }
            $context .= "\n";
            $context .= "  Link bắt buộc khi nhắc tới: [{$loc->name}](loc:{$loc->id})\n";
        }

        $context .= "---------------------------------------------------------\n";
        return $context;
    }

    /**
     * Sửa / gỡ thẻ [Tên](loc:ID) sai: ID không tồn tại, tên bịa, gắn nhầm chỗ.
     */
    protected function sanitizeLocationLinks(string $content): string
    {
        $locations = $this->getLocations();
        if ($locations->isEmpty()) {
            return $content;
        }

        $byId = $locations->keyBy(fn ($l) => (string) $l->id);

        return preg_replace_callback(
            '/\[([^\]]+)\]\(loc:([^)\s]+)\)/u',
            function ($m) use ($byId, $locations) {
                $displayName = trim($m[1]);
                $rawId = trim($m[2]);

                if (isset($byId[$rawId])) {
                    $loc = $byId[$rawId];
                    // Giữ đúng tên trong DB — tránh AI gắn "Bái Đính" vào ID chùa khác
                    return '[' . $loc->name . '](loc:' . $loc->id . ')';
                }

                // ID ảo: thử khớp theo tên
                $matched = $this->findLocationByName($displayName, $locations);
                if ($matched) {
                    return '[' . $matched->name . '](loc:' . $matched->id . ')';
                }

                // Không có trong DB → bỏ link, giữ text thường
                return $displayName;
            },
            $content
        ) ?? $content;
    }

    protected function findLocationByName(string $name, $locations): ?Location
    {
        $needle = mb_strtolower(trim($name));
        if ($needle === '') {
            return null;
        }

        // Khớp chính xác tên
        foreach ($locations as $loc) {
            if (mb_strtolower($loc->name) === $needle) {
                return $loc;
            }
        }

        // Khớp chứa nhau (đặt độ dài tối thiểu để tránh khớp nhiễu)
        if (mb_strlen($needle) < 4) {
            return null;
        }

        foreach ($locations as $loc) {
            $hay = mb_strtolower($loc->name);
            if (str_contains($hay, $needle) || str_contains($needle, $hay)) {
                return $loc;
            }
        }

        return null;
    }

    /**
     * Gửi tin nhắn tới AI (Gemini) và nhận câu trả lời đã làm sạch link.
     *
     * @param string $message Tin nhắn của người dùng
     * @param array $history Lịch sử hội thoại trước đó
     * @return string Nội dung phản hồi của trợ lý
     */
    public function sendMessage(string $message, array $history = []): string
    {
        $messages = [];

        $locationInfo = $this->getLocationContext();

        $systemPrompt = "Bạn là Trợ lý ảo AI của \"Cổng Thông Tin Du Lịch Ninh Bình\".

QUY TẮC BẮT BUỘC:

1) Danh tính: Xưng là \"Trợ lý ảo Du lịch Ninh Bình\". Không nói mình là Gemini/ChatGPT/Google/OpenAI.

2) Phạm vi: CHỈ tư vấn du lịch (địa điểm, ẩm thực, lịch trình, lưu trú, di chuyển, thời tiết/kinh nghiệm khu vực các điểm trong danh sách). Câu hỏi ngoài chủ đề → từ chối lịch sự và kéo về du lịch.

3) CHỐNG BỊA ĐỊA ĐIỂM (QUAN TRỌNG NHẤT):
- CHỈ được giới thiệu / đưa vào lịch trình các địa điểm CÓ TRONG DANH SÁCH bên dưới.
- CẤM bịa hoặc nhắc các điểm nổi tiếng nếu KHÔNG có trong danh sách (ví dụ: Chùa Bái Đính, Tràng An, Tam Cốc, Hang Múa, Cố đô Hoa Lư… trừ khi đúng tên đó xuất hiện trong danh sách).
- Không gắn ID của một địa điểm này cho tên của địa điểm khác.
- Khi nói ăn uống: chỉ gắn link loc: cho quán/điểm thuộc loại Ẩm thực (hoặc điểm ăn thật sự trong list). Không gắn link chùa/đền vào câu \"ăn trưa\".
- Nếu danh sách chủ yếu ở Hà Nam / khu vực lân cận: lập lịch dựa trên các điểm ĐÓ, nói rõ khu vực thực tế; đừng ép lịch \"Ninh Bình kinh điển\" khi không có dữ liệu.

4) Lịch trình phải hợp lý địa lý:
- Dùng tọa độ lat/lng trong danh sách để nhóm điểm GẦN NHAU trong cùng buổi.
- Tránh nhảy xa giữa hai điểm trong cùng buổi nếu có lựa chọn gần hơn trong list.
- Nếu user không nói điểm xuất phát: bắt đầu tại cụm điểm trong list (không mặc định Hà Nội).
- Mỗi buổi (Sáng/Trưa/Chiều/Tối): 1–2 điểm chính + thời gian ước lượng; ngắn gọn.

5) Định dạng link địa điểm:
- Khi nhắc địa điểm trong list, VIẾT ĐÚNG: [Tên chính xác trong list](loc:ID) — ID và Tên phải khớp dòng trong list.
- Ví dụ đúng (nếu có trong list): [Chùa Tam Chúc](loc:1)
- CẤM ví dụ/bịa kiểu [Chùa Bái Đính](loc:5) nếu không có đúng dòng đó.
- Không viết \"(ID: 1)\" hay dạng khác.

6) Trình bày: Markdown nhẹ (gạch đầu dòng, **in đậm** tiêu đề buổi). Ít emoji. Súc tích, lịch sự.

{$locationInfo}";

        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt
        ];

        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        if (!$this->gemini->isConfigured()) {
            Log::error('ChatbotService: Chưa cấu hình GEMINI_API_KEY.');
            return 'Xin lỗi, hệ thống chưa được cấu hình API Key AI.';
        }

        $content = $this->gemini->generate($messages, 0.35, 1400, 70);
        if ($content) {
            return $this->sanitizeLocationLinks($content);
        }

        return 'Xin lỗi, hệ thống AI hiện chưa thể xử lý câu trả lời lúc này. Vui lòng thử lại!';
    }
}
