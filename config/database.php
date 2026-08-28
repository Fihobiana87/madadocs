<?php

return [
    'driver' => env('DB_DRIVER', 'mysql'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'name' => env('DB_NAME', 'madadocs'),
    'user' => env('DB_USER', 'root'),
    'pass' => env('DB_PASS', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    // Utilisé uniquement quand driver=sqlite (confort de développement local)
    'sqlite_path' => env('DB_SQLITE_PATH', dirname(__DIR__) . '/storage/madadocs.sqlite'),
];
