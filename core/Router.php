<?php

class Router
{
    private array $routes = [];

    public function get($path, $callback, $middleware = [])
    {
        $this->routes['GET'][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    public function post($path, $callback, $middleware = [])
    {
        $this->routes['POST'][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    public function dispatch($uri, $method)
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = $uri ?: '/';

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        $route = $this->routes[$method][$uri];

        // Запуск middleware
        foreach ($route['middleware'] as $middleware) {

            // защита от path traversal
            $middleware = preg_replace('/[^a-zA-Z0-9]/', '', $middleware);

            $file = __DIR__ . '/../app/Middleware/' . ucfirst($middleware) . 'Middleware.php';

            if (!file_exists($file)) {
                throw new Exception("Middleware not found");
            }

            require_once $file;

            $middlewareClass = ucfirst($middleware) . 'Middleware';

            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware class not found");
            }

            (new $middlewareClass())->handle();
        }

        $callback = $route['callback'];

        if (is_array($callback)) {

            if (!class_exists($callback[0])) {
                throw new Exception("Controller not found");
            }

            $controller = new $callback[0];
            $method = $callback[1];

            if (!method_exists($controller, $method)) {
                throw new Exception("Method not found");
            }

            $controller->$method();

        } else {
            call_user_func($callback);
        }
    }
}