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
  TransactionSearchService,
  ResponseService,
  SessionService,
  Request,
  AuthService
};

class TransactionController
{
  public function __construct(
    private TemplateEngine $view,
    private ValidatorService $validatorService,
    private TransactionService $transactionService,
    private DatePeriodService $datePeriodService,
    private ViewHelperService $viewHelper,
    private TransactionSearchService $searchService,
    private ResponseService $response,
    private SessionService $session,
    private Request $request,
    private AuthService $auth
  ) {}

  public function expensesView()
  {
    $expenses = [];
    $chartData = ['labels' => [], 'data' => []];
    
    if ($this->auth->check()) {
      $userId = $this->auth->getUserId();
      $searchTerm = $this->request->get('s', '');
      $all = $this->transactionService->getUserTransactions($userId, null, $searchTerm);
      $expenses = array_filter($all, fn($t) => $t['type'] === 'Expense');
      
      // Filtrowanie po okresie - używamy DatePeriodService
      if ($this->request->get('period') && $this->request->get('period') !== 'all') {
        $expenses = $this->datePeriodService->filterByPeriod(
          $expenses, 
          $this->request->get('period'), 
          $this->request->get('start_date'), 
          $this->request->get('end_date')
        );
      }
      
      // Obsługa wyszukiwania - używamy TransactionSearchService
      $searchQuery = $this->request->get('s', '');
      if (trim($searchQuery) !== '') {
        $expenses = $this->searchService->filterTransactions($expenses, $searchQuery, 'expense');
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
    
    if ($this->auth->check()) {
      $userId = $this->auth->getUserId();
      $searchTerm = $this->request->get('s', '');
      $all = $this->transactionService->getUserTransactions($userId, null, $searchTerm);
      $incomes = array_filter($all, fn($t) => $t['type'] === 'Income');
      
      // Filtrowanie po okresie - używamy DatePeriodService
      if ($this->request->get('period') && $this->request->get('period') !== 'all') {
        $incomes = $this->datePeriodService->filterByPeriod(
          $incomes, 
          $this->request->get('period'), 
          $this->request->get('start_date'), 
          $this->request->get('end_date')
        );
      }
      
      // Obsługa wyszukiwania - używamy TransactionSearchService
      $searchQuery = $this->request->get('s', '');
      if (trim($searchQuery) !== '') {
        $incomes = $this->searchService->filterTransactions($incomes, $searchQuery, 'income');
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
    
    if ($this->request->isPost()) {
      $this->session->set('token', bin2hex(random_bytes(32)));
      
      $period = $this->request->post('period', 'all');
      
      if ($period === 'custom') {
        $startDate = $this->request->post('startingDate');
        $endDate = $this->request->post('endingDate');
      } else {
        // Calculate dates based on period - używamy DatePeriodService
        $dates = $this->datePeriodService->calculatePeriodDates($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
      }
    }
    
    $csrfToken = $this->session->get('token', '');
    $userId = $this->auth->getUserId();
    echo $this->view->render("dashboards.php", [
      'transactionService' => $this->transactionService,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'csrfToken' => $csrfToken,
      'userId' => $userId
    ]);
  }

  public function createView()
  {
    echo $this->view->render("transactions/create.php");
  }

  public function addTransaction()
  {
    $this->auth->requireAuth();

    $userId = $this->auth->getUserId();
    $csrfToken = $this->session->get('token', '');
    $result = $this->transactionService->addTransaction($this->request->postAll(), $this->validatorService, $userId, $csrfToken);
    
    if (empty($result['errors'])) {
      if ($this->request->hasPost('expensesCategory')) {
        $this->response->redirect('/expenses');
      }
      if ($this->request->hasPost('incomesCategory')) {
        $this->response->redirect('/incomes');
      }
      $this->response->redirect('/mainPage');
    } else {
      echo $this->view->render('mainPage.php', $result);
    }
  }

  public function editExpense()
  {
    $this->auth->requireAuth();
    
    $userId = $this->auth->getUserId();
    $currentCsrfToken = $this->session->get('token', '');
    $result = $this->transactionService->updateExpense($this->request->postAll(), $this->validatorService, $userId, $currentCsrfToken);
    
    if (empty($result['errors'])) {
      $this->response->redirect('/expenses');
    } else {
      $searchTerm = $this->request->get('s', '');
      $all = $this->transactionService->getUserTransactions($userId, null, $searchTerm);
      $expenses = array_filter($all, fn($t) => $t['type'] === 'Expense');
      echo $this->view->render('expenses.php', array_merge($result, ['expenses' => $expenses]));
    }
  }

  public function editIncome()
  {
    $this->auth->requireAuth();
    
    $userId = $this->auth->getUserId();
    $currentCsrfToken = $this->session->get('token', '');
    $result = $this->transactionService->updateIncome($this->request->postAll(), $this->validatorService, $userId, $currentCsrfToken);
    
    if (empty($result['errors'])) {
      $this->response->redirect('/incomes');
    } else {
      $searchTerm = $this->request->get('s', '');
      $all = $this->transactionService->getUserTransactions($userId, null, $searchTerm);
      $incomes = array_filter($all, fn($t) => $t['type'] === 'Income');
      echo $this->view->render('incomes.php', array_merge($result, ['incomes' => $incomes]));
    }
  }

  public function deleteExpense()
  {
    if ($this->request->isPost() && $this->request->hasPost('expense_id')) {
      $expenseId = $this->request->post('expense_id');
      $userId = $this->auth->getUserId();
      $this->transactionService->deleteExpenseById($expenseId, $userId);
    }
    $this->response->redirect('/expenses');
  }

  public function deleteIncome()
  {
    if ($this->request->isPost() && $this->request->hasPost('income_id')) {
      $incomeId = $this->request->post('income_id');
      $userId = $this->auth->getUserId();
      $this->transactionService->deleteIncomeById($incomeId, $userId);
    }
    $this->response->redirect('/incomes');
  }

  public function deleteExpenseFromMainPage()
  {
    if ($this->request->isPost() && $this->request->hasPost('expense_id')) {
      $expenseId = $this->request->post('expense_id');
      $userId = $this->auth->getUserId();
      $this->transactionService->deleteExpenseById($expenseId, $userId);
    }
    $this->response->redirect('/mainPage');
  }

  public function deleteIncomeFromMainPage()
  {
    if ($this->request->isPost() && $this->request->hasPost('income_id')) {
      $incomeId = $this->request->post('income_id');
      $userId = $this->auth->getUserId();
      $this->transactionService->deleteIncomeById($incomeId, $userId);
    }
    $this->response->redirect('/mainPage');
  }

  /**
   * API endpoint - sprawdza stan wykorzystania limitu kategorii
   * GET /api/check-category-limit?category_id=X&amount=Y&expense_id=Z
   */
  public function checkCategoryLimit()
  {
    $categoryId = (int)$this->request->get('category_id', 0);
    $amount = (float)$this->request->get('amount', 0);
    $expenseId = $this->request->get('expense_id') ? (int)$this->request->get('expense_id') : null;
    $userId = $this->auth->getUserId() ?? 0;

    if (!$categoryId || !$userId) {
      $this->response->jsonError('Invalid parameters', [], 400);
    }

    // Pobierz limit kategorii
    $limit = $this->transactionService->getCategoryLimit($categoryId);
    
    // Jeśli kategoria nie ma limitu, zwróć OK
    if ($limit === null) {
      $this->response->json([
        'hasLimit' => false,
        'status' => 'ok'
      ]);
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

    $this->response->json([
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

