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
     * Lấy danh sách địa điểm trong DB làm ngữ cảnh (kèm tọa độ GPS để AI tính toán khoảng cách và gom cụm)
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

            $context = "\n--- DANH SÁCH BẮT BUỘC 100% CÁC ĐỊA ĐIỂM DU LỊCH TRONG CSDL (KÈM TỌA ĐỘ GPS ĐỂ GOM CỤM ĐIỂM GẦN NHAU) ---\n";
            foreach ($locations as $loc) {
                $catName = $loc->category->name ?? 'Địa điểm';
                $lat = $loc->lat ? round($loc->lat, 4) : 'N/A';
                $lng = $loc->lng ? round($loc->lng, 4) : 'N/A';
                $context .= "- ID:{$loc->id} | \"{$loc->name}\" | Loại:{$catName} | GPS:({$lat},{$lng}) | Địa chỉ:" . ($loc->address ?? '') . "\n";
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
                        'X-Title' => 'POI Trip Planner',
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
        if (preg_match('/\{[\s\S]*/', $clean, $matches)) {
            $jsonCandidate = $matches[0];
            
            // Tìm ký tự } cuối cùng
            $lastBrace = strrpos($jsonCandidate, '}');
            if ($lastBrace !== false) {
                $jsonCandidate = substr($jsonCandidate, 0, $lastBrace + 1);
            }

            // Xử lý control characters (xuống dòng trong string)
            $jsonCandidate = preg_replace('/[\x00-\x1F\x7F]/', ' ', $jsonCandidate);
            
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

        $systemPrompt = 'Bạn là chuyên gia thiết kế lịch trình du lịch thông minh.
Nhiệm vụ của bạn là xem xét hồ sơ du lịch đã thu thập được từ người dùng (bao gồm kiểu chuyến đi, đối tượng đi cùng, phương tiện, thời gian/khách sạn, ngân sách) và sinh ra CÂU HỎI BỔ SUNG CHUYÊN SÂU HOẶC CUỐI CÙNG để cá nhân hóa chi tiết chuyến đi theo sở thích riêng của họ.

QUY TẮC BẮT BUỘC:
1. Trả về ĐÚNG 1 ĐỐI TƯỢNG JSON thuần túy (không kèm markdown):
{
  "done": false,
  "greeting": "Lời nhận xét ngắn gọn, tinh tế hoặc khen ngợi ấm áp về hồ sơ đã chọn",
  "question": "Câu hỏi bổ sung chuyên sâu (Ví dụ: Về sở thích ăn uống đặc sản, phong cách chụp ảnh/check-in, nhịp độ di chuyển, hay trải nghiệm đặc biệt...)",
  "type": "single" hoặc "multi",
  "options": [
    { "value": "ma_gia_tri_1", "label": "Nhãn hiển thị 1" },
    { "value": "ma_gia_tri_2", "label": "Nhãn hiển thị 2" },
    { "value": "other", "label": "Khác..." }
  ]
}

2. Nếu thông tin trong hồ sơ đã đầy đủ hoặc đã hỏi qua 1 câu hỏi bổ sung chuyên sâu, hãy trả về "done": true:
{
  "done": true,
  "greeting": "Tuyệt vời! Mình đã nắm đầy đủ thông tin chi tiết cho chuyến đi của bạn rồi. Hãy bấm Tạo lịch trình để mình thiết kế nhé!"
}

3. Luôn nhớ thêm lựa chọn { "value": "other", "label": "Khác..." } ở cuối danh sách options.';

        $userPrompt = "Hồ sơ chuyến đi người dùng đã chọn đến hiện tại:\n{$answersText}\nHãy sinh ra câu hỏi bổ sung chuyên sâu dạng JSON.";

        $rawResponse = $this->callAI($systemPrompt, $userPrompt, 350, 0.7);

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

        $systemPrompt = "Bạn là chuyên gia lập kế hoạch du lịch chuyên nghiệp.
Nhiệm vụ của bạn là lập LỊCH TRÌNH DU LỊCH CHI TIẾT theo dạng Timeline dựa trên hồ sơ mong muốn của người dùng.

QUY TẮC BẮT BUỘC (TUYỆT ĐỐI TUÂN THỦ 100%):

1. BẮT BUỘC DỰA VÀO TỌA ĐỘ GPS `GPS:(lat,lng)` CỦA CÁC ĐỊA ĐIỂM TRONG DANH SÁCH BÊN DƯỚI ĐỂ TÍNH TOÁN KHOẢNG CÁCH VÀ GOM CỤM VỊ TRÍ:
   - Các địa điểm có tọa độ gần nhau (khoảng cách ngắn) BẮT BUỘC phải được xếp chung vào một cụm buổi (Sáng/Chiều/Tối) hoặc trong cùng một ngày.
   - Sắp xếp các điểm tham quan theo lộ trình di chuyển tuyến tính xuôi đường, tuyệt đối KHÔNG di chuyển zig-zag hay quay lại các khu vực xa nhau nhiều lần trong ngày.
2. BẮT BUỘC 100% CÁC ĐỊA ĐIỂM THAM QUAN, ĂN UỐNG, KHÁCH SẠN TRONG LỊCH TRÌNH CHỈ ĐƯỢC CHỌN TỪ DANH SÁCH DỮ LIỆU CÓ SẴN BÊN DƯỚI. TUYỆT ĐỐI KHÔNG TỰ BỊA ĐỊA ĐIỂM KHÔNG CÓ TRONG HỆ THỐNG.
3. Với mỗi địa điểm trong lịch trình, BẮT BUỘC ghi đúng tên và điền `location_id` chính xác là ID tương ứng từ danh sách DB bên dưới.
4. Phân bổ thời gian thực tế, hợp lý. Nếu chọn lưu trú/khách sạn, ưu tiên chọn khách sạn có tọa độ gần cụm địa điểm tham quan.
5. Giữ mô tả các slot hoạt động ngắn gọn, súc tích (1-2 câu ngắn), mỗi ngày 3-5 slot chính.
6. Trả về ĐÚNG 1 ĐỐI TƯỢNG JSON thuần túy (không kèm markdown):
{
  \"title\": \"Tiêu đề chuyến đi (ví dụ: HÀNH TRÌNH KHÁM PHÁ HÀ NAM 2 NGÀY 1 ĐÊM)\",
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
          \"location\": \"Tên địa điểm chính xác từ DB\",
          \"location_id\": 1,
          \"type\": \"visit\" (chọn 1 trong: visit, food, transport, rest, photo),
          \"tip\": \"Mẹo nhỏ cho hoạt động này\"
        }
      ]
    }
  ],
  \"tips\": [
    \"Lưu ý quan trọng 1\",
    \"Lưu ý quan trọng 2\"
  ]
}

{$locationInfo}";

        $userPrompt = "Hồ sơ mong muốn của người dùng:\n{$answersText}\nHãy sinh lịch trình dạng JSON chi tiết.";

        $rawResponse = $this->callAI($systemPrompt, $userPrompt, 1000, 0.7);

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
