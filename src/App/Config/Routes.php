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
  ErrorController,
  PlannerController,
  SettingsController
};
use App\Middleware\{AuthRequiredMiddleware, GuestOnlyMiddleware};

function registerRoutes(App $app)
{
  $app->get('/', [HomeController::class, 'home']);

  $app->get('/register', [AuthController::class, 'registerView'])->add(GuestOnlyMiddleware::class);
  $app->post('/register', [AuthController::class, 'register'])->add(GuestOnlyMiddleware::class);

  $app->get('/login', [AuthController::class, 'loginView'])->add(GuestOnlyMiddleware::class);
  $app->post('/login', [AuthController::class, 'login'])->add(GuestOnlyMiddleware::class);
  $app->get('/logout', [AuthController::class, 'logout'])->add(AuthRequiredMiddleware::class);

  $app->get('/about', [AboutController::class, 'about']);

  $app->get('/mainPage', [HomeController::class, 'mainPageView']);
  $app->post('/mainPage', [TransactionController::class, 'addTransaction'])->add(AuthRequiredMiddleware::class);

  $app->get('/expenses', [TransactionController::class, 'expensesView']);
  $app->post('/expenses', [TransactionController::class, 'addTransaction'])->add(AuthRequiredMiddleware::class);

  $app->get('/incomes', [TransactionController::class, 'incomesView']);

  $app->get('/dashboards', [TransactionController::class, 'dashboardsView']);
  $app->post('/dashboards', [TransactionController::class, 'dashboardsView']);

  $app->get('/planner', [PlannerController::class, 'planner']);

  $app->get('/settings', [SettingsController::class, 'settings']);
  $app->post('/settings', [SettingsController::class, 'settings'])->add(AuthRequiredMiddleware::class);
}
