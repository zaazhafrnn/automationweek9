<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function add($method, $path, $controllerAction)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'action' => $controllerAction
        ];
    }

    public function get($path, $action) { $this->add('GET', $path, $action); }
    public function post($path, $action) { $this->add('POST', $path, $action); }

    public function dispatch($requestUri, $requestMethod)
    {
        $path = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($requestMethod) && $route['path'] === $path) {
                
                [$controllerName, $method] = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $method)) {
                        return $controller->$method();
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
