<?php

use App\Core\Env;
use App\Core\Router;
use App\Core\Session;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/helpers/functions.php';

Env::load(dirname(__DIR__) . '/.env');

error_reporting(E_ALL);
ini_set('display_errors', config('app.debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/storage/logs/php-errors.log');

set_exception_handler(function (\Throwable $e): void {
    \App\Core\Logger::error($e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (config('app.debug')) {
        echo '<pre>' . e($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
        return;
    }
    require dirname(__DIR__) . '/app/views/errors/500.php';
});

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; font-src 'self' data:");

Session::start();

require dirname(__DIR__) . '/routes.php';

/** @var Router $router */
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
