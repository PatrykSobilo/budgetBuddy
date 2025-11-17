<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
    $validMethods = ['POST', 'PATCH', 'DELETE'];

    if (!in_array($requestMethod, $validMethods)) {
      $next();
      return;
    }

    // Check if this is an API endpoint
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $isApiEndpoint = strpos($requestUri, '/ai/') !== false;

    if ($_SESSION['token'] !== $_POST['token']) {
      if ($isApiEndpoint) {
        // For API endpoints, return JSON error instead of redirect
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode([
          'success' => false,
          'message' => 'Invalid CSRF token'
        ]);
        exit;
      }
      redirectTo('/');
    }

    // Regenerate token for next request (instead of unsetting)
    $_SESSION['token'] = bin2hex(random_bytes(32));

    $next();
  }
}