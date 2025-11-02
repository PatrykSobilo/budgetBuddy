<?php

declare(strict_types=1);

namespace App\Services;

/**
 * AuthService
 * 
 * Centralized authentication and authorization service.
 * Provides security layer for checking user authentication status.
 */
class AuthService
{
    public function __construct(
        private SessionService $session
    ) {}

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function check(): bool
    {
        return $this->session->isAuthenticated();
    }

    /**
     * Check if user is a guest (not authenticated)
     * 
     * @return bool
     */
    public function isGuest(): bool
    {
        return !$this->check();
    }

    /**
     * Get current user ID
     * 
     * @return int|null User ID or null if not authenticated
     */
    public function getUserId(): ?int
    {
        return $this->session->getUserId();
    }

    /**
     * Require authentication - redirect to login if not authenticated
     * 
     * @param string $redirectUrl URL to redirect to if not authenticated (default: /login)
     * @return void
     */
    public function requireAuth(string $redirectUrl = '/login'): void
    {
        if ($this->isGuest()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Require guest - redirect if already authenticated
     * 
     * @param string $redirectUrl URL to redirect to if authenticated (default: /mainPage)
     * @return void
     */
    public function requireGuest(string $redirectUrl = '/mainPage'): void
    {
        if ($this->check()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Get user data from session
     * 
     * @param string $key Session key (default: 'user' returns full user data)
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function user(string $key = 'user', mixed $default = null): mixed
    {
        return $this->session->get($key, $default);
    }

    /**
     * Set user ID in session (login)
     * 
     * @param int $userId User ID
     * @return void
     */
    public function login(int $userId): void
    {
        $this->session->set('user', $userId);
        $this->session->regenerate();
    }

    /**
     * Remove user from session (logout)
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->session->flush();
        $this->session->regenerate();
    }

    /**
     * Check if user owns a resource
     * 
     * @param int $resourceUserId User ID associated with the resource
     * @return bool
     */
    public function owns(int $resourceUserId): bool
    {
        $currentUserId = $this->getUserId();
        return $currentUserId !== null && $currentUserId === $resourceUserId;
    }
}
