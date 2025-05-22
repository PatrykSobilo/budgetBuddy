<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
  public function __construct(private Database $db)
  {
  }

  public function createExpense(array $formData)
  {
    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "INSERT INTO expenses(user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
      VALUES(:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)",
      [
        'user_id' => $_SESSION['user'],
        'expense_category_assigned_to_user_id' => $_SESSION['user'],
        'payment_method_assigned_to_user_id' => $_SESSION['user'],
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
      "INSERT INTO transactions(user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
      VALUES(:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)",
      [
        'user_id' => $_SESSION['user'],
        'income_category_assigned_to_user_id'=> $_SESSION['user'],
        'amount' => $formData['amount'],
        'date_of_income' => $formattedDate,
        'income_comment' => $formData['description']
      ]
    );
  }

  public function getUserTransactions(int $length, int $offset)
  {
    $searchTerm = addcslashes($_GET['s'] ?? '', '%_');
    $params = [
      'user_id' => $_SESSION['user'],
      'description' => "%{$searchTerm}%"
    ];

    $transactions = $this->db->query(
      "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
      FROM transactions 
      WHERE user_id = :user_id
      AND description LIKE :description
      LIMIT {$length} OFFSET {$offset}",
      $params
    )->findAll();

    $transactions = array_map(function (array $transaction) {
      $transaction['receipts'] = $this->db->query(
        "SELECT * FROM receipts WHERE transaction_id = :transaction_id",
        ['transaction_id' => $transaction['id']]
      )->findAll();

      return $transaction;
    }, $transactions);

    $transactionCount = $this->db->query(
      "SELECT COUNT(*)
      FROM transactions 
      WHERE user_id = :user_id
      AND description LIKE :description",
      $params
    )->count();

    return [$transactions, $transactionCount];
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