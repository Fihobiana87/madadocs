<?php

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            flash('error', 'Veuillez vous connecter pour continuer.');
            redirect('/connexion');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->render('errors/403', [], 'main');
            exit;
        }
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::verifyRequest()) {
            http_response_code(419);
            flash('error', 'Votre session a expiré, veuillez réessayer.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
}
