<?php

declare(strict_types=1);

namespace App\Services;

class ViewHelperService
{
  public function __construct(private SessionService $session) {}

  /**
   * Get expense category name by ID
   * 
   * @param int|null $categoryId Category ID
   * @return string Category name or 'Uncategorized'
   */
  public function getExpenseCategoryName(?int $categoryId): string
  {
    if (!$categoryId) {
      return 'Uncategorized';
    }

    $expenseCategories = $this->session->get('expenseCategories', []);
    
    foreach ($expenseCategories as $category) {
      if ($category['id'] == $categoryId) {
        return $category['name'];
      }
    }

    return 'Uncategorized';
  }

  /**
   * Get income category name by ID
   * 
   * @param int|null $categoryId Category ID
   * @return string Category name or 'Uncategorized'
   */
  public function getIncomeCategoryName(?int $categoryId): string
  {
    if (!$categoryId) {
      return 'Uncategorized';
    }

    $incomeCategories = $this->session->get('incomeCategories', []);
    
    foreach ($incomeCategories as $category) {
      if ($category['id'] == $categoryId) {
        return $category['name'];
      }
    }

    return 'Uncategorized';
  }

  /**
   * Get payment method name by ID
   * 
   * @param int|null $methodId Payment method ID
   * @return string Payment method name or 'Unknown'
   */
  public function getPaymentMethodName(?int $methodId): string
  {
    if (!$methodId) {
      return 'Unknown';
    }

    $paymentMethods = $this->session->get('paymentMethods', []);
    
    foreach ($paymentMethods as $method) {
      if ($method['id'] == $methodId) {
        return $method['name'];
      }
    }

    return 'Unknown';
  }

  /**
   * Prepare chart data from transactions by category
   * 
   * @param array $transactions Array of transactions
   * @param string $type 'expense' or 'income'
   * @return array{labels: array, data: array}
   */
  public function prepareChartDataByCategory(array $transactions, string $type = 'expense'): array
  {
    $categoryTotals = [];
    
    foreach ($transactions as $transaction) {
      if ($type === 'expense') {
        $catId = $transaction['expense_category_assigned_to_user_id'] ?? null;
        $catName = $this->getExpenseCategoryName($catId);
      } else {
        $catId = $transaction['income_category_assigned_to_user_id'] ?? null;
        $catName = $this->getIncomeCategoryName($catId);
      }
      
      if (!isset($categoryTotals[$catName])) {
        $categoryTotals[$catName] = 0;
      }
      $categoryTotals[$catName] += floatval($transaction['amount']);
    }
    
    // Sort by value (descending)
    arsort($categoryTotals);
    
    return [
      'labels' => array_keys($categoryTotals),
      'data' => array_values($categoryTotals)
    ];
  }

  /**
   * Find expense category by ID
   * 
   * @param int $categoryId Category ID
   * @return array|null Category data or null if not found
   */
  public function findExpenseCategoryById(int $categoryId): ?array
  {
    $expenseCategories = $this->session->get('expenseCategories', []);

    foreach ($expenseCategories as $category) {
      if ((int)$category['id'] === $categoryId) {
        return $category;
      }
    }

    return null;
  }

  /**
   * Find income category by ID
   * 
   * @param int $categoryId Category ID
   * @return array|null Category data or null if not found
   */
  public function findIncomeCategoryById(int $categoryId): ?array
  {
    $incomeCategories = $this->session->get('incomeCategories', []);

    foreach ($incomeCategories as $category) {
      if ((int)$category['id'] === $categoryId) {
        return $category;
      }
    }

    return null;
  }

  /**
   * Find payment method by ID
   * 
   * @param int $methodId Payment method ID
   * @return array|null Payment method data or null if not found
   */
  public function findPaymentMethodById(int $methodId): ?array
  {
    $paymentMethods = $this->session->get('paymentMethods', []);

    foreach ($paymentMethods as $method) {
      if ((int)$method['id'] === $methodId) {
        return $method;
      }
    }

    return null;
  }
}
