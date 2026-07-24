<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Location;

class ChatbotService
{
    protected $openRouterApiKey;
    protected $openRouterModel;
    protected $openRouterBaseUrl;

    public function __construct()
    {
        $this->openRouterApiKey = env('OPENROUTER_API_KEY');
        $this->openRouterModel = env('OPENROUTER_MODEL', 'google/gemini-2.5-flash');
        $this->openRouterBaseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
    }

    /**
     * Lấy danh sách địa điểm từ Database để nạp kiến thức cho AI
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

            if ($locations->isEmpty()) {
                return "";
            }

            $context = "\n--- DỮ LIỆU ĐỊA ĐIỂM DU LỊCH TRONG HỆ THỐNG NINH BÌNH POI ---\n";
            foreach ($locations as $loc) {
                $catName = $loc->category->name ?? 'Địa điểm';
                $context .= "- **{$loc->name}** (ID: {$loc->id}) [Loại: {$catName}]: " . ($loc->short_description ?? 'Chưa có mô tả ngắn') . ". ";
                if ($loc->address) {
                    $context .= "Địa chỉ: {$loc->address}. ";
                }
                if ($loc->opening_hours) {
                    $context .= "Giờ mở cửa: {$loc->opening_hours}. ";
                }
                if ($loc->average_rating) {
                    $context .= "Đánh giá: {$loc->average_rating}/5⭐. ";
                }
                $context .= "\n";
            }
            $context .= "---------------------------------------------------------\n";
            return $context;
        } catch (\Exception $e) {
            Log::warning('Cannot fetch locations for chatbot context: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Send message to OpenRouter API and get response
     *
     * @param string $message User's message
     * @param array $history Previous conversation history
     * @return string API response content
     */
    public function sendMessage(string $message, array $history = []): string
    {
        $messages = [];
        
        $locationInfo = $this->getLocationContext();

        // System prompt chuyên sâu
        $systemPrompt = "Bạn là chuyên gia tư vấn du lịch & trợ lý ảo AI thông minh của \"Cổng Thông Tin Du Lịch Ninh Bình\".

Nhiệm vụ và phong cách làm việc của bạn:
1. **Tư vấn & Hỗ trợ nhiệt tình**: Giải đáp thắc mắc, gợi ý địa điểm, ẩm thực đặc sản (cơm cháy, thịt dê...), phương tiện di chuyển, thời điểm du lịch đẹp nhất.
2. **Lên Lịch Trình Chi Tiết**: Khi người dùng yêu cầu lên lịch trình (1 ngày, 2 ngày 1 đêm, 3 ngày 2 đêm, du lịch gia đình/cặp đôi/phượt...), hãy lập kế hoạch chi tiết từng buổi (Sáng, Trưa, Chiều, Tối) hợp lý về thời gian và di chuyển.
3. **Sử Dụng Dữ Liệu Thực Tế**: Ưu tiên sử dụng và giới thiệu các địa điểm có trong cơ sở dữ liệu hệ thống bên dưới.
4. **Nhớ Ngữ Cảnh Hội Thoại**: Luôn ghi nhớ nội dung các câu hỏi và câu trả lời trước đó của người dùng để trả lời nối tiếp mạch hội thoại một cách tự nhiên, thông minh.
5. **Trình Bày Thanh Lịch & Tự Nhiên**: Sử dụng định dạng Markdown nhẹ nhàng (gạch đầu dòng). Hạn chế tối đa việc sử dụng biểu tượng cảm xúc (emoji/icon) rườm rà. Trả lời thẳng vào trọng tâm, súc tích, văn phong lịch sự.
6. **Định Dạng Liên Kết Địa Điểm Để Định Vị Bản Đồ**: Khi giới thiệu hoặc nhắc đến bất kỳ địa điểm nào có trong danh sách cơ sở dữ liệu bên dưới, bạn BẮT BUỘC phải viết tên địa điểm dưới dạng liên kết: [Tên Địa Điểm](loc:ID) (Ví dụ: [Chùa Bái Đính](loc:5) hoặc [Quần thể danh thắng Tràng An](loc:12)). Điều này giúp người dùng nhấp trực tiếp vào tên địa điểm để bay bản đồ tới vị trí đó!

{$locationInfo}";

        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt
        ];

        // Add history
        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        // Add user message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openRouterApiKey,
                'HTTP-Referer' => 'http://localhost',
                'X-Title' => 'Ninh Binh POI',
                'Content-Type' => 'application/json',
            ])
            ->timeout(45)
            ->post($this->openRouterBaseUrl . '/chat/completions', [
                'model' => $this->openRouterModel,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1200,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['choices'][0]['message']['content'])) {
                    return $data['choices'][0]['message']['content'];
                }
            }

            Log::error('OpenRouter API Response Error: ' . $response->body());
            return 'Xin lỗi, hệ thống AI hiện chưa thể xử lý câu trả lời lúc này. Vui lòng thử lại!';

        } catch (\Exception $e) {
            Log::error('OpenRouter API Exception: ' . $e->getMessage());
            return 'Xin lỗi, không thể kết nối đến máy chủ OpenRouter AI.';
        }
    }
}
