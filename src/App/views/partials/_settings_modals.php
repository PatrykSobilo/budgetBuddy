<div id="modalEditEmail" class="custom-modal">
  <div class="custom-modal-dialog">
    <form class="modal-form" method="POST" action="/settings">
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
    <form class="modal-form" method="POST" action="/settings">
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
    <form class="modal-form" method="POST" action="/settings">
      <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
      <div class="custom-modal-header">
        <h5>Set new password</h5>
        <button type="button" class="btn-close" onclick="closeCustomModal('modalEditPassword')">&times;</button>
      </div>
      <div class="custom-modal-body">
        <input type="hidden" name="type" value="password">
        <div class="mb-3">
          <label for="oldPasswordInput" class="form-label fw-bold">Enter old password</label>
          <input type="password" class="form-control" id="oldPasswordInput" name="old_password" placeholder="Type here..." required>
        </div>
        <div class="mb-3">
          <label for="newPasswordInput" class="form-label fw-bold">Enter new password</label>
          <input type="password" class="form-control" id="newPasswordInput" name="new_password" placeholder="Type here..." required>
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