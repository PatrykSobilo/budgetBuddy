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
      <h2 class="mb-4">Previous 10 Incomes/Expenses</h2>
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
              <td></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No transactions found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>

<?php include $this->resolve("partials/_footer.php"); ?>

<?php if (!empty($openModal)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    openCustomModal('<?php echo $openModal; ?>');
  });
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>