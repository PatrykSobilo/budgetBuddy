<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class SettingsService
{
    public function __construct(private Database $db) {}

    public function addPaymentMethod(int $userId, string $methodName): int
    {
        $this->db->query(
            "INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
            [
                'user_id' => $userId,
                'name' => $methodName
            ]
        );
        return (int)$this->db->id();
    }

    public function addExpenseCategory(int $userId, string $categoryName): int
    {
        $this->db->query(
            "INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
            [
                'user_id' => $userId,
                'name' => $categoryName
            ]
        );
        return (int)$this->db->id();
    }

    public function addIncomeCategory(int $userId, string $categoryName): int
    {
        $this->db->query(
            "INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
            [
                'user_id' => $userId,
                'name' => $categoryName
            ]
        );
        return (int)$this->db->id();
    }
    
    public function updatePaymentMethod(int $userId, int $methodId, string $name): void
    {
        $this->db->query(
            "UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id AND user_id = :user_id",
            [
                'name' => $name,
                'id' => $methodId,
                'user_id' => $userId
            ]
        );
    }

    public function updateExpenseCategory(int $userId, int $categoryId, string $name): void
    {
        $this->db->query(
            "UPDATE expenses_category_assigned_to_users SET name = :name WHERE id = :id AND user_id = :user_id",
            [
                'name' => $name,
                'id' => $categoryId,
                'user_id' => $userId
            ]
        );
    }

    public function updateIncomeCategory(int $userId, int $categoryId, string $name): void
    {
        $this->db->query(
            "UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id AND user_id = :user_id",
            [
                'name' => $name,
                'id' => $categoryId,
                'user_id' => $userId
            ]
        );
    }

    public function getDb(): Database
    {
        return $this->db;
    }
}
