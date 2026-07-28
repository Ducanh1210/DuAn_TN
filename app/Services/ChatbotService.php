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
                $context .= "- [{$loc->name}](loc:{$loc->id}) [Loại: {$catName}]: " . ($loc->short_description ?? 'Chưa có mô tả ngắn') . ". ";
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
        $systemPrompt = "Bạn là Trợ lý ảo AI thông minh của \"Cổng Thông Tin Du Lịch Ninh Bình\".

QUY TẮC BẮT BUỘC VỀ DANH TÍNH & PHẠM VI TRẢ LỜI:
1. **Danh Tính**: Bạn là \"Trợ lý ảo Du lịch Ninh Bình\". Nếu được hỏi bạn là ai hay mô hình AI nào, bạn KHÔNG ĐƯỢC xưng là Gemini hay mô hình của Google/OpenAI. Hãy khẳng định bạn là Trợ lý ảo Du lịch Ninh Bình.
2. **Phạm Vi Trả Lời (CHỈ DU LỊCH)**: Bạn CHỈ được phép tư vấn và trả lời các chủ đề liên quan đến: du lịch, địa điểm tham quan, ẩm thực đặc sản, lịch trình di chuyển, khách sạn/homestay, thời tiết, kinh nghiệm du lịch Ninh Bình và khu vực lân cận.
3. **Từ Chối Câu Hỏi Không Liên Quan**: Nếu người dùng hỏi các chủ đề KHÔNG liên quan đến du lịch (như: xem YouTube, tin học/công nghệ, lập trình, toán học, giải bài tập, bóng đá, chính trị...), bạn BẮT BUỘC phải từ chối lịch sự và hướng người dùng quay lại chủ đề du lịch.
   *Mẫu từ chối*: \"Xin lỗi bạn, mình là Trợ lý ảo Du lịch Ninh Bình nên chỉ hỗ trợ các thông tin liên quan đến du lịch, địa điểm, ẩm thực và lịch trình thôi ạ. Bạn có muốn mình gợi ý địa điểm đẹp hay hỗ trợ lên lịch trình không?\"
4. **Tư vấn & Hỗ trợ nhiệt tình**: Gợi ý địa điểm, ẩm thực đặc sản (cơm cháy, thịt dê...), phương tiện di chuyển, thời điểm du lịch đẹp nhất.
5. **Lên Lịch Trình Chi Tiết**: Khi người dùng yêu cầu lên lịch trình (1 ngày, 2 ngày 1 đêm...), hãy lập kế hoạch chi tiết từng buổi (Sáng, Trưa, Chiều, Tối) hợp lý. Nếu người dùng KHÔNG nêu điểm xuất phát, mặc định lịch trình bắt đầu trực tiếp tại Ninh Binh/điểm tham quan, TUYỆT ĐỐI KHÔNG tự động mặc định xuất phát từ Hà Nội trừ khi người dùng yêu cầu rõ.
6. **Sử Dụng Dữ Liệu Thực Tế**: Ưu tiên sử dụng và giới thiệu các địa điểm có trong cơ sở dữ liệu hệ thống bên dưới.
7. **Trình Bày Thanh Lịch**: Sử dụng định dạng Markdown nhẹ nhàng (gạch đầu dòng). Hạn chế tối đa biểu tượng cảm xúc (emoji/icon) rườm rà. Trả lời súc tích, lịch sự.
8. **Định Dạng Liên Kết Địa Điểm**: Khi giới thiệu bất kỳ địa điểm nào có trong danh sách cơ sở dữ liệu bên dưới, bạn BẮT BUỘC phải viết tên địa điểm theo ĐÚNG cú pháp liên kết: [Tên Địa Điểm](loc:ID) (Ví dụ: [Chùa Tam Chúc](loc:1) hoặc [Chùa Bái Đính](loc:5)). TUYỆT ĐỐI KHÔNG viết dạng \"(ID: 1)\" hay \"**Tên** (ID: 1)\".

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

        $apiKeys = $this->getApiKeys();
        $modelsToTry = array_unique([
            $this->openRouterModel,
            'google/gemini-2.5-flash-lite',
            'openrouter/auto'
        ]);

        if (empty($apiKeys)) {
            Log::error('ChatbotService: No OpenRouter API Key configured.');
            return 'Xin lỗi, hệ thống chưa được cấu hình API Key AI.';
        }

        // Vòng lặp xoay vòng qua từng Key -> nếu Key này hết tiền/lỗi thì tự sang Key tiếp theo
        foreach ($apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'HTTP-Referer' => 'http://localhost',
                        'X-Title' => 'Ninh Binh POI',
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post($this->openRouterBaseUrl . '/chat/completions', [
                        'model' => $modelName,
                        'messages' => $messages,
                        'temperature' => 0.7,
                        'max_tokens' => 1000,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (!empty($data['choices'][0]['message']['content'])) {
                            return $data['choices'][0]['message']['content'];
                        }
                    }

                    Log::warning("ChatbotService Key #" . ($keyIndex + 1) . " with model {$modelName} failed: " . $response->body());
                } catch (\Exception $e) {
                    Log::warning("ChatbotService Key #" . ($keyIndex + 1) . " with model {$modelName} exception: " . $e->getMessage());
                }
            }
        }

        return 'Xin lỗi, hệ thống AI hiện chưa thể xử lý câu trả lời lúc me. Vui lòng thử lại!';
    }
}
