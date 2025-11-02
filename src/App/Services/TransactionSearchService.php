<?php

declare(strict_types=1);

namespace App\Services;

class TransactionSearchService
{
  public function __construct(
    private ViewHelperService $viewHelper
  ) {}

  /**
   * Filter transactions by search query
   * 
   * @param array $transactions Array of transactions
   * @param string $searchQuery Search string
   * @param string $type 'expense' or 'income'
   * @return array Filtered transactions
   */
  public function filterTransactions(array $transactions, string $searchQuery, string $type = 'expense'): array
  {
    if (trim($searchQuery) === '') {
      return $transactions;
    }

    $search = mb_strtolower(trim($searchQuery));
    
    return array_filter($transactions, function($transaction) use ($search, $type) {
      // Search in description
      if (mb_strpos(mb_strtolower($transaction['description'] ?? ''), $search) !== false) {
        return true;
      }
      
      // Search in category
      if ($type === 'expense') {
        $categoryId = $transaction['expense_category_assigned_to_user_id'] ?? null;
        $categoryName = $this->viewHelper->getExpenseCategoryName($categoryId);
        if (mb_strpos(mb_strtolower($categoryName), $search) !== false) {
          return true;
        }
        
        // Search in payment method (only for expenses)
        $paymentId = $transaction['payment_method_assigned_to_user_id'] ?? null;
        $paymentName = $this->viewHelper->getPaymentMethodName($paymentId);
        if (mb_strpos(mb_strtolower($paymentName), $search) !== false) {
          return true;
        }
      } else {
        // Income search
        $categoryId = $transaction['income_category_assigned_to_user_id'] ?? null;
        $categoryName = $this->viewHelper->getIncomeCategoryName($categoryId);
        if (mb_strpos(mb_strtolower($categoryName), $search) !== false) {
          return true;
        }
      }
      
      // Search in amount
      if (mb_strpos((string)$transaction['amount'], $search) !== false) {
        return true;
      }
      
      // Search in date
      if (mb_strpos($transaction['date'], $search) !== false) {
        return true;
      }
      
      return false;
    });
  }
}
