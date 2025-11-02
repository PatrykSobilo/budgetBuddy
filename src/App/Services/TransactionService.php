<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use App\Repositories\TransactionRepository;

class TransactionService
{
  public function __construct(
    private Database $db,
    private BudgetCalculatorService $budgetCalculator,
    private TransactionRepository $transactionRepository
  ) {}

  public function create(array $formData, int $userId)
  {
    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "INSERT INTO transactions(user_id, description, amount, date)
      VALUES(:user_id, :description, :amount, :date)",
      [
        'user_id' => $userId,
        'description' => $formData['description'],
        'amount' => $formData['amount'],
        'date_of_expense' => $formattedDate,
        'expense_comment' => $formData['description']
      ]
    );
  }

  public function createIncome(array $formData, int $userId)
  {
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->transactionRepository->createIncome([
      'user_id' => $userId,
      'income_category_assigned_to_user_id' => $formData['incomesCategory'],
      'amount' => $formData['amount'],
      'date_of_income' => $formattedDate,
      'income_comment' => $formData['description']
    ]);
  }

  public function createExpense(array $formData, int $userId)
  {
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->transactionRepository->createExpense([
      'user_id' => $userId,
      'expense_category_assigned_to_user_id' => $formData['expensesCategory'],
      'payment_method_assigned_to_user_id' => $formData['paymentMethods'],
      'amount' => $formData['amount'],
      'date_of_expense' => $formattedDate,
      'expense_comment' => $formData['description']
    ]);
  }

  public function getUserTransactions(int $userId, int $limit = null, string $searchTerm = '')
  {
    $expenses = $this->transactionRepository->getAllExpenses($userId);
    $incomes = $this->transactionRepository->getAllIncomes($userId);
    
    $allTransactions = array_merge($expenses, $incomes);
    usort($allTransactions, function($a, $b) {
      return strtotime($b['date']) <=> strtotime($a['date']);
    });
    
    if ($limit !== null && $limit > 0) {
      return array_slice($allTransactions, 0, $limit);
    }
    return $allTransactions;
  }

  public function getUserTransaction(string $id, int $userId)
  {
    return $this->db->query(
      "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
      FROM transactions 
      WHERE id = :id AND user_id = :user_id",
      [
        'id' => $id,
        'user_id' => $userId
      ]
    )->find();
  }

  public function delete(int $id, int $userId)
  {
    $this->db->query(
      "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
      [
        'id' => $id,
        'user_id' => $userId
      ]
    );
  }

  public function deleteExpenseById($expenseId, int $userId)
  {
    $this->transactionRepository->deleteExpense((int)$expenseId, $userId);
  }

  public function deleteIncomeById($incomeId, int $userId)
  {
    $this->transactionRepository->deleteIncome((int)$incomeId, $userId);
  }

  public function addTransaction(array $formData, ValidatorService $validatorService, int $userId, string $csrfToken)
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
      $newCsrfToken = bin2hex(random_bytes(32));
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $newCsrfToken
      ];
    }
    if (isset($formData['expensesCategory'])) {
      $this->createExpense($formData, $userId);
    } elseif (isset($formData['incomesCategory'])) {
      $this->createIncome($formData, $userId);
    }
    $newCsrfToken = bin2hex(random_bytes(32));
    return [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $newCsrfToken
    ];
  }
  
  public function calculateTransactions(int $userId, $startDate = null, $endDate = null): array
  {
    $all = $this->getUserTransactions($userId);
    return $this->budgetCalculator->calculateTransactions($all, $startDate, $endDate);
  }

  public function updateExpense(array $formData, $validatorService, int $userId, string $currentCsrfToken)
  {
    $openModal = null;
    if (!isset($formData['expense_id'])) {
      return [
        'oldFormData' => $formData,
        'errors' => ['global' => ['Brak ID wydatku do edycji']],
        'openModal' => $openModal,
        'csrfToken' => $currentCsrfToken
      ];
    }
    try {
      $validatorService->validateTransaction($formData);
    } catch (\Framework\Exceptions\ValidationException $e) {
      $csrfToken = bin2hex(random_bytes(32));
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ];
    }
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->transactionRepository->updateExpense(
      (int)$formData['expense_id'],
      $userId,
      [
        'expensesCategory' => $formData['expensesCategory'],
        'paymentMethods' => $formData['paymentMethods'],
        'amount' => $formData['amount'],
        'date' => $formattedDate,
        'description' => $formData['description']
      ]
    );
    $csrfToken = bin2hex(random_bytes(32));
    return [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $csrfToken
    ];
  }

  public function updateIncome(array $formData, $validatorService, int $userId, string $currentCsrfToken)
  {
    $openModal = null;
    if (!isset($formData['income_id'])) {
      return [
        'oldFormData' => $formData,
        'errors' => ['global' => ['Brak ID przychodu do edycji']],
        'openModal' => $openModal,
        'csrfToken' => $currentCsrfToken
      ];
    }
    try {
      $validatorService->validateTransaction($formData);
    } catch (\Framework\Exceptions\ValidationException $e) {
      $csrfToken = bin2hex(random_bytes(32));
      return [
        'oldFormData' => $formData,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ];
    }
    $formattedDate = "{$formData['date']} 00:00:00";
    $this->transactionRepository->updateIncome(
      (int)$formData['income_id'],
      $userId,
      [
        'incomesCategory' => $formData['incomesCategory'],
        'amount' => $formData['amount'],
        'date' => $formattedDate,
        'description' => $formData['description']
      ]
    );
    $csrfToken = bin2hex(random_bytes(32));
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
    return $this->budgetCalculator->getCategoryMonthlyTotal($userId, $categoryId, $excludeExpenseId);
  }

  /**
   * Pobiera limit kategorii wydatków
   * @param int $categoryId ID kategorii
   * @return float|null Limit kategorii lub null jeśli nie ustawiony
   */
  public function getCategoryLimit(int $categoryId): ?float
  {
    return $this->budgetCalculator->getCategoryLimit($categoryId);
  }

  /**
   * Pobiera podsumowanie budżetu dla wszystkich kategorii z limitami
   * @param int $userId ID użytkownika
   * @return array Dane o budżecie: total_limit, total_spent, categories_exceeded, categories_warning
   */
  public function getBudgetSummary(int $userId): array
  {
    return $this->budgetCalculator->getBudgetSummary($userId);
  }

  /**
   * Pobiera szczegółowe dane o kategoriach z limitami dla progress bars
   * @param int $userId ID użytkownika
   * @return array Lista kategorii z danymi o wydatkach i limitach
   */
  public function getCategoriesWithLimits(int $userId): array
  {
    return $this->budgetCalculator->getCategoriesWithLimits($userId);
  }

  /**
   * Pobiera dane timeline dla kategorii (wydatki dzień po dniu w miesiącu)
   * @param int $userId ID użytkownika
   * @param int $categoryId ID kategorii
   * @return array Dane dla wykresu timeline
   */
  public function getCategoryTimeline(int $userId, int $categoryId): array
  {
    return $this->budgetCalculator->getCategoryTimeline($userId, $categoryId);
  }
}

