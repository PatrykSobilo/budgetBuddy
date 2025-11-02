<?php

declare(strict_types=1);

namespace App\Controllers;


use Framework\Exceptions\ValidationException;
use Framework\TemplateEngine;
use App\Services\{
  ValidatorService, 
  TransactionService, 
  DatePeriodService, 
  ViewHelperService, 
  TransactionSearchService
};

class TransactionController
{
  public function __construct(
    private TemplateEngine $view,
    private ValidatorService $validatorService,
    private TransactionService $transactionService,
    private DatePeriodService $datePeriodService,
    private ViewHelperService $viewHelper,
    private TransactionSearchService $searchService
  ) {}

  public function expensesView()
  {
    $expenses = [];
    $chartData = ['labels' => [], 'data' => []];
    
    if (isset($_SESSION['user'])) {
      $all = $this->transactionService->getUserTransactions();
      $expenses = array_filter($all, fn($t) => $t['type'] === 'Expense');
      
      // Filtrowanie po okresie - używamy DatePeriodService
      if (isset($_GET['period']) && $_GET['period'] !== 'all') {
        $expenses = $this->datePeriodService->filterByPeriod(
          $expenses, 
          $_GET['period'], 
          $_GET['start_date'] ?? null, 
          $_GET['end_date'] ?? null
        );
      }
      
      // Obsługa wyszukiwania - używamy TransactionSearchService
      if (isset($_GET['s']) && trim($_GET['s']) !== '') {
        $expenses = $this->searchService->filterTransactions($expenses, $_GET['s'], 'expense');
      }
      
      // Przygotowanie danych do wykresów - używamy ViewHelperService
      $chartData = $this->viewHelper->prepareChartDataByCategory($expenses, 'expense');
    }
    
    echo $this->view->render("expenses.php", [
      'expenses' => $expenses,
      'chartData' => $chartData
    ]);
  }

  public function incomesView()
  {
    $incomes = [];
    $chartData = ['labels' => [], 'data' => []];
    
    if (isset($_SESSION['user'])) {
      $all = $this->transactionService->getUserTransactions();
      $incomes = array_filter($all, fn($t) => $t['type'] === 'Income');
      
      // Filtrowanie po okresie - używamy DatePeriodService
      if (isset($_GET['period']) && $_GET['period'] !== 'all') {
        $incomes = $this->datePeriodService->filterByPeriod(
          $incomes, 
          $_GET['period'], 
          $_GET['start_date'] ?? null, 
          $_GET['end_date'] ?? null
        );
      }
      
      // Obsługa wyszukiwania - używamy TransactionSearchService
      if (isset($_GET['s']) && trim($_GET['s']) !== '') {
        $incomes = $this->searchService->filterTransactions($incomes, $_GET['s'], 'income');
      }
      
      // Przygotowanie danych do wykresów - używamy ViewHelperService
      $chartData = $this->viewHelper->prepareChartDataByCategory($incomes, 'income');
    }
    
    echo $this->view->render("incomes.php", [
      'incomes' => $incomes,
      'chartData' => $chartData
    ]);
  }

  public function dashboardsView()
  {
    $startDate = null;
    $endDate = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $_SESSION['token'] = bin2hex(random_bytes(32));
      
      $period = $_POST['period'] ?? 'all';
      
      if ($period === 'custom') {
        $startDate = $_POST['startingDate'] ?? null;
        $endDate = $_POST['endingDate'] ?? null;
      } else {
        // Calculate dates based on period - używamy DatePeriodService
        $dates = $this->datePeriodService->calculatePeriodDates($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
      }
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

  /**
   * API endpoint - sprawdza stan wykorzystania limitu kategorii
   * GET /api/check-category-limit?category_id=X&amount=Y&expense_id=Z
   */
  public function checkCategoryLimit()
  {
    header('Content-Type: application/json');
    
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;
    $expenseId = isset($_GET['expense_id']) ? (int)$_GET['expense_id'] : null;
    $userId = $_SESSION['user'] ?? 0;

    if (!$categoryId || !$userId) {
      echo json_encode(['error' => 'Invalid parameters']);
      return;
    }

    // Pobierz limit kategorii
    $limit = $this->transactionService->getCategoryLimit($categoryId);
    
    // Jeśli kategoria nie ma limitu, zwróć OK
    if ($limit === null) {
      echo json_encode([
        'hasLimit' => false,
        'status' => 'ok'
      ]);
      return;
    }

    // Oblicz sumę wydatków w kategorii (bez edytowanego wydatku)
    $currentTotal = $this->transactionService->getCategoryMonthlyTotal($userId, $categoryId, $expenseId);
    
    // Dodaj nową kwotę
    $newTotal = $currentTotal + $amount;
    
    // Oblicz procent wykorzystania
    $percentage = $limit > 0 ? ($newTotal / $limit) * 100 : 0;
    
    // Określ status
    $status = 'ok';
    $level = 'success';
    
    if ($percentage >= 100) {
      $status = 'exceeded';
      $level = 'danger';
    } elseif ($percentage >= 80) {
      $status = 'warning';
      $level = 'warning';
    }

    echo json_encode([
      'hasLimit' => true,
      'limit' => $limit,
      'currentTotal' => $currentTotal,
      'newTotal' => $newTotal,
      'percentage' => round($percentage, 1),
      'status' => $status,
      'level' => $level,
      'message' => $this->getLimitMessage($status, $newTotal, $limit, $percentage)
    ]);
  }

  private function getLimitMessage(string $status, float $newTotal, float $limit, float $percentage): string
  {
    if ($status === 'exceeded') {
      $over = $newTotal - $limit;
      return "⚠️ Warning! This expense will exceed your category limit by " . number_format($over, 2) . " PLN (". round($percentage, 1) . "% of limit).";
    } elseif ($status === 'warning') {
      return "⚠️ Caution! You're approaching your category limit (" . round($percentage, 1) . "% used).";
    }
    return "✓ Within budget (" . round($percentage, 1) . "% of limit used).";
  }
}

