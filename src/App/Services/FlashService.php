<?php

declare(strict_types=1);

namespace App\Services;

/**
 * FlashService
 * 
 * Manages flash messages and temporary session data.
 * Provides a clean interface for user feedback.
 */
class FlashService
{
    /**
     * Set a success flash message
     * 
     * @param string $message Success message
     * @return void
     */
    public function success(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    /**
     * Set an error flash message
     * 
     * @param string $message Error message
     * @return void
     */
    public function error(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }

    /**
     * Set a warning flash message
     * 
     * @param string $message Warning message
     * @return void
     */
    public function warning(string $message): void
    {
        $_SESSION['flash_warning'] = $message;
    }

    /**
     * Set an info flash message
     * 
     * @param string $message Info message
     * @return void
     */
    public function info(string $message): void
    {
        $_SESSION['flash_info'] = $message;
    }

    /**
     * Set arbitrary flash data
     * 
     * @param string $key Session key
     * @param mixed $value Value to store
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get and remove flash data
     * 
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    /**
     * Check if flash data exists
     * 
     * @param string $key Session key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Clear all flash messages
     * 
     * @return void
     */
    public function clear(): void
    {
        $flashKeys = ['flash_success', 'flash_error', 'flash_warning', 'flash_info'];
        foreach ($flashKeys as $key) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Get all flash messages
     * 
     * @return array Associative array of flash messages by type
     */
    public function all(): array
    {
        return [
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
            'warning' => $_SESSION['flash_warning'] ?? null,
            'info' => $_SESSION['flash_info'] ?? null
        ];
    }
}
