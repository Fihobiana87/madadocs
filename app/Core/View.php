<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $viewPath = dirname(__DIR__) . "/views/{$view}.php";

        if (!is_file($viewPath)) {
            throw new \RuntimeException("Vue introuvable: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . "/views/layouts/{$layout}.php";
        if (!is_file($layoutPath)) {
            echo $content;
            return;
        }

        require $layoutPath;
    }

    public static function partial(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . "/views/{$view}.php";
        if (!is_file($viewPath)) {
            return;
        }
        extract($data, EXTR_SKIP);
        require $viewPath;
    }
}
