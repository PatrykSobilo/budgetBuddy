<?php

declare(strict_types=1);

namespace App\Services;

class DatePeriodService
{
  /**
   * Calculate start and end dates based on period string
   * 
   * @param string $period Period identifier (current_month, last_month, etc.)
   * @return array{start: string|null, end: string|null}
   */
  public function calculatePeriodDates(string $period): array
  {
    $now = new \DateTime();
    $start = null;
    $end = null;
    
    switch ($period) {
      case 'current_month':
        $start = (clone $now)->modify('first day of this month')->format('Y-m-d');
        $end = (clone $now)->modify('last day of this month')->format('Y-m-d');
        break;
      case 'last_month':
        $start = (clone $now)->modify('first day of last month')->format('Y-m-d');
        $end = (clone $now)->modify('last day of last month')->format('Y-m-d');
        break;
      case 'last_30_days':
        $start = (clone $now)->modify('-30 days')->format('Y-m-d');
        $end = $now->format('Y-m-d');
        break;
      case 'last_90_days':
        $start = (clone $now)->modify('-90 days')->format('Y-m-d');
        $end = $now->format('Y-m-d');
        break;
      case 'current_year':
        $start = (clone $now)->modify('first day of January this year')->format('Y-m-d');
        $end = (clone $now)->modify('last day of December this year')->format('Y-m-d');
        break;
      case 'all':
      default:
        $start = null;
        $end = null;
        break;
    }
    
    return ['start' => $start, 'end' => $end];
  }

  /**
   * Filter transactions by period
   * 
   * @param array $transactions Array of transactions
   * @param string $period Period identifier
   * @param string|null $customStartDate Custom start date (for 'custom' period)
   * @param string|null $customEndDate Custom end date (for 'custom' period)
   * @return array Filtered transactions
   */
  public function filterByPeriod(array $transactions, string $period, ?string $customStartDate = null, ?string $customEndDate = null): array
  {
    if ($period === 'all') {
      return $transactions;
    }

    $now = new \DateTime();
    $filtered = [];
    
    foreach ($transactions as $transaction) {
      $transDate = new \DateTime($transaction['date']);
      $include = false;
      
      switch ($period) {
        case 'current_month':
          $include = $transDate->format('Y-m') === $now->format('Y-m');
          break;
        case 'last_month':
          $lastMonth = (clone $now)->modify('-1 month');
          $include = $transDate->format('Y-m') === $lastMonth->format('Y-m');
          break;
        case 'last_30_days':
          $thirtyDaysAgo = (clone $now)->modify('-30 days');
          $include = $transDate >= $thirtyDaysAgo && $transDate <= $now;
          break;
        case 'last_90_days':
          $ninetyDaysAgo = (clone $now)->modify('-90 days');
          $include = $transDate >= $ninetyDaysAgo && $transDate <= $now;
          break;
        case 'current_year':
          $include = $transDate->format('Y') === $now->format('Y');
          break;
        case 'custom':
          if ($customStartDate && $customEndDate) {
            $start = new \DateTime($customStartDate);
            $end = new \DateTime($customEndDate);
            $include = $transDate >= $start && $transDate <= $end;
          }
          break;
      }
      
      if ($include) {
        $filtered[] = $transaction;
      }
    }
    
    return $filtered;
  }
}
