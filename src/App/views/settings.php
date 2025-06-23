<?php include $this->resolve("partials/_header.php"); ?>

<div class="settings-ribbon py-3" style="background-color: #f5f6fa; border-bottom: 1px solid #e0e0e0;">
  <div class="container d-flex flex-wrap gap-4 justify-content-center">
    <a href="#" class="btn btn-outline-secondary settings-tab active" data-section="profile">Profile</a>
    <a href="#" class="btn btn-outline-secondary settings-tab" data-section="expense-categories">Expense Categories</a>
    <a href="#" class="btn btn-outline-secondary settings-tab" data-section="incomes-categories">Incomes Categories</a>
  </div>
</div>

<section class="container mt-5 d-flex flex-column align-items-center justify-content-center">
  <h1 class="mb-4 text-center">Settings</h1>
  <div id="profile" class="settings-section mb-5 w-100" style="max-width: 600px;">
    <h3 class="text-center">Profile</h3>
    <?php if (isset($user) && is_array($user)): ?>
      <div class="card p-3 shadow-sm">
        <div class="mb-3">
          <label class="form-label fw-bold">Email</label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Edit</button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Age</label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Edit</button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" value="********" readonly style="border-radius: 0.3rem;">
            <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Edit</button>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button class="btn fw-semibold" type="button" style="background: #dc3545; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:160px;">Delete Account</button>
      </div>
    <?php else: ?>
      <p class="text-center">User data not available.</p>
    <?php endif; ?>
  </div>
  <div id="expense-categories" class="settings-section mb-5 w-100" style="display:none; max-width: 600px;">
    <h3 class="text-center">Expense Categories</h3>
    <?php if (!empty($_SESSION['expenseCategories'])): ?>
      <div class="card p-3 shadow-sm">
        <?php foreach ($_SESSION['expenseCategories'] as $category): ?>
          <div class="mb-3 d-flex align-items-center justify-content-between">
            <div class="input-group">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" readonly style="border-radius: 0.3rem;">
              <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Edit</button>
              <button class="btn ms-2 fw-semibold" type="button" style="min-width:120px; background: #dc3545; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button id="newExpenseCategoryBtn" class="btn btn-primary fw-semibold" type="button" style="background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:120px;">New...</button>
      </div>
    <?php else: ?>
      <p class="text-center">No expense categories found.</p>
    <?php endif; ?>
  </div>
  <div id="incomes-categories" class="settings-section mb-5 w-100" style="display:none; max-width: 600px;">
    <h3 class="text-center">Incomes Categories</h3>
    <?php if (!empty($_SESSION['incomeCategories'])): ?>
      <div class="card p-3 shadow-sm">
        <?php foreach ($_SESSION['incomeCategories'] as $category): ?>
          <div class="mb-3 d-flex align-items-center justify-content-between">
            <div class="input-group">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" readonly style="border-radius: 0.3rem;">
              <button class="btn btn-primary ms-2 fw-semibold" type="button" style="min-width:120px; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Edit</button>
              <button class="btn ms-2 fw-semibold" type="button" style="min-width:120px; background: #dc3545; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500;">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button id="newIncomeCategoryBtn" class="btn btn-primary fw-semibold" type="button" style="background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 0.3rem; font-weight: 500; min-width:120px;">New...</button>
      </div>
    <?php else: ?>
      <p class="text-center">No income categories found.</p>
    <?php endif; ?>
  </div>
</section>

<div class="modal fade" id="addExpenseCategoryModal" tabindex="-1" aria-labelledby="addExpenseCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addExpenseCategoryModalLabel">Add Expense Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addExpenseCategoryForm" method="POST" action="/settings" autocomplete="off">
          <div class="input-group">
            <input type="text" class="form-control" name="name" placeholder="Category Name..." style="border-radius: 0.3rem;">
            <button type="submit" class="btn btn-primary fw-semibold ms-2" style="min-width:120px; background: #2563eb; color: #fff; border: none; border-radius: 0.3rem; font-weight: 500;">Save</button>
          </div>
          <input type="hidden" name="type" value="expense">
          <input type="hidden" name="token" value="<?php echo e($csrfToken); ?>">
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addIncomeCategoryModal" tabindex="-1" aria-labelledby="addIncomeCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addIncomeCategoryModalLabel">Add Income Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addIncomeCategoryForm" method="POST" action="/settings" autocomplete="off">
          <div class="input-group">
            <input type="text" class="form-control" name="name" placeholder="Category Name..." style="border-radius: 0.3rem;">
            <button type="submit" class="btn btn-primary fw-semibold ms-2" style="min-width:120px; background: #2563eb; color: #fff; border: none; border-radius: 0.3rem; font-weight: 500;">Save</button>
          </div>
          <input type="hidden" name="type" value="income">
          <input type="hidden" name="token" value="<?php echo e($csrfToken); ?>">
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.settings-tab');
    const sections = document.querySelectorAll('.settings-section');
    tabs.forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const sectionId = this.getAttribute('data-section');
        sections.forEach(section => {
          section.style.display = (section.id === sectionId) ? '' : 'none';
        });
      });
    });

    const newExpenseBtn = document.getElementById('newExpenseCategoryBtn');
    if (newExpenseBtn) {
      newExpenseBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('addExpenseCategoryModal'));
        modal.show();
      });
    }
    const newIncomeBtn = document.getElementById('newIncomeCategoryBtn');
    if (newIncomeBtn) {
      newIncomeBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('addIncomeCategoryModal'));
        modal.show();
      });
    }
  });
</script>

<?php include $this->resolve("partials/_footer.php"); ?>
