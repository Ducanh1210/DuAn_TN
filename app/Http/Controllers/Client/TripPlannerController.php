<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TripPlannerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TripPlannerController extends Controller
{
    protected $tripPlannerService;

    public function __construct(TripPlannerService $tripPlannerService)
    {
        $this->tripPlannerService = $tripPlannerService;
    }

    /**
     * AI sinh câu hỏi tiếp theo
     */
    public function nextQuestion(Request $request): JsonResponse
    {
        $answers = $request->input('answers', []);
        $step = $request->input('step', 1);

        $result = $this->tripPlannerService->generateNextQuestion($answers, $step);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * AI tạo lịch trình dựa trên các lựa chọn
     */
    public function generate(Request $request): JsonResponse
    {
        $answers = $request->input('answers', []);

        if (empty($answers)) {
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng hoàn thành các lựa chọn trước khi tạo lịch trình.'
            ], 422);
        }

        $result = $this->tripPlannerService->generateItinerary($answers);

        return response()->json($result);
    }
}
