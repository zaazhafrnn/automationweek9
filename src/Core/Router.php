<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, string $controllerAction): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'action' => $controllerAction
        ];
    }

    public function get(string $path, string $action): void
    {
        $this->add('GET', $path, $action);
    }
    public function post(string $path, string $action): void
    {
        $this->add('POST', $path, $action);
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($requestMethod) && $route['path'] === $path) {

                [$controllerName, $method] = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $method)) {
                        $controller->$method();
                        return;
                    } else {
                        die("Method $method not found in controller $controllerName");
                    }
                } else {
                    die("Controller $controllerClass not found");
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
