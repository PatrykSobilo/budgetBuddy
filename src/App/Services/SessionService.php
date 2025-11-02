<?php

declare(strict_types=1);

namespace App\Services;

/**
 * SessionService
 * 
 * Wrapper for $_SESSION superglobal.
 * Provides type-safe session management with a clean API.
 */
class SessionService
{
    /**
     * Set a session value
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
     * Get a session value
     * 
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists
     * 
     * @param string $key Session key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key
     * 
     * @param string $key Session key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Clear all session data
     * 
     * @return void
     */
    public function flush(): void
    {
        $_SESSION = [];
    }

    /**
     * Regenerate session ID (security)
     * 
     * @param bool $deleteOldSession Delete old session file
     * @return void
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Get all session data
     * 
     * @return array
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * Pull a value (get and remove)
     * 
     * @param string $key Session key
     * @param mixed $default Default value
     * @return mixed
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    /**
     * Set multiple session values at once
     * 
     * @param array $data Associative array of key-value pairs
     * @return void
     */
    public function setMultiple(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get user ID from session (convenience method)
     * 
     * @return int|null
     */
    public function getUserId(): ?int
    {
        $userId = $this->get('user');
        return $userId !== null ? (int)$userId : null;
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->has('user');
    }
}
