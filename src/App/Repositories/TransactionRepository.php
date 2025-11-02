<?php

declare(strict_types=1);

namespace App\Repositories;

use Framework\Database;

class TransactionRepository
{
  public function __construct(private Database $db) {}

  public function createExpense(array $data): void
  {
    $this->db->query(
      "INSERT INTO expenses(user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
      VALUES(:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)",
      $data
    );
  }

  public function createIncome(array $data): void
  {
    $this->db->query(
      "INSERT INTO incomes(user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
      VALUES(:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)",
      $data
    );
  }

  public function getAllExpenses(int $userId): array
  {
    return $this->db->query(
      "SELECT 'Expense' AS type, id, expense_comment AS description, amount, date_of_expense AS date, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id
       FROM expenses
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function getAllIncomes(int $userId): array
  {
    return $this->db->query(
      "SELECT 'Income' AS type, id, income_comment AS description, amount, date_of_income AS date, income_category_assigned_to_user_id
       FROM incomes
       WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function updateExpense(int $expenseId, int $userId, array $data): void
  {
    $this->db->query(
      "UPDATE expenses SET expense_category_assigned_to_user_id = :cat, payment_method_assigned_to_user_id = :pay, amount = :amount, date_of_expense = :date, expense_comment = :desc WHERE id = :id AND user_id = :user_id",
      [
        'cat' => $data['expensesCategory'],
        'pay' => $data['paymentMethods'],
        'amount' => $data['amount'],
        'date' => $data['date'],
        'desc' => $data['description'],
        'id' => $expenseId,
        'user_id' => $userId
      ]
    );
  }

  public function updateIncome(int $incomeId, int $userId, array $data): void
  {
    $this->db->query(
      "UPDATE incomes SET income_category_assigned_to_user_id = :cat, amount = :amount, date_of_income = :date, income_comment = :desc WHERE id = :id AND user_id = :user_id",
      [
        'cat' => $data['incomesCategory'],
        'amount' => $data['amount'],
        'date' => $data['date'],
        'desc' => $data['description'],
        'id' => $incomeId,
        'user_id' => $userId
      ]
    );
  }

  public function deleteExpense(int $expenseId, int $userId): void
  {
    $this->db->query(
      "DELETE FROM expenses WHERE id = :id AND user_id = :user_id",
      [
        'id' => $expenseId,
        'user_id' => $userId
      ]
    );
  }

  public function deleteIncome(int $incomeId, int $userId): void
  {
    $this->db->query(
      "DELETE FROM incomes WHERE id = :id AND user_id = :user_id",
      [
        'id' => $incomeId,
        'user_id' => $userId
      ]
    );
  }

  public function getExpensesByDateRange(int $userId, string $startDate, string $endDate): array
  {
    return $this->db->query(
      "SELECT * FROM expenses WHERE user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date ORDER BY date_of_expense DESC",
      [
        'user_id' => $userId,
        'start_date' => $startDate,
        'end_date' => $endDate
      ]
    )->findAll();
  }

  public function getIncomesByDateRange(int $userId, string $startDate, string $endDate): array
  {
    return $this->db->query(
      "SELECT * FROM incomes WHERE user_id = :user_id AND date_of_income BETWEEN :start_date AND :end_date ORDER BY date_of_income DESC",
      [
        'user_id' => $userId,
        'start_date' => $startDate,
        'end_date' => $endDate
      ]
    )->findAll();
  }
}
