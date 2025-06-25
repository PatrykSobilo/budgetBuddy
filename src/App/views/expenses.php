<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
<?php include $this->resolve("partials/_searchForm.php"); ?>

<section id="historyExpensesPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Expenses</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['type']); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?></td>
                            <td>
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-icon"
                                    data-id="<?php echo htmlspecialchars($expense['id'] ?? ''); ?>"
                                    data-description="<?php echo htmlspecialchars($expense['description']); ?>"
                                    data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                    data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?>"
                                    data-category="<?php echo htmlspecialchars($expense['expense_category_assigned_to_user_id'] ?? ''); ?>"
                                    data-payment="<?php echo htmlspecialchars($expense['payment_method_assigned_to_user_id'] ?? ''); ?>"
                                >
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-description="<?php echo htmlspecialchars($expense['description']); ?>">
                                    <i class="bi bi-trash"></i>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Expense</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.edit-icon').forEach(function(icon) {
    icon.addEventListener('click', function() {
      setExpenseModalHeader(true, this.dataset.description);
      const idInput = document.getElementById('expense_id');
      if (idInput) idInput.value = this.dataset.id || '';
      const catSelect = document.getElementById('expensesCategory');
      if (catSelect) {
        catSelect.value = '';
        Array.from(catSelect.options).forEach(opt => {
          if (opt.value == this.dataset.category) opt.selected = true;
        });
      }
      const paySelect = document.getElementById('paymentMethods');
      if (paySelect) {
        paySelect.value = '';
        Array.from(paySelect.options).forEach(opt => {
          if (opt.value == this.dataset.payment) opt.selected = true;
        });
      }
      document.getElementById('amount').value = this.dataset.amount || '';
      document.getElementById('date').value = this.dataset.date || '';
      document.getElementById('description').value = this.dataset.description || '';
      // Ustaw action na edycję
      const form = document.getElementById('expenseForm');
      if (form) form.action = '/expenses/edit';
      openCustomModal('customAddExpenseModal');
    });
  });
  document.querySelectorAll('[onclick*="openCustomModal(\'customAddExpenseModal\')"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      setExpenseModalHeader(false);
      const idInput = document.getElementById('expense_id');
      if (idInput) idInput.value = '';
      // Ustaw action na dodawanie
      const form = document.getElementById('expenseForm');
      if (form) form.action = '/transactions/add';
    });
  });
});
</script>