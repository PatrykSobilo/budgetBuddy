<div id="customAddExpenseModal" class="custom-modal">
  <div class="custom-modal-dialog">
    <div class="custom-modal-header">
      <h5 id="expenseModalHeader">Expense - New</h5>
      <button type="button" class="btn-close" aria-label="Close" onclick="closeCustomModal('customAddExpenseModal')">&times;</button>
    </div>
    <div class="custom-modal-body">
      <form method="POST" action="/transactions/add" class="modal-form" id="expenseForm">
        <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
        <input type="hidden" id="expense_id" name="expense_id" value="<?php echo e($oldFormData['expense_id'] ?? ''); ?>">

        <div class="form-floating">
          <select class="form-control" id="expensesCategory" name="expensesCategory">
            <option value="">-- Wybierz kategorię --</option>
            <?php if (!empty($_SESSION['expenseCategories'])): ?>
              <?php foreach ($_SESSION['expenseCategories'] as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php if (($oldFormData['expensesCategory'] ?? '') == $cat['id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option disabled>Brak kategorii</option>
            <?php endif; ?>
          </select>
          <label for="expensesCategory">Expense Category</label>
          <?php if (isset($errors['expensesCategory'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['expensesCategory'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <select class="form-control" id="paymentMethods" name="paymentMethods">
            <option value="">-- Wybierz metodę --</option>
            <?php if (!empty($_SESSION['paymentMethods'])): ?>
              <?php foreach ($_SESSION['paymentMethods'] as $method): ?>
                <option value="<?php echo htmlspecialchars($method['id']); ?>" <?php if (($oldFormData['paymentMethods'] ?? '') == $method['id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($method['name']); ?>
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option disabled>Brak metod</option>
            <?php endif; ?>
          </select>
          <label for="paymentMethods">Payment Method</label>
          <?php if (isset($errors['paymentMethods'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['paymentMethods'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Amount">
          <label for="amount">Amount</label>
          <?php if (isset($errors['amount'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['amount'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['date'] ?? date('Y-m-d')); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
          <label for="date">Date</label>
          <?php if (isset($errors['date'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['date'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="description" name="description" placeholder="Description">
          <label for="description">Description</label>
          <?php if (isset($errors['description'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['description'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeCustomModal('customAddExpenseModal')">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="customAddIncomeModal" class="custom-modal">
  <div class="custom-modal-dialog">
    <div class="custom-modal-header">
      <h5 id="incomeModalHeader">Income - New</h5>
      <button type="button" class="btn-close" aria-label="Close" onclick="closeCustomModal('customAddIncomeModal')">&times;</button>
    </div>
    <div class="custom-modal-body">
      <form method="POST" action="/transactions/add" class="modal-form" id="incomeForm">
        <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
        <input type="hidden" id="income_id" name="income_id" value="<?php echo e($oldFormData['income_id'] ?? ''); ?>">

        <div class="form-floating">
          <select class="form-control" id="incomesCategory" name="incomesCategory">
            <option value="">-- Wybierz kategorię --</option>
            <?php if (!empty($_SESSION['incomeCategories'])): ?>
              <?php foreach ($_SESSION['incomeCategories'] as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php if (($oldFormData['incomesCategory'] ?? '') == $cat['id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option disabled>Brak kategorii</option>
            <?php endif; ?>
          </select>
          <label for="incomesCategory">Income Category</label>
          <?php if (isset($errors['incomesCategory'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['incomesCategory'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" step="0.01" class="form-control" id="income_amount" name="amount" placeholder="Amount">
          <label for="income_amount">Amount</label>
          <?php if (isset($errors['amount'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['amount'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['date'] ?? date('Y-m-d')); ?>" type="date" class="form-control" id="income_date" name="date" placeholder="mm/dd/yyyy">
          <label for="income_date">Date</label>
          <?php if (isset($errors['date'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['date'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="income_description" name="description" placeholder="Description">
          <label for="income_description">Description</label>
          <?php if (isset($errors['description'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['description'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeCustomModal('customAddIncomeModal')">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<section id="actionButtons" class="text-end d-flex justify-content-center p-4">
  <button type="button" class="btn btn-primary m-1" onclick="openCustomModal('customAddExpenseModal')">+
    Add Expense</button>
  <button type="button" class="btn btn-primary m-1" onclick="openCustomModal('customAddIncomeModal')">+
    Add Income</button>
</section>

<?php if (!empty($openModal)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    openCustomModal('<?php echo $openModal; ?>');
    <?php $openModal = null; ?>
  });
</script>
<?php endif; ?>