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
      
      // Filtrowanie po okresie
      if (isset($_GET['period']) && $_GET['period'] !== 'all') {
        $expenses = $this->filterByPeriod($expenses, $_GET['period'], $_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
      }
      
      // Obsługa wyszukiwania po GET
      if (isset($_GET['s']) && trim($_GET['s']) !== '') {
        $search = mb_strtolower(trim($_GET['s']));
        $expenses = array_filter($expenses, function($exp) use ($search) {
          // Wyszukiwanie w description
          if (mb_strpos(mb_strtolower($exp['description'] ?? ''), $search) !== false) {
            return true;
          }
          // Wyszukiwanie w category
          if (!empty($_SESSION['expenseCategories'])) {
            foreach ($_SESSION['expenseCategories'] as $cat) {
              if ($cat['id'] == ($exp['expense_category_assigned_to_user_id'] ?? null)) {
                if (mb_strpos(mb_strtolower($cat['name']), $search) !== false) {
                  return true;
                }
              }
            }
          }
          // Wyszukiwanie w payment method
          if (!empty($_SESSION['paymentMethods'])) {
            foreach ($_SESSION['paymentMethods'] as $method) {
              if ($method['id'] == ($exp['payment_method_assigned_to_user_id'] ?? null)) {
                if (mb_strpos(mb_strtolower($method['name']), $search) !== false) {
                  return true;
                }
              }
            }
          }
          // Wyszukiwanie w amount
          if (mb_strpos((string)$exp['amount'], $search) !== false) {
            return true;
          }
          // Wyszukiwanie w date
          if (mb_strpos($exp['date'], $search) !== false) {
            return true;
          }
          return false;
        });
      }
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
      
      // Filtrowanie po okresie
      if (isset($_GET['period']) && $_GET['period'] !== 'all') {
        $incomes = $this->filterByPeriod($incomes, $_GET['period'], $_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
      }
      
      // Obsługa wyszukiwania po GET
      if (isset($_GET['s']) && trim($_GET['s']) !== '') {
        $search = mb_strtolower(trim($_GET['s']));
        $incomes = array_filter($incomes, function($inc) use ($search) {
          // Wyszukiwanie w description
          if (mb_strpos(mb_strtolower($inc['description'] ?? ''), $search) !== false) {
            return true;
          }
          // Wyszukiwanie w category
          if (!empty($_SESSION['incomeCategories'])) {
            foreach ($_SESSION['incomeCategories'] as $cat) {
              if ($cat['id'] == ($inc['income_category_assigned_to_user_id'] ?? null)) {
                if (mb_strpos(mb_strtolower($cat['name']), $search) !== false) {
                  return true;
                }
              }
            }
          }
          // Wyszukiwanie w amount
          if (mb_strpos((string)$inc['amount'], $search) !== false) {
            return true;
          }
          // Wyszukiwanie w date
          if (mb_strpos($inc['date'], $search) !== false) {
            return true;
          }
          return false;
        });
      }
    }
    echo $this->view->render("incomes.php", [
      'incomes' => $incomes
    ]);
  }

  private function filterByPeriod(array $transactions, string $period, ?string $startDate, ?string $endDate): array
  {
    $now = new \DateTime();
    $filtered = [];
    
    foreach ($transactions as $transaction) {
      $transDate = new \DateTime($transaction['date']);
      $include = false;
      
      switch ($period) {
        case 'current_month':
          $include = $transDate->format('Y-m') === $now->format('Y-m');
          break;
        case 'last_month':
          $lastMonth = (clone $now)->modify('-1 month');
          $include = $transDate->format('Y-m') === $lastMonth->format('Y-m');
          break;
        case 'last_30_days':
          $thirtyDaysAgo = (clone $now)->modify('-30 days');
          $include = $transDate >= $thirtyDaysAgo;
          break;
        case 'last_90_days':
          $ninetyDaysAgo = (clone $now)->modify('-90 days');
          $include = $transDate >= $ninetyDaysAgo;
          break;
        case 'current_year':
          $include = $transDate->format('Y') === $now->format('Y');
          break;
        case 'custom':
          if ($startDate && $endDate) {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $include = $transDate >= $start && $transDate <= $end;
          }
          break;
      }
      
      if ($include) {
        $filtered[] = $transaction;
      }
    }
    
    return $filtered;
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
