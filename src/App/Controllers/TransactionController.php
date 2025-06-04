<?php

declare(strict_types=1);

namespace App\Controllers;


use Framework\Exceptions\ValidationException;
use Framework\TemplateEngine;
use App\Services\{ValidatorService, TransactionService};

class TransactionController
{
  public function __construct(
    private TemplateEngine $view,
    private ValidatorService $validatorService,
    private TransactionService $transactionService
  ) {}

  public function expensesView()
  {
    $expenses = [];
    if (isset($_SESSION['user'])) {
      $all = $this->transactionService->getUserTransactions();
      $expenses = array_filter($all, fn($t) => $t['type'] === 'Expense');
    }
    echo $this->view->render("expenses.php", [
      'expenses' => $expenses
    ]);
  }

  public function incomesView()
  {
    $incomes = [];
    if (isset($_SESSION['user'])) {
      $all = $this->transactionService->getUserTransactions();
      $incomes = array_filter($all, fn($t) => $t['type'] === 'Income');
    }
    echo $this->view->render("incomes.php", [
      'incomes' => $incomes
    ]);
  }

  public function dashboardsView()
  {
    echo $this->view->render("dashboards.php");
  }

  public function createView()
  {
    echo $this->view->render("transactions/create.php");
  }

  public function addTransaction()
  {
    $result = $this->transactionService->addTransaction($_POST, $this->validatorService);
    echo $this->view->render('mainPage.php', $result);
  }
}
