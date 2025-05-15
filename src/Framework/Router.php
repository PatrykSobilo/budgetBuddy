<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];

    public function add(string $httpMethod, string $sitePath, array $displayingController)
    {
        $sitePath = $this->normalizePath($sitePath);

        $this->routes[] = [
            'path' => $sitePath,
            'method' => strtoupper($httpMethod),
            'controller' => $displayingController,
        ];
    }

    private function normalizePath($sitePath): string
    {
        $sitePath = trim($sitePath, '/');
        $sitePath = "/{$sitePath}/";
        $sitePath = preg_replace('#[/]{2,}#', '/', $sitePath);

        return $sitePath;
    }

    public function dispatch(string $sitePath, string $httpMethod)
    {
        $sitePath = $this->normalizePath($sitePath);
        $httpMethod = strtoupper($httpMethod);

        foreach ($this->routes as $route) {
            if (!preg_match("#^{$route['path']}$#", $sitePath) || $route['method'] !== $httpMethod) {
                continue;
            }


            [$controllerClass, $method] = $route['controller'];
            $controller = new $controllerClass();
            $controller->$method();
        }
    }
}
