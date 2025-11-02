<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class SettingsService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    // Check if expense category is used in any expenses
    public function isExpenseCategoryUsed(int $categoryId, int $userId): bool
    {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM expenses WHERE expense_category_assigned_to_user_id = :cat_id AND user_id = :user_id",
            [
                'cat_id' => $categoryId,
                'user_id' => $userId
            ]
        )->find();
        return ($result && $result['cnt'] > 0);
    }

    // Check if income category is used in any incomes
    public function isIncomeCategoryUsed(int $categoryId, int $userId): bool
    {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM incomes WHERE income_category_assigned_to_user_id = :cat_id AND user_id = :user_id",
            [
                'cat_id' => $categoryId,
                'user_id' => $userId
            ]
        )->find();
        return ($result && $result['cnt'] > 0);
    }

    // Check if payment method is used in any expenses
    public function isPaymentMethodUsed(int $methodId, int $userId): bool
    {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM expenses WHERE payment_method_assigned_to_user_id = :method_id AND user_id = :user_id",
            [
                'method_id' => $methodId,
                'user_id' => $userId
            ]
        )->find();
        return ($result && $result['cnt'] > 0);
    }

    // Delete expense category
    public function deleteExpenseCategory(int $categoryId, int $userId): void
    {
        $this->db->query(
            "DELETE FROM expenses_category_assigned_to_users WHERE id = :id AND user_id = :user_id",
            [
                'id' => $categoryId,
                'user_id' => $userId
            ]
        );
    }

    // Delete income category
    public function deleteIncomeCategory(int $categoryId, int $userId): void
    {
        $this->db->query(
            "DELETE FROM incomes_category_assigned_to_users WHERE id = :id AND user_id = :user_id",
            [
                'id' => $categoryId,
                'user_id' => $userId
            ]
        );
    }

    // Delete payment method
    public function deletePaymentMethod(int $methodId, int $userId): void
    {
        $this->db->query(
            "DELETE FROM payment_methods_assigned_to_users WHERE id = :id AND user_id = :user_id",
            [
                'id' => $methodId,
                'user_id' => $userId
            ]
        );
    }

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

    public function updateExpenseCategory(int $userId, int $categoryId, string $name, ?float $categoryLimit = null): void
    {
        $this->db->query(
            "UPDATE expenses_category_assigned_to_users SET name = :name, category_limit = :category_limit WHERE id = :id AND user_id = :user_id",
            [
                'name' => $name,
                'category_limit' => $categoryLimit,
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
