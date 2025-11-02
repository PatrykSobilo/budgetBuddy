<?php

declare(strict_types=1);

namespace App\Repositories;

use Framework\Database;

class CategoryRepository
{
  public function __construct(private Database $db) {}

  public function getExpenseCategories(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name, category_limit FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function getIncomeCategories(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function getPaymentMethods(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function getDefaultExpenseCategories(): array
  {
    return $this->db->query("SELECT name FROM expenses_category_default")->findAll();
  }

  public function getDefaultIncomeCategories(): array
  {
    return $this->db->query("SELECT name FROM incomes_category_default")->findAll();
  }

  public function getDefaultPaymentMethods(): array
  {
    return $this->db->query("SELECT name FROM payment_methods_default")->findAll();
  }

  public function createExpenseCategory(int $userId, string $name): void
  {
    $this->db->query(
      "INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
      ['user_id' => $userId, 'name' => $name]
    );
  }

  public function createIncomeCategory(int $userId, string $name): void
  {
    $this->db->query(
      "INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
      ['user_id' => $userId, 'name' => $name]
    );
  }

  public function createPaymentMethod(int $userId, string $name): void
  {
    $this->db->query(
      "INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
      ['user_id' => $userId, 'name' => $name]
    );
  }

  public function getCategoryLimit(int $categoryId): ?float
  {
    $result = $this->db->query(
      "SELECT category_limit FROM expenses_category_assigned_to_users WHERE id = :id",
      ['id' => $categoryId]
    )->find();

    return $result ? (float)$result['category_limit'] : null;
  }

  public function updateCategoryLimit(int $categoryId, float $limit): void
  {
    $this->db->query(
      "UPDATE expenses_category_assigned_to_users SET category_limit = :limit WHERE id = :id",
      ['limit' => $limit, 'id' => $categoryId]
    );
  }
}
