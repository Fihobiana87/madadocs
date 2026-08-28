<?php

namespace App\Core;

/**
 * Passerelle IA remplaçable. Aujourd'hui : Groq (API compatible OpenAI, gratuite).
 * Pour changer de fournisseur : implémenter une méthode call*() et l'associer
 * dans complete() selon config('ai.provider'). Le reste de l'app ne connaît
 * que complete() et isAvailable().
 */
class AiClient
{
    public static function isAvailable(): bool
    {
        return config('ai.provider') !== 'none' && config('ai.api_key') !== '';
    }

    public static function isRateLimited(): bool
    {
        $limit = config('ai.rate_limit');
        $key = 'ai_requests';
        $now = time();

        $log = array_filter(
            $_SESSION[$key] ?? [],
            fn ($ts) => $ts > $now - $limit['window_seconds']
        );

        if (count($log) >= $limit['max_requests']) {
            $_SESSION[$key] = $log;
            return true;
        }

        $log[] = $now;
        $_SESSION[$key] = $log;
        return false;
    }

    /**
     * @return array{ok: bool, text?: string, error?: string}
     */
    public static function complete(string $systemPrompt, string $userPrompt): array
    {
        if (!self::isAvailable()) {
            return ['ok' => false, 'error' => 'unavailable'];
        }

        if (self::isRateLimited()) {
            return ['ok' => false, 'error' => 'rate_limited'];
        }

        return match (config('ai.provider')) {
            'groq' => self::callGroq($systemPrompt, $userPrompt),
            default => ['ok' => false, 'error' => 'unavailable'],
        };
    }

    private static function callGroq(string $systemPrompt, string $userPrompt): array
    {
        $payload = json_encode([
            'model' => config('ai.model'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.6,
            'max_tokens' => 900,
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init(config('ai.endpoints')['groq']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => config('ai.timeout'),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . config('ai.api_key'),
            ],
        ]);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $curlError = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($errno || $response === false) {
            Logger::error('AI Groq: erreur réseau (' . $errno . ') ' . $curlError);
            return ['ok' => false, 'error' => 'network'];
        }

        $decoded = json_decode($response, true);

        if ($status === 429) {
            return ['ok' => false, 'error' => 'quota'];
        }

        if ($status !== 200 || !isset($decoded['choices'][0]['message']['content'])) {
            Logger::error('AI Groq: réponse invalide (HTTP ' . $status . ')');
            return ['ok' => false, 'error' => 'invalid_response'];
        }

        return ['ok' => true, 'text' => trim($decoded['choices'][0]['message']['content'])];
    }
}
