<!-- Delete Account Modal -->
<div id="modalDeleteAccount" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/delete-account">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Delete Account</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalDeleteAccount')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <p>Are you sure you want to <span class="fw-bold text-danger">delete your account</span> and all your data? This action cannot be undone.</p>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalDeleteAccount')">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete Account</button>
      </div>
    </form>
  </div>
</div>
<!-- Delete Expense Category Modal -->
<div id="modalDeleteExpenseCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/delete-category">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <input type="hidden" name="type" value="expense_category_delete">
      <input type="hidden" id="deleteExpenseCategoryId" name="category_id" value="">
      <div class="custom-modal-header">
        <h5>Delete Expense Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalDeleteExpenseCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <p>Are you sure you want to delete the expense category <span id="deleteExpenseCategoryName" class="fw-bold"></span>?<br>This action cannot be undone.</p>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalDeleteExpenseCategory')">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Income Category Modal -->
<div id="modalDeleteIncomeCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/delete-category">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <input type="hidden" name="type" value="income_category_delete">
      <input type="hidden" id="deleteIncomeCategoryId" name="category_id" value="">
      <div class="custom-modal-header">
        <h5>Delete Income Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalDeleteIncomeCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <p>Are you sure you want to delete the income category <span id="deleteIncomeCategoryName" class="fw-bold"></span>?<br>This action cannot be undone.</p>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalDeleteIncomeCategory')">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Payment Method Modal -->
<div id="modalDeletePaymentMethod" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/delete-category">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <input type="hidden" name="type" value="payment_method_delete">
      <input type="hidden" id="deletePaymentMethodId" name="category_id" value="">
      <div class="custom-modal-header">
        <h5>Delete Payment Method</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalDeletePaymentMethod')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <p>Are you sure you want to delete the payment method <span id="deletePaymentMethodName" class="fw-bold"></span>?<br>This action cannot be undone.</p>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalDeletePaymentMethod')">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>
<div id="modalEditEmail" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/edit-user">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Set new e-mail</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditEmail')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="email">
        <div class="mb-3">
          <label for="editEmailInput" class="form-label fw-bold">New e-mail</label>
          <input type="email" class="form-control" id="editEmailInput" name="email" placeholder="Type here..." required>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditEmail')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditAge" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/edit-user">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Set Age</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditAge')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="age">
        <div class="mb-3">
          <label for="editAgeInput" class="form-label fw-bold">Age</label>
          <input type="number" class="form-control" id="editAgeInput" name="age" placeholder="Type here..." min="1" max="120" required>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditAge')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditPassword" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings/edit-user">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Set new password</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditPassword')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="password">
        <div class="mb-3">
          <label for="oldPasswordInput" class="form-label fw-bold">Enter old password</label>
          <input type="password" class="form-control" id="oldPasswordInput" name="old_password" placeholder="Type here..." required value="<?php echo isset($editUserOld['old_password']) && $editUserOld['type']==='password' ? htmlspecialchars($editUserOld['old_password']) : ''; ?>">
          <?php if (isset($editUserErrors['old_password'])): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($editUserErrors['old_password'][0]); ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="newPasswordInput" class="form-label fw-bold">Enter new password</label>
          <input type="password" class="form-control" id="newPasswordInput" name="new_password" placeholder="Type here..." required value="<?php echo isset($editUserOld['new_password']) && $editUserOld['type']==='password' ? htmlspecialchars($editUserOld['new_password']) : ''; ?>">
          <?php if (isset($editUserErrors['new_password'])): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($editUserErrors['new_password'][0]); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditPassword')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalAddExpenseCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Add Expense Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalAddExpenseCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="expense_category">
        <div class="mb-3">
          <label for="addExpenseCategoryInput" class="form-label fw-bold">Category name</label>
          <input type="text" class="form-control" id="addExpenseCategoryInput" name="category_name" placeholder="Type here..." maxlength="50" required>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalAddExpenseCategory')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalAddIncomeCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Add Income Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalAddIncomeCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="income_category">
        <div class="mb-3">
          <label for="addIncomeCategoryInput" class="form-label fw-bold">Category name</label>
          <input type="text" class="form-control" id="addIncomeCategoryInput" name="category_name" placeholder="Type here..." maxlength="50" required>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalAddIncomeCategory')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalAddPaymentMethod" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Add Payment Method</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalAddPaymentMethod')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="payment_method">
        <div class="mb-3">
          <label for="addPaymentMethodInput" class="form-label fw-bold">Payment method name</label>
          <input type="text" class="form-control" id="addPaymentMethodInput" name="category_name" placeholder="Type here..." maxlength="50" required>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalAddPaymentMethod')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditPaymentMethod" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Edit Payment Method</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditPaymentMethod')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="payment_method_edit">
        <input type="hidden" id="editPaymentMethodId" name="category_id" value="">
        <div class="mb-3">
          <label for="editPaymentMethodInput" class="form-label fw-bold">Payment method name</label>
          <input type="text" class="form-control" id="editPaymentMethodInput" name="category_name" placeholder="Type here..." maxlength="50" required value="<?php echo isset($categoryOld['name']) && $categoryOld['type']==='payment_method_edit' ? htmlspecialchars($categoryOld['name']) : ''; ?>">
          <?php if (isset($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'payment_method_edit'): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditPaymentMethod')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditExpenseCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Edit Expense Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditExpenseCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="expense_category_edit">
        <input type="hidden" id="editExpenseCategoryId" name="category_id" value="">
        <div class="mb-3">
          <label for="editExpenseCategoryInput" class="form-label fw-bold">Category name</label>
          <input type="text" class="form-control" id="editExpenseCategoryInput" name="category_name" placeholder="Type here..." maxlength="50" required value="<?php echo isset($categoryOld['name']) && $categoryOld['type']==='expense_category_edit' ? htmlspecialchars($categoryOld['name']) : ''; ?>">
          <?php if (isset($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'expense_category_edit'): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="editExpenseCategoryLimit" class="form-label fw-bold">Category Limit <span class="text-muted fw-normal">(optional)</span></label>
          <input type="number" class="form-control" id="editExpenseCategoryLimit" name="category_limit" placeholder="0.00" step="0.01" min="0">
          <small class="text-muted">Set a monthly spending limit for this category</small>
          <?php if (isset($categoryErrors['category_limit']) && ($categoryOld['type'] ?? '') === 'expense_category_edit'): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($categoryErrors['category_limit'][0]); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditExpenseCategory')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditIncomeCategory" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Edit Income Category</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditIncomeCategory')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="income_category_edit">
        <input type="hidden" id="editIncomeCategoryId" name="category_id" value="">
        <div class="mb-3">
          <label for="editIncomeCategoryInput" class="form-label fw-bold">Category name</label>
          <input type="text" class="form-control" id="editIncomeCategoryInput" name="category_name" placeholder="Type here..." maxlength="50" required value="<?php echo isset($categoryOld['name']) && $categoryOld['type']==='income_category_edit' ? htmlspecialchars($categoryOld['name']) : ''; ?>">
          <?php if (isset($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'income_category_edit'): ?>
            <div class="text-danger mt-1"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex justify-content-center w-100" style="padding-bottom: 1.5rem;">
        <button type="button" class="btn btn-secondary me-2" onclick="closeCustomModal('modalEditIncomeCategory')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>