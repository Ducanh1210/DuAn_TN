<?php

namespace App\Services\Concerns;

/**
 * Gỡ code fence và chuẩn hóa JSON do mô hình sinh ra trước khi decode.
 */
trait ParsesAiJson
{
    protected function decodeAiJson(string $rawContent): ?array
    {
        $clean = trim($rawContent);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*/', $clean, $matches)) {
            $candidate = $matches[0];
            $lastBrace = strrpos($candidate, '}');
            if ($lastBrace !== false) {
                $candidate = substr($candidate, 0, $lastBrace + 1);
            }

            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            $repaired = preg_replace('/[\x00-\x1F\x7F]/', ' ', $candidate);
            $repaired = preg_replace('/,\s*([\]\}])/', '$1', $repaired);
            $decoded = json_decode($repaired, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
