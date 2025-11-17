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
  $app->post('/transactions/add', [TransactionController::class, 'addTransaction'])->add(AuthRequiredMiddleware::class);
  $app->post('/mainPage/delete-expense', [TransactionController::class, 'deleteExpenseFromMainPage'])->add(AuthRequiredMiddleware::class);
  $app->post('/mainPage/delete-income', [TransactionController::class, 'deleteIncomeFromMainPage'])->add(AuthRequiredMiddleware::class);

  $app->get('/expenses', [TransactionController::class, 'expensesView']);
  $app->post('/expenses/edit', [TransactionController::class, 'editExpense'])->add(AuthRequiredMiddleware::class);
  $app->post('/expenses/delete', [TransactionController::class, 'deleteExpense'])->add(AuthRequiredMiddleware::class);

  $app->get('/incomes', [TransactionController::class, 'incomesView']);
  $app->post('/incomes/edit', [TransactionController::class, 'editIncome'])->add(AuthRequiredMiddleware::class);
  $app->post('/incomes/delete', [TransactionController::class, 'deleteIncome'])->add(AuthRequiredMiddleware::class);

  $app->get('/dashboards', [TransactionController::class, 'dashboardsView']);
  $app->post('/dashboards', [TransactionController::class, 'dashboardsView']);

  $app->get('/planner', [PlannerController::class, 'planner']);

  $app->get('/settings', [SettingsController::class, 'settings']);
  $app->post('/settings', [SettingsController::class, 'settings'])->add(AuthRequiredMiddleware::class);
  $app->post('/settings/edit-user', [SettingsController::class, 'editUser'])->add(AuthRequiredMiddleware::class);

  $app->post('/settings/delete-category', [SettingsController::class, 'deleteCategory'])->add(AuthRequiredMiddleware::class);
  $app->post('/settings/delete-account', [SettingsController::class, 'deleteAccount'])->add(AuthRequiredMiddleware::class);

  // API endpoints
  $app->get('/api/check-category-limit', [TransactionController::class, 'checkCategoryLimit'])->add(AuthRequiredMiddleware::class);

  // AI Advisor endpoints
  $app->get('/ai/insights', [HomeController::class, 'getInsights'])->add(AuthRequiredMiddleware::class);
  $app->post('/ai/insights/refresh', [HomeController::class, 'getInsights'])->add(AuthRequiredMiddleware::class);
  $app->post('/ai/chat', [HomeController::class, 'chatMessage'])->add(AuthRequiredMiddleware::class);
  $app->post('/ai/chat/clear', [HomeController::class, 'clearChat'])->add(AuthRequiredMiddleware::class);

}
