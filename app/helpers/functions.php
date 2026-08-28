<?php

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function config(string $key, mixed $default = null): mixed
{
    static $cache = [];

    [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

    if (!isset($cache[$file])) {
        $path = dirname(__DIR__, 2) . "/config/{$file}.php";
        $cache[$file] = is_file($path) ? require $path : [];
    }

    if ($item === null) {
        return $cache[$file];
    }

    return $cache[$file][$item] ?? $default;
}

function base_url(string $path = ''): string
{
    $configured = config('app.url', '');
    if ($configured !== '') {
        return $configured . '/' . ltrim($path, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return "{$scheme}://{$host}/" . ltrim($path, '/');
}

function asset(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function redirect(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(string $key, ?string $message = null): mixed
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function csrf_field(): string
{
    $token = \App\Core\Csrf::token();
    return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

function view_exists(string $view): bool
{
    return is_file(dirname(__DIR__) . "/views/{$view}.php");
}
