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
      "SELECT 'Expense' AS type, expense_comment AS description, amount, date_of_expense AS date
       FROM expenses
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $incomes = $this->db->query(
      "SELECT 'Income' AS type, income_comment AS description, amount, date_of_income AS date
       FROM incomes
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $all = array_merge($expenses, $incomes);
    usort($all, function($a, $b) {
      return strtotime($b['date']) <=> strtotime($a['date']);
    });
    if ($limit !== null && $limit > 0) {
      return array_slice($all, 0, $limit);
    }
    return $all;
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

  public function update(array $formData, int $id)
  {
    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "UPDATE transactions
      SET description = :description,
        amount = :amount,
        date = :date
      WHERE id = :id
      AND user_id = :user_id",
      [
        'description' => $formData['description'],
        'amount' => $formData['amount'],
        'date' => $formattedDate,
        'id' => $id,
        'user_id' => $_SESSION['user']
      ]
    );
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
}
