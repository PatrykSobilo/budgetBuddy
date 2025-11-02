<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class FlashMiddleware implements MiddlewareInterface
{
  public function __construct(private TemplateEngine $view)
  {
  }

  public function process(callable $next)
  {
    $this->view->addGlobal('errors', $_SESSION['errors'] ?? []);
    unset($_SESSION['errors']);

    $this->view->addGlobal('oldFormData', $_SESSION['oldFormData'] ?? []);
    unset($_SESSION['oldFormData']);
    
    // Add flash messages
    $this->view->addGlobal('flash_success', $_SESSION['flash_success'] ?? '');
    unset($_SESSION['flash_success']);
    
    $this->view->addGlobal('flash_error', $_SESSION['flash_error'] ?? '');
    unset($_SESSION['flash_error']);
    
    // Add settings section for Settings page
    $this->view->addGlobal('settings_section', $_SESSION['settings_section'] ?? 'profile');
    unset($_SESSION['settings_section']);

    $next();
  }
}