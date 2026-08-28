<?php

return [
    // Fournisseur actif : 'groq' ou 'none'. L'appli reste 100% fonctionnelle si 'none'.
    'provider' => env('AI_PROVIDER', 'none'),
    'api_key' => env('AI_API_KEY', ''),
    'model' => env('AI_MODEL', 'llama-3.1-8b-instant'),
    'timeout' => (int) env('AI_TIMEOUT', 12),
    'endpoints' => [
        'groq' => 'https://api.groq.com/openai/v1/chat/completions',
    ],
    // Anti-abus simple, compatible hébergement mutualisé (pas de Redis)
    'rate_limit' => [
        'max_requests' => 12,
        'window_seconds' => 3600,
    ],
];
