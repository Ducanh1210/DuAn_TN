<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ChatbotService;

/**
 * Controller trợ lý ảo (chatbot) phía người dùng: nhận tin nhắn từ giao diện chat,
 * kiểm tra dữ liệu và chuyển cho ChatbotService xử lý bằng AI.
 */
class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /** Nhận tin nhắn người dùng (kèm lịch sử hội thoại) và trả về câu trả lời của AI. */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string'
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);

        $response = $this->chatbotService->sendMessage($message, $history);

        return response()->json([
            'success' => true,
            'reply' => $response
        ]);
    }
}
