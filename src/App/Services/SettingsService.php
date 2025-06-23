<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class SettingsService
{
    public function __construct(private Database $db) {}

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

    /**
     * Dodaje nową kategorię przychodów dla użytkownika
     * @param int $userId
     * @param string $categoryName
     * @return int ID nowej kategorii
     */
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
}
