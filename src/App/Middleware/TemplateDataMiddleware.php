<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;
use App\Services\SessionService;

class TemplateDataMiddleware implements MiddlewareInterface
{
  public function __construct(
    private TemplateEngine $view,
    private SessionService $session
  ) {
  }

  public function process(callable $next)
  {
    $this->view->addGlobal('title', 'Expense Tracking App');
    
    // Add CSRF token
    $this->view->addGlobal('csrfToken', $this->session->get('token'));
    
    // Add user data (categories, payment methods)
    $userData = $this->session->get('user');
    if ($userData) {
      $this->view->addGlobal('expenseCategories', $userData['expenseCategories'] ?? []);
      $this->view->addGlobal('incomeCategories', $userData['incomeCategories'] ?? []);
      $this->view->addGlobal('paymentMethods', $userData['paymentMethods'] ?? []);
    }

    $next();
  }
}