<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TripPlannerService;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TripPlannerController extends Controller
{
    protected $tripPlannerService;

    public function __construct(TripPlannerService $tripPlannerService)
    {
        $this->tripPlannerService = $tripPlannerService;
    }

    /**
     * AI tạo lịch trình dựa trên các lựa chọn
     */
    public function generate(Request $request): JsonResponse
    {
        // Lịch trình cần gọi Gemini lâu hơn chatbot; tránh FatalError 60s làm frontend báo "không kết nối".
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        $answers = $request->input('answers', []);
        $tripType = $request->input('trip_type');

        if (empty($answers)) {
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng hoàn thành các lựa chọn trước khi tạo lịch trình.'
            ], 422);
        }

        try {
            $result = $this->tripPlannerService->generateItinerary($answers, $tripType);
            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('TripPlanner generate failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Máy chủ xử lý quá lâu hoặc AI đang bận. Vui lòng bấm "Lên lịch mới" để thử lại.',
            ], 504);
        }
    }

    /**
     * Lưu lịch trình vào trang cá nhân (cần đăng nhập)
     */
    public function save(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng đăng nhập để lưu lịch trình.',
                'need_login' => true,
            ], 401);
        }

        $itinerary = $request->input('itinerary');
        $answers = $request->input('answers', []);

        if (!is_array($itinerary) || empty($itinerary['days'])) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu lịch trình không hợp lệ.',
            ], 422);
        }

        $record = $this->tripPlannerService->saveItinerary($user->id, $itinerary, $answers);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu lịch trình vào trang cá nhân.',
            'id' => $record->id,
            'profile_url' => route('client.profile') . '#itineraries',
        ]);
    }

    /**
     * Xem chi tiết 1 lịch trình của user
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        $record = Itinerary::where('user_id', $user->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'itinerary' => $record->payload ?: [
                'title' => $record->title,
                'summary' => $record->summary,
                'estimated_cost' => $record->estimated_cost,
                'days' => [],
            ],
            'meta' => [
                'id' => $record->id,
                'created_at' => $record->created_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Xóa lịch trình đã lưu
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $record = Itinerary::where('user_id', $user->id)->findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa lịch trình.',
        ]);
    }
}
