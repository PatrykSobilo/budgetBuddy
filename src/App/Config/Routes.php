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
  $app->get('/', [HomeController::class, 'home'])->add(AuthRequiredMiddleware::class);

  $app->get('/register', [AuthController::class, 'registerView'])->add(GuestOnlyMiddleware::class);
  $app->post('/register', [AuthController::class, 'register'])->add(GuestOnlyMiddleware::class);

  $app->get('/login', [AuthController::class, 'loginView'])->add(GuestOnlyMiddleware::class);
  $app->post('/login', [AuthController::class, 'login'])->add(GuestOnlyMiddleware::class);
  $app->get('/logout', [AuthController::class, 'logout'])->add(AuthRequiredMiddleware::class);

  $app->get('/about', [AboutController::class, 'about']);

  $app->get('/mainPage', [HomeController::class, 'mainPageView']);

  $app->post('/mainPage', [TransactionController::class, 'addIncome'])->add(AuthRequiredMiddleware::class);

  $app->get('/expenses', [TransactionController::class, 'expensesView']);
  $app->get('/incomes', [TransactionController::class, 'incomesView']);
  $app->get('/dashboards', [TransactionController::class, 'dashboardsView']);
}
