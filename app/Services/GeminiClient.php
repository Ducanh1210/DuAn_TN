<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gọi Gemini REST API trực tiếp (không qua OpenRouter).
 * Hỗ trợ nhiều API key (xoay vòng) và mô hình dự phòng.
 */
class GeminiClient
{
    protected string $baseUrl;

    protected string $primaryModel;

    /** @var array<int, string> */
    protected array $apiKeys;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->primaryModel = (string) config('services.gemini.model', 'gemini-3.6-flash');
        $this->apiKeys = $this->readApiKeys();
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKeys);
    }

    /**
     * Gửi hội thoại kiểu chat (system/user/assistant) và nhận text phản hồi.
     *
     * @param  array<int, array{role:string, content:string}>  $messages
     * @param  array{json?:bool}  $options
     */
    public function generate(array $messages, float $temperature = 0.4, int $maxTokens = 2048, int $timeout = 70, array $options = []): ?string
    {
        if (!$this->isConfigured()) {
            Log::error('GeminiClient: Chưa cấu hình GEMINI_API_KEY.');
            return null;
        }

        $payload = $this->buildPayload($messages, $temperature, $maxTokens, $options);
        $defaultModels = [
            $this->primaryModel,
            'gemini-3.6-flash',
            'gemini-3.5-flash',
            'gemini-flash-latest',
            'gemini-3.5-flash-lite',
        ];
        // Trip planner / tác vụ nặng: ít model hơn để tránh vượt max_execution_time PHP.
        if (!empty($options['compact_models'])) {
            $defaultModels = [
                $this->primaryModel,
                'gemini-flash-latest',
                'gemini-3.5-flash-lite',
            ];
        }
        if (!empty($options['models']) && is_array($options['models'])) {
            $defaultModels = $options['models'];
        }
        $modelsToTry = array_values(array_unique(array_filter($defaultModels)));

        foreach ($this->apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                $text = $this->requestOnce($apiKey, $modelName, $payload, $timeout, $keyIndex);
                if ($text !== null && $text !== '') {
                    return $text;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{role:string, content:string}>  $messages
     * @param  array{json?:bool}  $options
     * @return array<string, mixed>
     */
    protected function buildPayload(array $messages, float $temperature, int $maxTokens, array $options = []): array
    {
        $system = '';
        $contents = [];

        foreach ($messages as $msg) {
            $role = (string) ($msg['role'] ?? 'user');
            $text = (string) ($msg['content'] ?? '');
            if ($text === '') {
                continue;
            }

            if ($role === 'system') {
                $system .= ($system === '' ? '' : "\n\n") . $text;
                continue;
            }

            $geminiRole = $role === 'assistant' ? 'model' : 'user';
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $geminiRole) {
                $contents[$lastIndex]['parts'][0]['text'] .= "\n" . $text;
                continue;
            }

            $contents[] = [
                'role' => $geminiRole,
                'parts' => [['text' => $text]],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => max($maxTokens, 1024),
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
            ],
        ];

        if (!empty($options['json'])) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        if ($system !== '') {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $system]],
            ];
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function requestOnce(string $apiKey, string $modelName, array $payload, int $timeout, int $keyIndex): ?string
    {
        $url = $this->baseUrl . '/models/' . $modelName . ':generateContent';

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                $text = $this->extractText($response->json());
                if ($text !== null && $text !== '') {
                    return $text;
                }
            }

            // Một số model không nhận thinkingBudget / JSON mime → thử lại bản tối giản.
            if ($response->status() === 400) {
                $fallbackPayload = $payload;
                unset($fallbackPayload['generationConfig']['thinkingConfig'], $fallbackPayload['generationConfig']['responseMimeType']);
                $retry = Http::timeout($timeout)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => $apiKey,
                    ])
                    ->post($url, $fallbackPayload);

                if ($retry->successful()) {
                    $text = $this->extractText($retry->json());
                    if ($text !== null && $text !== '') {
                        return $text;
                    }
                }

                Log::warning('GeminiClient Key #' . ($keyIndex + 1) . " model {$modelName} failed: " . $retry->body());
                return null;
            }

            Log::warning('GeminiClient Key #' . ($keyIndex + 1) . " model {$modelName} failed: " . $response->body());
        } catch (\Exception $e) {
            Log::warning('GeminiClient Key #' . ($keyIndex + 1) . " model {$modelName} exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    protected function extractText(?array $data): ?string
    {
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        if (!is_array($parts) || $parts === []) {
            return null;
        }

        $chunks = [];
        foreach ($parts as $part) {
            if (!empty($part['text'])) {
                $chunks[] = (string) $part['text'];
            }
        }

        $text = trim(implode('', $chunks));
        return $text === '' ? null : $text;
    }

    /** @return array<int, string> */
    protected function readApiKeys(): array
    {
        $rawKeys = config('services.gemini.api_keys') ?: config('services.gemini.api_key');
        if (!$rawKeys) {
            return [];
        }

        $keys = array_map('trim', explode(',', (string) $rawKeys));

        return array_values(array_filter($keys));
    }
}
