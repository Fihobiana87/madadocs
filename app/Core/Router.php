<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, array $action): void
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', trim($path, '/'));
        $this->routes[] = [
            'method' => $method,
            'pattern' => '#^' . $pattern . '$#',
            'action' => $action,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                [$controllerClass, $methodName] = $route['action'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        http_response_code(404);
        (new \App\Controllers\ErrorController())->notFound();
    }
}
