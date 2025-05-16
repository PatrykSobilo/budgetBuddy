<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\{
  HomeController,
  AboutController,
  AuthController,
  TransactionController,
  ReceiptController,
  ErrorController
};
use App\Middleware\{AuthRequiredMiddleware, GuestOnlyMiddleware};

function registerRoutes(App $app)
{
  $app->get('/', [HomeController::class, 'home']);
  $app->get('/about', [AboutController::class, 'about']);
  $app->get('/register', [AuthController::class, 'registerView']);
  $app->post('/register', [AuthController::class, 'register']);
  $app->get('/expenses', [TransactionController::class, 'expensesView']);
  $app->get('/incomes', [TransactionController::class, 'incomesView']);
  $app->get('/dashboards', [TransactionController::class, 'dashboardsView']);
}
