<?php 
$pageScripts = ['settings-handlers.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php $categoryErrors = $categoryErrors ?? []; $categoryOld = $categoryOld ?? []; ?>
<?php include $this->resolve("partials/_settings_modals.php"); ?>

<?php
$sectionToShow = $settings_section ?? 'profile';
?>

<div class="settings-ribbon py-3" style="background-color: #f5f6fa; border-bottom: 1px solid #e0e0e0;">
  <div class="container d-flex flex-wrap gap-4 justify-content-center">
    <a href="#" class="btn btn-outline-secondary settings-tab <?php echo $sectionToShow === 'profile' ? 'active' : ''; ?>" data-section="profile">Profile</a>
    <a href="#" class="btn btn-outline-secondary settings-tab <?php echo $sectionToShow === 'expense-categories' ? 'active' : ''; ?>" data-section="expense-categories">Expense Categories</a>
    <a href="#" class="btn btn-outline-secondary settings-tab <?php echo $sectionToShow === 'incomes-categories' ? 'active' : ''; ?>" data-section="incomes-categories">Incomes Categories</a>
    <a href="#" class="btn btn-outline-secondary settings-tab <?php echo $sectionToShow === 'payment-methods' ? 'active' : ''; ?>" data-section="payment-methods">Payment Methods</a>
  </div>
</div>


<?php if (!empty($flash_error)): ?>
  <div class="alert alert-danger text-center" style="margin-top: 2rem; max-width: 600px; margin-left:auto; margin-right:auto;">
    <?php echo htmlspecialchars($flash_error); ?>
  </div>
<?php elseif (!empty($flash_success)): ?>
  <div class="alert alert-success text-center" style="margin-top: 2rem; max-width: 600px; margin-left:auto; margin-right:auto;">
    <?php echo htmlspecialchars($flash_success); ?>
  </div>
<?php endif; ?>

<section class="container mt-5 d-flex flex-column align-items-center justify-content-center">
  <h1 class="mb-4 text-center h2">Settings</h1>
  <div id="profile" class="settings-section mb-5 w-100" style="max-width: 600px;<?php if($sectionToShow !== 'profile') echo 'display:none;'; ?>">
    <h2 class="text-center h3">Profile</h2>
    <?php if (isset($user) && is_array($user)): ?>
      <div class="card p-3 shadow-sm">
        <div class="mb-3">
          <label class="form-label fw-bold">Email</label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;" onclick="openCustomModal('modalEditEmail')">Edit</button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Age</label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;" onclick="openCustomModal('modalEditAge')">Edit</button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" value="********" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;" onclick="openCustomModal('modalEditPassword')">Edit</button>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button class="btn fw-semibold" type="button" style="background: #dc3545; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:160px;" onclick="openCustomModal('modalDeleteAccount')">Delete Account</button>
      </div>
    <?php else: ?>
      <p class="text-center">User data not available.</p>
    <?php endif; ?>
  </div>
  <div id="expense-categories" class="settings-section mb-5 w-100" style="<?php echo ($sectionToShow === 'expense-categories') ? '' : 'display:none;'; ?> max-width: 800px;">
    <h2 class="text-center h3">Expense Categories</h2>
    <?php if (!empty($expenseCategories)): ?>
      <div class="card p-3 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Category</th>
                <th>Category Limit</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($expenseCategories as $category): ?>
                <tr>
                  <td><?php echo htmlspecialchars($category['name']); ?></td>
                  <td>
                    <?php if (isset($category['category_limit']) && $category['category_limit'] !== null): ?>
                      <span class="btn btn-warning btn-sm"><?php echo number_format($category['category_limit'], 2); ?> PLN</span>
                    <?php else: ?>
                      <span class="text-muted fst-italic">Not set</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-primary fw-semibold edit-expense-category-btn" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($category['id']); ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>" data-limit="<?php echo isset($category['category_limit']) ? htmlspecialchars($category['category_limit']) : ''; ?>">Edit</button>
                    <button class="btn btn-sm btn-danger fw-semibold delete-expense-category-btn ms-1" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($category['id']); ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Delete</button>
                  </td>
                </tr>
                <?php if (!empty($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'expense_category_delete' && ($categoryOld['category_id'] ?? null) == $category['id']): ?>
                  <tr>
                    <td colspan="3">
                      <div class="text-danger small"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button id="newExpenseCategoryBtn" class="btn btn-primary fw-semibold" type="button" style="background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:120px;">New...</button>
      </div>
    <?php else: ?>
      <p class="text-center">No expense categories found.</p>
    <?php endif; ?>
  </div>
  <div id="incomes-categories" class="settings-section mb-5 w-100" style="<?php echo ($sectionToShow === 'incomes-categories') ? '' : 'display:none;'; ?> max-width: 800px;">
    <h2 class="text-center h3">Incomes Categories</h2>
    <?php if (!empty($incomeCategories)): ?>
      <div class="card p-3 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Category</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($incomeCategories as $category): ?>
                <tr>
                  <td><?php echo htmlspecialchars($category['name']); ?></td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-primary fw-semibold edit-income-category-btn" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($category['id']); ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</button>
                    <button class="btn btn-sm btn-danger fw-semibold delete-income-category-btn ms-1" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($category['id']); ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Delete</button>
                  </td>
                </tr>
                <?php if (!empty($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'income_category_delete' && ($categoryOld['category_id'] ?? null) == $category['id']): ?>
                  <tr>
                    <td colspan="2">
                      <div class="text-danger small"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button id="newIncomeCategoryBtn" class="btn btn-primary fw-semibold" type="button" style="background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:120px;">New...</button>
      </div>
    <?php else: ?>
      <p class="text-center">No income categories found.</p>
    <?php endif; ?>
  </div>
  <div id="payment-methods" class="settings-section mb-5 w-100" style="<?php echo ($sectionToShow === 'payment-methods') ? '' : 'display:none;'; ?> max-width: 800px;">
    <h2 class="text-center h3">Payment Methods</h2>
    <?php if (!empty($paymentMethods)): ?>
      <div class="card p-3 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Name</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($paymentMethods as $method): ?>
                <tr>
                  <td><?php echo htmlspecialchars($method['name']); ?></td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-primary fw-semibold edit-payment-method-btn" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($method['id']); ?>" data-name="<?php echo htmlspecialchars($method['name']); ?>">Edit</button>
                    <button class="btn btn-sm btn-danger fw-semibold delete-payment-method-btn ms-1" type="button" style="min-width:90px; width:90px; padding: 0.25rem 0.5rem; font-size: 0.875rem;" data-id="<?php echo htmlspecialchars($method['id']); ?>" data-name="<?php echo htmlspecialchars($method['name']); ?>">Delete</button>
                  </td>
                </tr>
                <?php if (!empty($categoryErrors['name']) && ($categoryOld['type'] ?? '') === 'payment_method_delete' && ($categoryOld['category_id'] ?? null) == $method['id']): ?>
                  <tr>
                    <td colspan="2">
                      <div class="text-danger small"><?php echo htmlspecialchars($categoryErrors['name'][0]); ?></div>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button id="newPaymentMethodBtn" class="btn btn-primary fw-semibold" type="button" style="background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:120px;">New...</button>
      </div>
    <?php else: ?>
      <p class="text-center">No payment methods found.</p>
    <?php endif; ?>
  </div>
</section>

<script>
  // Handle error modals - reopen if validation failed
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($categoryErrors['name'])): ?>
      var type = <?php echo json_encode($categoryOld['type'] ?? ''); ?>;
      var modalId = null;
      if (type === 'income_category') modalId = 'modalAddIncomeCategory';
      else if (type === 'expense_category') modalId = 'modalAddExpenseCategory';
      else if (type === 'payment_method') modalId = 'modalAddPaymentMethod';
      else if (type === 'income_category_edit') modalId = 'modalEditIncomeCategory';
      else if (type === 'expense_category_edit') modalId = 'modalEditExpenseCategory';
      else if (type === 'payment_method_edit') modalId = 'modalEditPaymentMethod';
      if (modalId) {
        openCustomModal(modalId);
      }
    <?php endif; ?>
    <?php if (!empty($editUserErrors) && isset($editUserOld['type']) && $editUserOld['type'] === 'password'): ?>
      openCustomModal('modalEditPassword');
    <?php endif; ?>
  });
</script>

<?php include $this->resolve("partials/_footer.php"); ?>
