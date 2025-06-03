<!-- Custom Editable Modal for Add Expense -->
<div id="customAddExpenseModal" class="custom-modal" style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
  <div class="custom-modal-dialog" style="background:#fff; border-radius:8px; max-width:400px; width:100%; margin:auto; box-shadow:0 2px 16px rgba(0,0,0,0.2);">
    <div class="custom-modal-header" style="padding:1rem; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
      <h5 style="margin:0;">Add Expense</h5>
      <button type="button" class="btn-close" aria-label="Close" onclick="closeCustomModal('customAddExpenseModal')">&times;</button>
    </div>
    <div class="custom-modal-body" style="padding:1rem;">
      <form method="POST" action="/mainPage" class="grid grid-cols-1 gap-6">
        <?php include $this->resolve("partials/_csrf.php"); ?>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <select class="form-control" id="expensesCategory" name="expensesCategory">
            <option value="Food">Food</option>
            <option value="Rent">Rent</option>
            <option value="Utilities">Utilities</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Other">Other</option>
          </select>
          <label for="expenseCategory">Expense Category</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <select class="form-control" id="paymentMethods" name="paymentMethods">
            <option value="Cash">Cash</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
          <label for="expenseCategory">Payment Method</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
          <label for="amount">Amount</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
          <label for="date">Date</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="description" name="description" placeholder="Description">
          <label for="description">Description</label>
        </div>

        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:0.5rem; padding-top:1rem;">
          <button type="button" class="btn btn-secondary" onclick="closeCustomModal('customAddExpenseModal')">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom Editable Modal for Add Income -->
<div id="customAddIncomeModal" class="custom-modal" style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
  <div class="custom-modal-dialog" style="background:#fff; border-radius:8px; max-width:400px; width:100%; margin:auto; box-shadow:0 2px 16px rgba(0,0,0,0.2);">
    <div class="custom-modal-header" style="padding:1rem; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
      <h5 style="margin:0;">Add Income</h5>
      <button type="button" class="btn-close" aria-label="Close" onclick="closeCustomModal('customAddIncomeModal')">&times;</button>
    </div>
    <div class="custom-modal-body" style="padding:1rem;">
      <form method="POST" action="/mainPage" class="grid grid-cols-1 gap-6">
        <?php include $this->resolve("partials/_csrf.php"); ?>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <select class="form-control" id="incomesCategory" name="incomesCategory">
            <option value="Salary">Salary</option>
            <option value="Gift">Gift</option>
            <option value="Other">Other</option>
          </select>
          <label for="incomesCategory">Income Category</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
          <label for="amount">Amount</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
          <label for="date">Date</label>
        </div>

        <div class="form-floating" style="margin-bottom: 1rem;">
          <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="description" name="description" placeholder="Surname">
          <label for="description">Description</label>
        </div>

        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:0.5rem; padding-top:1rem;">
          <button type="button" class="btn btn-secondary" onclick="closeCustomModal('customAddIncomeModal')">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Replace Bootstrap modal triggers with custom modal triggers -->
<section id="actionButtons" class="text-end d-flex justify-content-center p-4">
  <button type="button" class="btn btn-primary m-1" onclick="openCustomModal('customAddExpenseModal')">+
    Add Expense</button>
  <button type="button" class="btn btn-primary m-1" onclick="openCustomModal('customAddIncomeModal')">+
    Add Income</button>
</section>

<script>
function openCustomModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function closeCustomModal(id) {
  document.getElementById(id).style.display = 'none';
}
// Zamknij modal po kliknięciu w tło
window.addEventListener('click', function(e) {
  document.querySelectorAll('.custom-modal').forEach(function(modal) {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
});
</script>