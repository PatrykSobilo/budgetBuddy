<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php"); ?>

<?php
$transactions = $transactions ?? [];
if (empty($transactions) && isset($this->transactionService)) {
    $allTransactions = $this->transactionService->getUserTransactions();
    $transactions = array_slice($allTransactions, 0, 10);
}
?>

<section id="historyPanel" class="py-3 mb-4">
  <div class="container d-flex flex-wrap border">
    <div class="container mt-5">
      <h2 class="mb-4 text-center">Previous 10 Incomes/Expenses</h2>
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
              <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
              <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($transaction['date']))); ?></td>
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
                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-description="<?php echo htmlspecialchars($transaction['description']); ?>">
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
</section>

<?php include $this->resolve("partials/_footer.php"); ?>