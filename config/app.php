<?php

return [
    'name' => env('APP_NAME', 'MadaDocs'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', 'false') === 'true',
    'url' => rtrim(env('APP_URL', ''), '/'),
    'session_lifetime' => (int) env('SESSION_LIFETIME', 7200),
];
