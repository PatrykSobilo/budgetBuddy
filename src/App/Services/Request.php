<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Request
 * 
 * HTTP request wrapper providing clean access to request data.
 * Encapsulates $_GET, $_POST, $_SERVER, and other request superglobals.
 */
class Request
{
    /**
     * Get a POST parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get a GET parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get all POST data
     * 
     * @return array
     */
    public function postAll(): array
    {
        return $_POST;
    }

    /**
     * Get all GET data
     * 
     * @return array
     */
    public function getAll(): array
    {
        return $_GET;
    }

    /**
     * Get specific POST keys only
     * 
     * @param array $keys Keys to retrieve
     * @return array
     */
    public function postOnly(array $keys): array
    {
        return array_intersect_key($_POST, array_flip($keys));
    }

    /**
     * Get specific GET keys only
     * 
     * @param array $keys Keys to retrieve
     * @return array
     */
    public function getOnly(array $keys): array
    {
        return array_intersect_key($_GET, array_flip($keys));
    }

    /**
     * Check if POST key exists
     * 
     * @param string $key Parameter key
     * @return bool
     */
    public function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    /**
     * Check if GET key exists
     * 
     * @param string $key Parameter key
     * @return bool
     */
    public function hasGet(string $key): bool
    {
        return isset($_GET[$key]);
    }

    /**
     * Get request method
     * 
     * @return string HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if request is POST
     * 
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * Check if request is GET
     * 
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    /**
     * Get request URI
     * 
     * @return string
     */
    public function uri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Get server parameter
     * 
     * @param string $key Server key
     * @param mixed $default Default value
     * @return mixed
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Get all request data (POST + GET merged)
     * 
     * @return array
     */
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Get input from POST or GET (POST has priority)
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post($key, $this->get($key, $default));
    }

    /**
     * Check if key exists in POST or GET
     * 
     * @param string $key Parameter key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->hasPost($key) || $this->hasGet($key);
    }
}
