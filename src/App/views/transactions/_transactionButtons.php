<div id="customAddExpenseModal" class="custom-modal">
  <div class="custom-modal-dialog">
    <div class="custom-modal-header">
      <h5 id="expenseModalHeader">Expense - New</h5>
      <button type="button" class="btn-close" aria-label="Close" onclick="closeCustomModal('customAddExpenseModal')">&times;</button>
    </div>
    <div class="custom-modal-body">
      <form method="POST" action="/expenses/edit" class="modal-form" id="expenseForm">
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
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
          <label for="amount">Amount</label>
          <?php if (isset($errors['amount'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['amount'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
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
      <form method="POST" action="/incomes/edit" class="modal-form" id="incomeForm">
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
          <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="income_amount" name="amount" placeholder="Amount">
          <label for="income_amount">Amount</label>
          <?php if (isset($errors['amount'])): ?>
            <div class="text-danger mt-1"><?php echo e($errors['amount'][0]); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-floating">
          <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="income_date" name="date" placeholder="mm/dd/yyyy">
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

<script>
function openCustomModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function closeCustomModal(id) {
  document.getElementById(id).style.display = 'none';
  clearAllModalsErrorsAndFields();
}
window.addEventListener('click', function(e) {
  document.querySelectorAll('.custom-modal').forEach(function(modal) {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
});

function clearModalErrorsAndFields(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  modal.querySelectorAll('.text-danger').forEach(el => el.innerHTML = '');
  modal.querySelectorAll('input.form-control').forEach(input => {
    if (input.type !== 'hidden') input.value = '';
  });
  modal.querySelectorAll('select.form-control').forEach(select => {
    select.selectedIndex = 0;
  });
}

function clearAllModalsErrorsAndFields() {
  clearModalErrorsAndFields('customAddExpenseModal');
  clearModalErrorsAndFields('customAddIncomeModal');
}

function setIncomeModalHeader(isEdit, description) {
  const header = document.getElementById('incomeModalHeader');
  if (!header) return;
  if (isEdit) {
    header.textContent = 'Income - ' + (description ? description : '');
  } else {
    header.textContent = 'Income - New';
  }
}
function setExpenseModalHeader(isEdit, description) {
  const header = document.getElementById('expenseModalHeader');
  if (!header) return;
  if (isEdit) {
    header.textContent = 'Expense - ' + (description ? description : '');
  } else {
    header.textContent = 'Expense - New';
  }
}
</script>

<style>
.custom-modal {
  display: none;
  position: fixed;
  z-index: 1050;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
.custom-modal-dialog {
  background: #fff;
  border-radius: 12px;
  max-width: 420px;
  width: 100%;
  margin: auto;
  box-shadow: 0 4px 32px rgba(0,0,0,0.18);
  padding: 0;
  animation: modalIn 0.2s;
}
@keyframes modalIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.custom-modal-header {
  padding: 1.2rem 1.5rem 1rem 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.custom-modal-header h5 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 600;
}
.custom-modal-body {
  padding: 1.5rem;
}
.modal-form .form-floating {
  margin-bottom: 1.1rem;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding-top: 1rem;
}
.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  line-height: 1;
  color: #888;
  cursor: pointer;
  padding: 0 0.5rem;
  transition: color 0.2s;
}
.btn-close:hover {
  color: #d32f2f;
}
.btn.btn-primary {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 0.3rem;
  font-weight: 500;
  transition: background 0.2s;
}
.btn.btn-primary:hover {
  background: #1746a2;
}
.btn.btn-secondary {
  background: #e0e0e0;
  color: #333;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 0.3rem;
  font-weight: 500;
  transition: background 0.2s;
}
.btn.btn-secondary:hover {
  background: #bdbdbd;
}
.form-control, select.form-control {
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  border: 1px solid #bdbdbd;
  border-radius: 0.3rem;
  background: #fafbfc;
  margin-bottom: 0.2rem;
  transition: border 0.2s;
}
.form-control:focus {
  border-color: #2563eb;
  outline: none;
}
label {
  font-size: 0.95rem;
  color: #444;
  margin-bottom: 0.2rem;
}
</style>