<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
<?php include $this->resolve("partials/_searchForm.php"); ?>

<section id="historyPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Incomes</h2>
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
                <?php if (!empty($incomes)): ?>
                    <?php foreach ($incomes as $income): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($income['type']); ?></td>
                            <td><?php echo htmlspecialchars($income['description']); ?></td>
                            <td><?php echo htmlspecialchars($income['amount']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($income['date']))); ?></td>
                            <td>
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-income-icon"
                                    data-description="<?php echo htmlspecialchars($income['description']); ?>"
                                    data-amount="<?php echo htmlspecialchars($income['amount']); ?>"
                                    data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($income['date']))); ?>"
                                    data-category="<?php echo htmlspecialchars($income['income_category_assigned_to_user_id'] ?? ''); ?>"
                                >
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-description="<?php echo htmlspecialchars($income['description']); ?>">
                                    <i class="bi bi-trash"></i>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Income</td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.edit-income-icon').forEach(function(icon) {
    icon.addEventListener('click', function() {
      setIncomeModalHeader(true, this.dataset.description);
      const catSelect = document.getElementById('incomesCategory');
      if (catSelect) {
        catSelect.value = '';
        Array.from(catSelect.options).forEach(opt => {
          if (opt.value == this.dataset.category) opt.selected = true;
        });
      }
      const amountInput = document.querySelector('#customAddIncomeModal #amount');
      if (amountInput) amountInput.value = this.dataset.amount || '';
      const dateInput = document.querySelector('#customAddIncomeModal #date');
      if (dateInput) dateInput.value = this.dataset.date || '';
      const descInput = document.querySelector('#customAddIncomeModal #description');
      if (descInput) descInput.value = this.dataset.description || '';
      openCustomModal('customAddIncomeModal');
    });
  });
  document.querySelectorAll('[onclick*="openCustomModal(\'customAddIncomeModal\')"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      setIncomeModalHeader(false);
    });
  });
});
</script>

<?php include $this->resolve("partials/_footer.php"); ?>