<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use App\Repositories\CategoryRepository;

/**
 * BudgetCalculatorService
 * 
 * Handles all budget calculations, analytics, and category limit tracking.
 * Separated from TransactionService for better single responsibility.
 */
class BudgetCalculatorService
{
    public function __construct(
        private Database $db,
        private CategoryRepository $categoryRepository
    ) {}

    /**
     * Calculate total expenses and incomes for a user within a date range
     * 
     * @param int $userId User ID
     * @param array $transactions All user transactions
     * @param string|null $startDate Start date (YYYY-MM-DD)
     * @param string|null $endDate End date (YYYY-MM-DD)
     * @return array Array with 'expenses', 'incomes', and 'balance'
     */
    public function calculateTransactions(array $transactions, ?string $startDate = null, ?string $endDate = null): array
    {
        $expenses = 0;
        $incomes = 0;

        foreach ($transactions as $t) {
            $date = $t['date'];
            // Extract only date part (YYYY-MM-DD) for comparison
            $dateOnly = substr($date, 0, 10);
            
            $inRange = true;
            if ($startDate && $dateOnly < $startDate) $inRange = false;
            if ($endDate && $dateOnly > $endDate) $inRange = false;
            
            if ($inRange) {
                if ($t['type'] === 'Expense') $expenses += $t['amount'];
                if ($t['type'] === 'Income') $incomes += $t['amount'];
            }
        }

        return [
            'expenses' => $expenses,
            'incomes' => $incomes,
            'balance' => $incomes - $expenses
        ];
    }

    /**
     * Get total amount spent in a category for current month
     * 
     * @param int $userId User ID
     * @param int $categoryId Category ID
     * @param int|null $excludeExpenseId Expense ID to exclude from calculation (for edit validation)
     * @return float Total spent in category this month
     */
    public function getCategoryMonthlyTotal(int $userId, int $categoryId, ?int $excludeExpenseId = null): float
    {
        $currentMonth = date('Y-m-01 00:00:00');
        $nextMonth = date('Y-m-01 00:00:00', strtotime('+1 month'));

        $query = "SELECT COALESCE(SUM(amount), 0) as total 
                  FROM expenses 
                  WHERE user_id = :user_id 
                  AND expense_category_assigned_to_user_id = :category_id
                  AND date_of_expense >= :start_date
                  AND date_of_expense < :end_date";
        
        $params = [
            'user_id' => $userId,
            'category_id' => $categoryId,
            'start_date' => $currentMonth,
            'end_date' => $nextMonth
        ];

        if ($excludeExpenseId !== null) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeExpenseId;
        }

