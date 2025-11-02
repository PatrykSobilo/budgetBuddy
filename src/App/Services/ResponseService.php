<?php

declare(strict_types=1);

namespace App\Services;

/**
 * ResponseService
 * 
 * Centralized HTTP response handling service.
 * Manages redirects, JSON responses, and HTTP headers.
 */
class ResponseService
{
    /**
     * Redirect to a URL
     * 
     * @param string $url Target URL
     * @param int $statusCode HTTP status code (default: 302)
     * @return never
     */
    public function redirect(string $url, int $statusCode = 302): never
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect with a flash message
     * 
     * @param string $url Target URL
     * @param string $message Flash message content
     * @param string $type Message type: 'success', 'error', 'warning', 'info'
     * @param int $statusCode HTTP status code (default: 302)
     * @return never
     */
    public function redirectWithFlash(string $url, string $message, string $type = 'success', int $statusCode = 302): never
    {
        $_SESSION["flash_{$type}"] = $message;
        $this->redirect($url, $statusCode);
    }

    /**
     * Return a JSON response
     * 
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default: 200)
     * @return never
     */
    public function json(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Return a success JSON response
     * 
     * @param array $data Data payload
     * @param string|null $message Optional success message
     * @return never
     */
    public function jsonSuccess(array $data = [], ?string $message = null): never
    {
        $response = ['success' => true];
        if ($message) {
            $response['message'] = $message;
        }
        if (!empty($data)) {
            $response['data'] = $data;
        }
        $this->json($response, 200);
    }

    /**
     * Return an error JSON response
     * 
     * @param string $message Error message
     * @param array $errors Optional validation errors
     * @param int $statusCode HTTP status code (default: 400)
     * @return never
     */
    public function jsonError(string $message, array $errors = [], int $statusCode = 400): never
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        $this->json($response, $statusCode);
    }

    /**
     * Set custom HTTP headers
     * 
     * @param array $headers Associative array of headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }
        return $this;
    }

    /**
     * Set HTTP status code
     * 
     * @param int $code Status code
     * @return self
     */
    public function setStatusCode(int $code): self
    {
        http_response_code($code);
        return $this;
    }
}
