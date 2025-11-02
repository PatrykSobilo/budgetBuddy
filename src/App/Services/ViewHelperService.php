<?php

declare(strict_types=1);

namespace App\Services;

class ViewHelperService
{
  /**
   * Get expense category name by ID
   * 
   * @param int|null $categoryId Category ID
   * @return string Category name or 'Uncategorized'
   */
  public function getExpenseCategoryName(?int $categoryId): string
  {
    if (!$categoryId || empty($_SESSION['expenseCategories'])) {
      return 'Uncategorized';
    }

    foreach ($_SESSION['expenseCategories'] as $category) {
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
    if (!$categoryId || empty($_SESSION['incomeCategories'])) {
      return 'Uncategorized';
    }

    foreach ($_SESSION['incomeCategories'] as $category) {
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
    if (!$methodId || empty($_SESSION['paymentMethods'])) {
      return 'Unknown';
    }

    foreach ($_SESSION['paymentMethods'] as $method) {
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
}
