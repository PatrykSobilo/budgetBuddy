<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class CsrfTokenMiddleware implements MiddlewareInterface
{
  public function __construct(private TemplateEngine $view)
  {
  }

  public function process(callable $next)
  {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        error_log('CSRF token wygenerowany: ' . $_SESSION['token']);
    } else {
        error_log('CSRF token istniejący: ' . $_SESSION['token']);
    }

    $this->view->addGlobal('csrfToken', $_SESSION['token']);

    $next();
  }
}