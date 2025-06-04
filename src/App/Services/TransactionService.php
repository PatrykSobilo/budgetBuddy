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

  /**
   * Pobiera wszystkie transakcje (wydatki i przychody) użytkownika, posortowane malejąco po dacie.
   * Zwraca tablicę z polami: type (Expense/Income), description, amount, date
   * Jeśli $limit > 0, zwraca tylko $limit najnowszych.
   */
  public function getUserTransactions(int $limit = null)
  {
    $userId = $_SESSION['user'];
    // Pobierz wydatki
    $expenses = $this->db->query(
      "SELECT 'Expense' AS type, expense_comment AS description, amount, date_of_expense AS date
       FROM expenses
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    // Pobierz przychody
    $incomes = $this->db->query(
      "SELECT 'Income' AS type, income_comment AS description, amount, date_of_income AS date
       FROM incomes
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    // Połącz i posortuj malejąco po dacie
    $all = array_merge($expenses, $incomes);
    usort($all, function($a, $b) {
      return strtotime($b['date']) <=> strtotime($a['date']);
    });
    // Jeśli $limit > 0, zwróć tylko $limit najnowszych
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
}
