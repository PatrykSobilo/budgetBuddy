<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php"); ?>

<?php
$transactions = $transactions ?? [];
if (empty($transactions) && isset($this->transactionService)) {
    $allTransactions = $this->transactionService->getUserTransactions();
    $transactions = array_slice($allTransactions, 0, 10);
}
$budgetSummary = $budgetSummary ?? null;
?>

<!-- Budget Status Widget -->
<?php if ($budgetSummary && $budgetSummary['categories_count'] > 0): ?>
<section id="budgetWidget" class="py-3 mb-4">
  <div class="container">
    <div class="card shadow-sm" style="border-left: 4px solid <?php 
      $percentage = $budgetSummary['total_percentage'];
      echo $percentage >= 100 ? '#dc3545' : ($percentage >= 80 ? '#ffc107' : '#28a745'); 
    ?>;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">💰 Monthly Budget Status</h5>
          <span class="badge bg-<?php 
            echo $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success'); 
          ?> fs-6">
            <?php echo number_format($budgetSummary['total_percentage'], 1); ?>%
          </span>
        </div>
        
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">
              <?php echo number_format($budgetSummary['total_spent'], 2); ?> PLN / 
              <?php echo number_format($budgetSummary['total_limit'], 2); ?> PLN
            </span>
            <span class="fw-bold <?php echo $percentage >= 100 ? 'text-danger' : ($percentage >= 80 ? 'text-warning' : 'text-success'); ?>">
              <?php if ($percentage < 100): ?>
                ✓ <?php echo $percentage >= 80 ? 'Warning' : 'On Track'; ?>
              <?php else: ?>
                ⚠️ Over Budget
              <?php endif; ?>
            </span>
          </div>
          <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-<?php 
              echo $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success'); 
            ?>" 
            role="progressbar" 
            style="width: <?php echo min($percentage, 100); ?>%;" 
            aria-valuenow="<?php echo $percentage; ?>" 
            aria-valuemin="0" 
            aria-valuemax="100">
              <?php echo number_format($budgetSummary['total_percentage'], 1); ?>%
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="text-muted small">
            <?php if ($budgetSummary['categories_exceeded'] > 0): ?>
              <span class="badge bg-danger me-2">
                ⚠️ <?php echo $budgetSummary['categories_exceeded']; ?> category<?php echo $budgetSummary['categories_exceeded'] > 1 ? 'ies' : 'y'; ?> exceeded
              </span>
            <?php endif; ?>
            <?php if ($budgetSummary['categories_warning'] > 0): ?>
              <span class="badge bg-warning text-dark">
                🟡 <?php echo $budgetSummary['categories_warning']; ?> category<?php echo $budgetSummary['categories_warning'] > 1 ? 'ies' : 'y'; ?> near limit
              </span>
            <?php endif; ?>
            <?php if ($budgetSummary['categories_exceeded'] == 0 && $budgetSummary['categories_warning'] == 0): ?>
              <span class="badge bg-success">✓ All categories within budget</span>
            <?php endif; ?>
          </div>
          <a href="/planner" class="btn btn-sm btn-outline-primary">
            View Details →
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section id="historyPanel" class="py-3 mb-4">
  <div class="container d-flex flex-wrap border">
    <div class="container mt-5">
      <h1 class="mb-4 text-center h2">Recent Transactions</h1>
      <div class="table-responsive">
      <table class="table table-bordered table-transactions" name="balance">
        <thead>
          <tr>
            <th class="type-col">Type</th>
            <th class="description-col">Description</th>
            <th class="amount-col">Amount</th>
            <th class="date-col">Date</th>
            <th class="actions-col">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($transactions)): ?>
          <?php foreach ($transactions as $transaction): ?>
            <tr>
              <td><?php echo htmlspecialchars($transaction['type']); ?></td>
              <td><?php echo htmlspecialchars($transaction['description']); ?></td>
              <td class="amount-col"><?php echo number_format($transaction['amount'], 2, '.', ' '); ?></td>
              <td class="date-col"><?php echo htmlspecialchars(date('Y-m-d', strtotime($transaction['date']))); ?></td>
              <td>
                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-icon"
                  data-type="<?php echo htmlspecialchars($transaction['type']); ?>"
                  data-id="<?php echo htmlspecialchars($transaction['id'] ?? ''); ?>"
                  data-description="<?php echo htmlspecialchars($transaction['description']); ?>"
                  data-amount="<?php echo htmlspecialchars($transaction['amount']); ?>"
                  data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($transaction['date']))); ?>"
                  <?php if ($transaction['type'] === 'Expense'): ?>
                    data-category="<?php echo htmlspecialchars($transaction['expense_category_assigned_to_user_id'] ?? ''); ?>"
                    data-payment="<?php echo htmlspecialchars($transaction['payment_method_assigned_to_user_id'] ?? ''); ?>"
                  <?php elseif ($transaction['type'] === 'Income'): ?>
                    data-category="<?php echo htmlspecialchars($transaction['income_category_assigned_to_user_id'] ?? ''); ?>"
                  <?php endif; ?>
                >
                  <i class="bi bi-pencil"></i>
                </span>
                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-id="<?php echo htmlspecialchars($transaction['id'] ?? ''); ?>" data-type="<?php echo htmlspecialchars($transaction['type']); ?>" data-description="<?php echo htmlspecialchars($transaction['description']); ?>">
                  <i class="bi bi-trash"></i>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No transactions found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
      </div>
  </div>
</div>
</section>

<form id="deleteExpenseForm" method="POST" action="/mainPage/delete-expense" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
  <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
</form>
<form id="deleteIncomeForm" method="POST" action="/mainPage/delete-income" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
  <input type="hidden" name="income_id" id="deleteIncomeId" value="">
</form>

<?php include $this->resolve("partials/_footer.php"); ?>