        $result = $this->db->query($query, $params)->find();
        return (float)($result['total'] ?? 0);
    }

    /**
     * Get category spending limit
     * 
     * @param int $categoryId Category ID
     * @return float|null Limit amount or null if not set
     */
    public function getCategoryLimit(int $categoryId): ?float
    {
        return $this->categoryRepository->getCategoryLimit($categoryId);
    }

    /**
     * Get overall budget summary for all categories with limits
     * 
     * @param int $userId User ID
     * @return array Budget summary with totals and category status counts
     */
    public function getBudgetSummary(int $userId): array
    {
        // Get all categories with limits
        $categories = $this->db->query(
            "SELECT id, name, category_limit 
             FROM expenses_category_assigned_to_users 
             WHERE user_id = :user_id AND category_limit IS NOT NULL",
            ['user_id' => $userId]
        )->findAll();

        $totalLimit = 0;
        $totalSpent = 0;
        $categoriesExceeded = 0;
        $categoriesWarning = 0;

        foreach ($categories as $category) {
            $limit = (float)$category['category_limit'];
            $totalLimit += $limit;

            // Calculate spent in category
            $spent = $this->getCategoryMonthlyTotal($userId, (int)$category['id']);
            $totalSpent += $spent;

            // Check status
            if ($limit > 0) {
                $percentage = ($spent / $limit) * 100;
                if ($percentage >= 100) {
                    $categoriesExceeded++;
                } elseif ($percentage >= 80) {
                    $categoriesWarning++;
                }
            }
        }

        return [
            'total_limit' => $totalLimit,
            'total_spent' => $totalSpent,
            'total_percentage' => $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0,
            'categories_exceeded' => $categoriesExceeded,
            'categories_warning' => $categoriesWarning,
            'categories_count' => count($categories)
        ];
    }

    /**
     * Get detailed category data with limits for progress tracking
     * 
     * @param int $userId User ID
     * @return array List of categories with spending data and status
     */
    public function getCategoriesWithLimits(int $userId): array
    {
        // Get categories with limits
        $categories = $this->db->query(
            "SELECT id, name, category_limit 
             FROM expenses_category_assigned_to_users 
             WHERE user_id = :user_id AND category_limit IS NOT NULL
             ORDER BY name ASC",
            ['user_id' => $userId]
        )->findAll();

        $result = [];
        foreach ($categories as $category) {
            $categoryId = (int)$category['id'];
            $limit = (float)$category['category_limit'];
            $spent = $this->getCategoryMonthlyTotal($userId, $categoryId);
            $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;

            $result[] = [
                'id' => $categoryId,
                'name' => $category['name'],
                'limit' => $limit,
                'spent' => $spent,
                'percentage' => $percentage,
                'status' => $percentage >= 100 ? 'exceeded' : ($percentage >= 80 ? 'warning' : 'ok')
            ];
        }

        // Sort: exceeded first, then warning, then by percentage descending
        usort($result, function($a, $b) {
            if ($a['status'] === 'exceeded' && $b['status'] !== 'exceeded') return -1;
            if ($a['status'] !== 'exceeded' && $b['status'] === 'exceeded') return 1;
            if ($a['status'] === 'warning' && $b['status'] === 'ok') return -1;
            if ($a['status'] === 'ok' && $b['status'] === 'warning') return 1;
            return $b['percentage'] <=> $a['percentage'];
        });

        return $result;
    }

    /**
     * Get daily cumulative spending timeline for a category in current month
     * 
     * @param int $userId User ID
     * @param int $categoryId Category ID
     * @return array Timeline data for charts (date and cumulative amount)
     */
    public function getCategoryTimeline(int $userId, int $categoryId, ?string $month = null): array
    {
        $month = $month ?? date('Y-m');
        $currentMonth = $month . '-01 00:00:00';
        $nextMonth = date('Y-m-01 00:00:00', strtotime($currentMonth . ' +1 month'));

        // Get all expenses in category for selected month
        $expenses = $this->db->query(
            "SELECT amount, DATE(date_of_expense) as expense_date
             FROM expenses
             WHERE user_id = :user_id
             AND expense_category_assigned_to_user_id = :category_id
             AND date_of_expense >= :start_date
             AND date_of_expense < :end_date
             ORDER BY date_of_expense ASC",
            [
                'user_id' => $userId,
                'category_id' => $categoryId,
                'start_date' => $currentMonth,
                'end_date' => $nextMonth
            ]
        )->findAll();

        // Prepare cumulative data
        $timeline = [];
        $daysInMonth = (int)date('t', strtotime($currentMonth)); // number of days in month

        // Initialize all days in month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $month . '-' . str_pad((string)$day, 2, '0', STR_PAD_LEFT);
            $timeline[$date] = 0;
        }

        // Sum expenses day by day
        foreach ($expenses as $expense) {
            $date = $expense['expense_date'];
            if (isset($timeline[$date])) {
                $timeline[$date] += (float)$expense['amount'];
            }
        }

        // Convert to cumulative values
        $cumulativeTimeline = [];
        $cumulative = 0;
        foreach ($timeline as $date => $amount) {
            $cumulative += $amount;
            $cumulativeTimeline[] = [
                'date' => $date,
                'amount' => $cumulative
            ];
        }

        return $cumulativeTimeline;
    }

    /**
     * Pobiera timeline dla wszystkich kategorii z limitami
     */
    public function getAllCategoriesTimeline(int $userId, ?string $month = null): array
    {
        $month = $month ?? date('Y-m');
        $currentMonth = $month . '-01 00:00:00';
        $nextMonth = date('Y-m-01 00:00:00', strtotime($currentMonth . ' +1 month'));

        // Get categories with limits
        $categories = $this->db->query(
            "SELECT id, name, category_limit
             FROM expenses_category_assigned_to_users
             WHERE user_id = :user_id
             AND category_limit IS NOT NULL
             ORDER BY name ASC",
            ['user_id' => $userId]
        )->findAll();

        $daysInMonth = (int)date('t', strtotime($currentMonth));
        $allTimelines = [];

        foreach ($categories as $category) {
            $categoryId = (int)$category['id'];
            
            // Get expenses for this category
            $expenses = $this->db->query(
                "SELECT amount, DATE(date_of_expense) as expense_date
                 FROM expenses
                 WHERE user_id = :user_id
                 AND expense_category_assigned_to_user_id = :category_id
                 AND date_of_expense >= :start_date
                 AND date_of_expense < :end_date
                 ORDER BY date_of_expense ASC",
                [
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'start_date' => $currentMonth,
                    'end_date' => $nextMonth
                ]
            )->findAll();

            // Initialize timeline for this category
            $timeline = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $month . '-' . str_pad((string)$day, 2, '0', STR_PAD_LEFT);
                $timeline[$date] = 0;
            }

            // Sum expenses day by day
            foreach ($expenses as $expense) {
                $date = $expense['expense_date'];
                if (isset($timeline[$date])) {
                    $timeline[$date] += (float)$expense['amount'];
                }
            }

            // Convert to cumulative values
            $cumulativeData = [];
            $cumulative = 0;
            foreach ($timeline as $date => $amount) {
                $cumulative += $amount;
                $cumulativeData[] = $cumulative;
            }

            $allTimelines[] = [
                'id' => $categoryId,
                'name' => $category['name'],
                'limit' => (float)$category['category_limit'],
                'data' => $cumulativeData
            ];
        }

        // Create dates array (only once for all categories)
        $dates = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dates[] = $month . '-' . str_pad((string)$day, 2, '0', STR_PAD_LEFT);
        }

        return [
            'dates' => $dates,
            'categories' => $allTimelines
        ];
    }
}
