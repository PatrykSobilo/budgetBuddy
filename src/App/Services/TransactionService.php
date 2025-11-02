<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
  public function __construct(private Database $db) {}

  public function create(array $formData)
  {
    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "INSERT INTO transactions(user_id, description, amount, date)
      VALUES(:user_id, :description, :amount, :date)",
      [
        'user_id' => $_SESSION['user'],
        'description' => $formData['description'],
        'amount' => $formData['amount'],
        'date_of_expense' => $formattedDate,
        'expense_comment' => $formData['description']
      ]
    );
  }

  public function createIncome(array $formData)
  {
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->db->query(
      "INSERT INTO incomes(user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
      VALUES(:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)",
      [
        'user_id' => $_SESSION['user'],
        'income_category_assigned_to_user_id' => $formData['incomesCategory'],
        'amount' => $formData['amount'],
        'date_of_income' => $formattedDate,
        'income_comment' => $formData['description']
      ]
    );
  }

  public function createExpense(array $formData)
  {
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->db->query(
      "INSERT INTO expenses(user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
      VALUES(:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)",
      [
        'user_id' => $_SESSION['user'],
        'expense_category_assigned_to_user_id' => $formData['expensesCategory'],
        'payment_method_assigned_to_user_id' => $formData['paymentMethods'],
        'amount' => $formData['amount'],
        'date_of_expense' => $formattedDate,
        'expense_comment' => $formData['description']
      ]
    );
  }

  public function getUserTransactions(int $limit = null)
  {
    $searchTerm = $_GET['s'] ?? '';

    $userId = $_SESSION['user'];
    $expenses = $this->db->query(
      "SELECT 'Expense' AS type, id, expense_comment AS description, amount, date_of_expense AS date, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id
       FROM expenses
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $incomes = $this->db->query(
      "SELECT 'Income' AS type, id, income_comment AS description, amount, date_of_income AS date, income_category_assigned_to_user_id
       FROM incomes
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $allTransactions = array_merge($expenses, $incomes);
    usort($allTransactions, function($a, $b) {
      return strtotime($b['date']) <=> strtotime($a['date']);
    });
    if ($limit !== null && $limit > 0) {
      return array_slice($allTransactions, 0, $limit);
    }
    return $allTransactions;
  }

  public function getUserTransaction(string $id)
  {
    return $this->db->query(
      "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
      FROM transactions 
      WHERE id = :id AND user_id = :user_id",
      [
        'id' => $id,
        'user_id' => $_SESSION['user']
      ]
    )->find();
  }

  public function delete(int $id)
  {
    $this->db->query(
      "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
      [
        'id' => $id,
        'user_id' => $_SESSION['user']
      ]
    );
  }

  public function deleteExpenseById($expenseId)
  {
    $this->db->query(
      "DELETE FROM expenses WHERE id = :id AND user_id = :user_id",
      [
        'id' => $expenseId,
        'user_id' => $_SESSION['user']
      ]
    );
  }

  public function deleteIncomeById($incomeId)
  {
    $this->db->query(
      "DELETE FROM incomes WHERE id = :id AND user_id = :user_id",
      [
        'id' => $incomeId,
        'user_id' => $_SESSION['user']
      ]
    );
  }

  public function addTransaction(array $formData, ValidatorService $validatorService)
  {
    $openModal = null;
    if (isset($formData['expensesCategory'])) {
      $openModal = 'customAddExpenseModal';
    } elseif (isset($formData['incomesCategory'])) {
      $openModal = 'customAddIncomeModal';
    }
    try {
      $validatorService->validateTransaction($formData);
    } catch (\Framework\Exceptions\ValidationException $e) {
      $csrfToken = bin2hex(random_bytes(32));
      $_SESSION['token'] = $csrfToken;
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ];
    }
    if (isset($formData['expensesCategory'])) {
      $this->createExpense($formData);
    } elseif (isset($formData['incomesCategory'])) {
      $this->createIncome($formData);
    }
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['token'] = $csrfToken;
    return [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $csrfToken
    ];
  }
  
  public function calculateTransactions($startDate = null, $endDate = null): array
  {
    $expenses = 0;
    $incomes = 0;
    $userId = $_SESSION['user'] ?? null;
    if ($userId) {
        $all = $this->getUserTransactions();
        foreach ($all as $t) {
            $date = $t['date'];
            // Extract only date part (YYYY-MM-DD) for comparison
            $dateOnly = substr($date, 0, 10);
            
            $inRange = true;
            if ($startDate && $dateOnly < $startDate) $inRange = false;
            if ($endDate && $dateOnly > $endDate) $inRange = false;
            
            if ($inRange) {
                if ($t['type'] === 'Expense') $expenses += $t['amount'];
                if ($t['type'] === 'Income') $incomes += $t['amount'];
            }
        }
    }
    return [
        'expenses' => $expenses,
        'incomes' => $incomes,
        'balance' => $incomes - $expenses
    ];
  }

  public function updateExpense(array $formData, $validatorService)
  {
    $openModal = null;
    if (!isset($formData['expense_id'])) {
      return [
        'oldFormData' => $formData,
        'errors' => ['global' => ['Brak ID wydatku do edycji']],
        'openModal' => $openModal,
        'csrfToken' => $_SESSION['token'] ?? ''
      ];
    }
    try {
      $validatorService->validateTransaction($formData);
    } catch (\Framework\Exceptions\ValidationException $e) {
      $csrfToken = bin2hex(random_bytes(32));
      $_SESSION['token'] = $csrfToken;
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ];
    }
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->db->query(
      "UPDATE expenses SET expense_category_assigned_to_user_id = :cat, payment_method_assigned_to_user_id = :pay, amount = :amount, date_of_expense = :date, expense_comment = :desc WHERE id = :id AND user_id = :user_id",
      [
        'cat' => $formData['expensesCategory'],
        'pay' => $formData['paymentMethods'],
        'amount' => $formData['amount'],
        'date' => $formattedDate,
        'desc' => $formData['description'],
        'id' => $formData['expense_id'],
        'user_id' => $_SESSION['user']
      ]
    );
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['token'] = $csrfToken;
    return [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $csrfToken
    ];
  }

  public function updateIncome(array $formData, $validatorService)
  {
    $openModal = null;
    if (!isset($formData['income_id'])) {
      return [
        'oldFormData' => $formData,
        'errors' => ['global' => ['Brak ID przychodu do edycji']],
        'openModal' => $openModal,
        'csrfToken' => $_SESSION['token'] ?? ''
      ];
    }
    try {
      $validatorService->validateTransaction($formData);
    } catch (\Framework\Exceptions\ValidationException $e) {
      $csrfToken = bin2hex(random_bytes(32));
      $_SESSION['token'] = $csrfToken;
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ];
    }
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->db->query(
      "UPDATE incomes SET income_category_assigned_to_user_id = :cat, amount = :amount, date_of_income = :date, income_comment = :desc WHERE id = :id AND user_id = :user_id",
      [
        'cat' => $formData['incomesCategory'],
        'amount' => $formData['amount'],
        'date' => $formattedDate,
        'desc' => $formData['description'],
        'id' => $formData['income_id'],
        'user_id' => $_SESSION['user']
      ]
    );
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['token'] = $csrfToken;
    return [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $csrfToken
    ];
  }

  /**
   * Oblicza sumę wydatków w danej kategorii za bieżący miesiąc
   * @param int $userId ID użytkownika
   * @param int $categoryId ID kategorii wydatków
   * @param int|null $excludeExpenseId ID wydatku do wykluczenia (przy edycji)
   * @return float Suma wydatków w kategorii
   */
  public function getCategoryMonthlyTotal(int $userId, int $categoryId, ?int $excludeExpenseId = null): float
  {
    $currentMonth = date('Y-m-01 00:00:00');
    $nextMonth = date('Y-m-01 00:00:00', strtotime('+1 month'));

    $query = "SELECT COALESCE(SUM(amount), 0) as total 
              FROM expenses 
              WHERE user_id = :user_id 
              AND expense_category_assigned_to_user_id = :category_id
              AND date_of_expense >= :start_date
              AND date_of_expense < :end_date";
    
    $params = [
      'user_id' => $userId,
      'category_id' => $categoryId,
      'start_date' => $currentMonth,
      'end_date' => $nextMonth
    ];

    if ($excludeExpenseId !== null) {
      $query .= " AND id != :exclude_id";
      $params['exclude_id'] = $excludeExpenseId;
    }

    $result = $this->db->query($query, $params)->find();
    return (float)($result['total'] ?? 0);
  }

  /**
   * Pobiera limit kategorii wydatków
   * @param int $categoryId ID kategorii
   * @return float|null Limit kategorii lub null jeśli nie ustawiony
   */
  public function getCategoryLimit(int $categoryId): ?float
  {
    $result = $this->db->query(
      "SELECT category_limit FROM expenses_category_assigned_to_users WHERE id = :id",
      ['id' => $categoryId]
    )->find();
    
    return $result && $result['category_limit'] !== null 
      ? (float)$result['category_limit'] 
      : null;
  }

  /**
   * Pobiera podsumowanie budżetu dla wszystkich kategorii z limitami
   * @param int $userId ID użytkownika
   * @return array Dane o budżecie: total_limit, total_spent, categories_exceeded, categories_warning
   */
  public function getBudgetSummary(int $userId): array
  {
    $currentMonth = date('Y-m-01 00:00:00');
    $nextMonth = date('Y-m-01 00:00:00', strtotime('+1 month'));

    // Pobierz wszystkie kategorie z limitami
    $categories = $this->db->query(
      "SELECT id, name, category_limit 
       FROM expenses_category_assigned_to_users 
       WHERE user_id = :user_id AND category_limit IS NOT NULL",
      ['user_id' => $userId]
    )->findAll();

    $totalLimit = 0;
    $totalSpent = 0;
    $categoriesExceeded = 0;
    $categoriesWarning = 0;

    foreach ($categories as $category) {
      $limit = (float)$category['category_limit'];
      $totalLimit += $limit;

      // Oblicz wydatki w kategorii
      $spent = $this->getCategoryMonthlyTotal($userId, (int)$category['id']);
      $totalSpent += $spent;

      // Sprawdź status
      if ($limit > 0) {
        $percentage = ($spent / $limit) * 100;
        if ($percentage >= 100) {
          $categoriesExceeded++;
        } elseif ($percentage >= 80) {
          $categoriesWarning++;
        }
      }
    }

    return [
      'total_limit' => $totalLimit,
      'total_spent' => $totalSpent,
      'total_percentage' => $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0,
      'categories_exceeded' => $categoriesExceeded,
      'categories_warning' => $categoriesWarning,
      'categories_count' => count($categories)
    ];
  }

  /**
   * Pobiera szczegółowe dane o kategoriach z limitami dla progress bars
   * @param int $userId ID użytkownika
   * @return array Lista kategorii z danymi o wydatkach i limitach
   */
  public function getCategoriesWithLimits(int $userId): array
  {
    // Pobierz kategorie z limitami
    $categories = $this->db->query(
      "SELECT id, name, category_limit 
       FROM expenses_category_assigned_to_users 
       WHERE user_id = :user_id AND category_limit IS NOT NULL
       ORDER BY name ASC",
      ['user_id' => $userId]
    )->findAll();

    $result = [];
    foreach ($categories as $category) {
      $categoryId = (int)$category['id'];
      $limit = (float)$category['category_limit'];
      $spent = $this->getCategoryMonthlyTotal($userId, $categoryId);
      $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;

      $result[] = [
        'id' => $categoryId,
        'name' => $category['name'],
        'limit' => $limit,
        'spent' => $spent,
        'percentage' => $percentage,
        'status' => $percentage >= 100 ? 'exceeded' : ($percentage >= 80 ? 'warning' : 'ok')
      ];
    }

    // Sortuj: najpierw exceeded, potem warning, potem według procentu malejąco
    usort($result, function($a, $b) {
      if ($a['status'] === 'exceeded' && $b['status'] !== 'exceeded') return -1;
      if ($a['status'] !== 'exceeded' && $b['status'] === 'exceeded') return 1;
      if ($a['status'] === 'warning' && $b['status'] === 'ok') return -1;
      if ($a['status'] === 'ok' && $b['status'] === 'warning') return 1;
      return $b['percentage'] <=> $a['percentage'];
    });

    return $result;
  }

  /**
   * Pobiera dane timeline dla kategorii (wydatki dzień po dniu w miesiącu)
   * @param int $userId ID użytkownika
   * @param int $categoryId ID kategorii
   * @return array Dane dla wykresu timeline
   */
  public function getCategoryTimeline(int $userId, int $categoryId): array
  {
    $currentMonth = date('Y-m-01 00:00:00');
    $nextMonth = date('Y-m-01 00:00:00', strtotime('+1 month'));

    // Pobierz wszystkie wydatki w kategorii w tym miesiącu
    $expenses = $this->db->query(
      "SELECT amount, DATE(date_of_expense) as expense_date
       FROM expenses
       WHERE user_id = :user_id
       AND expense_category_assigned_to_user_id = :category_id
       AND date_of_expense >= :start_date
       AND date_of_expense < :end_date
       ORDER BY date_of_expense ASC",
      [
        'user_id' => $userId,
        'category_id' => $categoryId,
        'start_date' => $currentMonth,
        'end_date' => $nextMonth
      ]
    )->findAll();

    // Przygotuj dane narastające
    $timeline = [];
    $cumulativeAmount = 0;
    $daysInMonth = (int)date('t'); // liczba dni w miesiącu

    // Inicjalizuj wszystkie dni miesiąca
    for ($day = 1; $day <= $daysInMonth; $day++) {
      $date = date('Y-m-') . str_pad((string)$day, 2, '0', STR_PAD_LEFT);
      $timeline[$date] = 0;
    }

    // Sumuj wydatki dzień po dniu
    foreach ($expenses as $expense) {
      $date = $expense['expense_date'];
      if (isset($timeline[$date])) {
        $timeline[$date] += (float)$expense['amount'];
      }
    }

    // Konwertuj na wartości narastające
    $cumulativeTimeline = [];
    $cumulative = 0;
    foreach ($timeline as $date => $amount) {
      $cumulative += $amount;
      $cumulativeTimeline[] = [
        'date' => $date,
        'amount' => $cumulative
      ];
    }

    return $cumulativeTimeline;
  }
}

