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
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

function signed_token(int|string $value): string
{
    $key = config('app.key', '');
    return substr(hash_hmac('sha256', (string) $value, $key), 0, 32);
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

/**
 * Remplace les jetons {{champ}} d'un gabarit par des valeurs utilisateur,
 * en échappant le HTML et en préservant les retours à la ligne des textarea.
 */
function fill_template(string $template, array $data): string
{
    return preg_replace_callback('/\{\{(\w+)\}\}/', function ($matches) use ($data) {
        $value = $data[$matches[1]] ?? '';
        return nl2br(e((string) $value));
    }, $template);
}
