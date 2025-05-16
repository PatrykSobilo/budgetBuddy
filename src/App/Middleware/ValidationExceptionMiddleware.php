<?php

declare(strict_types=1);

namespace App\Middleware;


use function Framework\redirectTo;
use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;


require_once __DIR__ . '/../functions.php';


class ValidationExceptionMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    try {
      $next();
    } catch (ValidationException $e) {
      $oldFormData = $_POST;

      $excludedFields = ['password', 'confirmPassword'];
      $formattedFormData = array_diff_key(
        $oldFormData,
        array_flip($excludedFields)
      );

      $_SESSION['errors'] = $e->errors;
      $_SESSION['oldFormData'] = $formattedFormData;

      $referer = $_SERVER['HTTP_REFERER'];
      redirectTo($referer);
    }
  }
}