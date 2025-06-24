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
      "SELECT 'Income' AS type, income_comment AS description, amount, date_of_income AS date, income_category_assigned_to_user_id
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
            $inRange = true;
            if ($startDate && $date < $startDate) $inRange = false;
            if ($endDate && $date > $endDate) $inRange = false;
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
}
