<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path)
    {
        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),

        ];
    }
}
