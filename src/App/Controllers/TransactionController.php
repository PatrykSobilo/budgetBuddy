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
    $startDate = $_POST['startingDate'] ?? null;
    $endDate = $_POST['endingDate'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['token'] ?? '';
    echo $this->view->render("dashboards.php", [
      'transactionService' => $this->transactionService,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'csrfToken' => $csrfToken
    ]);
  }

  public function createView()
  {
    echo $this->view->render("transactions/create.php");
  }

  public function addTransaction()
  {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }

    $result = $this->transactionService->addTransaction($_POST, $this->validatorService);
    if (empty($result['errors'])) {
      if (isset($_POST['expensesCategory'])) {
        header('Location: /expenses');
        exit;
      }
      if (isset($_POST['incomesCategory'])) {
        header('Location: /incomes');
        exit;
      }
      header('Location: /mainPage');
      exit;
    } else {
      echo $this->view->render('mainPage.php', $result);
    }
  }

  public function editExpense()
  {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }
    $result = $this->transactionService->updateExpense($_POST, $this->validatorService);
    if (empty($result['errors'])) {
      header('Location: /expenses');
      exit;
    } else {
      $all = $this->transactionService->getUserTransactions();
      $expenses = array_filter($all, fn($t) => $t['type'] === 'Expense');
      echo $this->view->render('expenses.php', array_merge($result, ['expenses' => $expenses]));
    }
  }

  public function editIncome()
  {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }
    $result = $this->transactionService->updateIncome($_POST, $this->validatorService);
    if (empty($result['errors'])) {
      header('Location: /incomes');
      exit;
    } else {
      $all = $this->transactionService->getUserTransactions();
      $incomes = array_filter($all, fn($t) => $t['type'] === 'Income');
      echo $this->view->render('incomes.php', array_merge($result, ['incomes' => $incomes]));
    }
  }

  public function deleteExpense()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_id'])) {
      $expenseId = $_POST['expense_id'];
      $this->transactionService->deleteExpenseById($expenseId);
      header('Location: /expenses');
      exit;
    }
    header('Location: /expenses');
    exit;
  }

  public function deleteIncome()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['income_id'])) {
      $incomeId = $_POST['income_id'];
      $this->transactionService->deleteIncomeById($incomeId);
      header('Location: /incomes');
      exit;
    }
    header('Location: /incomes');
    exit;
  }

  public function deleteExpenseFromMainPage()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_id'])) {
      $expenseId = $_POST['expense_id'];
      $this->transactionService->deleteExpenseById($expenseId);
      header('Location: /mainPage');
      exit;
    }
    header('Location: /mainPage');
    exit;
  }

  public function deleteIncomeFromMainPage()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['income_id'])) {
      $incomeId = $_POST['income_id'];
      $this->transactionService->deleteIncomeById($incomeId);
      header('Location: /mainPage');
      exit;
    }
    header('Location: /mainPage');
    exit;
  }
}
