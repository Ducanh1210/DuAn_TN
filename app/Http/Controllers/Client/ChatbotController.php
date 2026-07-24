<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ChatbotService;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

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

        // Optional: you can store this in ChatLog model here if you want to keep logs in DB
        
        $response = $this->chatbotService->sendMessage($message, $history);

        return response()->json([
            'success' => true,
            'reply' => $response
        ]);
    }
}
