/**
 * Settings Page Handlers
 * Tab switching, modal management, edit/delete handlers
 */

/**
 * Initialize settings page functionality
 */
function initializeSettingsPage() {
  initializeTabs();
  initializeAddButtons();
  initializeEditButtons();
  initializeDeleteButtons();
  handleErrorModals();
}

/**
 * Initialize tab switching
 */
function initializeTabs() {
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
}

/**
 * Initialize "Add New" buttons
 */
function initializeAddButtons() {
  const newExpenseBtn = document.getElementById('newExpenseCategoryBtn');
  if (newExpenseBtn) {
    newExpenseBtn.addEventListener('click', function() {
      openCustomModal('modalAddExpenseCategory');
    });
  }

  const newIncomeBtn = document.getElementById('newIncomeCategoryBtn');
  if (newIncomeBtn) {
    newIncomeBtn.addEventListener('click', function() {
      openCustomModal('modalAddIncomeCategory');
    });
  }

  const newPaymentMethodBtn = document.getElementById('newPaymentMethodBtn');
  if (newPaymentMethodBtn) {
    newPaymentMethodBtn.addEventListener('click', function() {
      openCustomModal('modalAddPaymentMethod');
    });
  }
}

/**
 * Initialize edit buttons for categories and payment methods
 */
function initializeEditButtons() {
  // Edit Payment Method
  document.querySelectorAll('.edit-payment-method-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      document.getElementById('editPaymentMethodId').value = id;
      document.getElementById('editPaymentMethodInput').value = name;
      openCustomModal('modalEditPaymentMethod');
    });
  });

  // Edit Expense Category
  document.querySelectorAll('.edit-expense-category-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      const limit = this.getAttribute('data-limit');
      document.getElementById('editExpenseCategoryId').value = id;
      document.getElementById('editExpenseCategoryInput').value = name;
      document.getElementById('editExpenseCategoryLimit').value = limit || '';
      openCustomModal('modalEditExpenseCategory');
    });
  });

  // Edit Income Category
  document.querySelectorAll('.edit-income-category-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      const limit = this.getAttribute('data-limit');
      document.getElementById('editIncomeCategoryId').value = id;
      document.getElementById('editIncomeCategoryInput').value = name;
      document.getElementById('editIncomeCategoryLimit').value = limit || '';
      openCustomModal('modalEditIncomeCategory');
    });
  });
}

/**
 * Initialize delete buttons
 */
function initializeDeleteButtons() {
  // Delete Expense Category
  document.querySelectorAll('.delete-expense-category-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      document.getElementById('deleteExpenseCategoryId').value = id;
      document.getElementById('deleteExpenseCategoryName').textContent = name;
      openCustomModal('modalDeleteExpenseCategory');
    });
  });

  // Delete Income Category
  document.querySelectorAll('.delete-income-category-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      document.getElementById('deleteIncomeCategoryId').value = id;
      document.getElementById('deleteIncomeCategoryName').textContent = name;
      openCustomModal('modalDeleteIncomeCategory');
    });
  });

  // Delete Payment Method
  document.querySelectorAll('.delete-payment-method-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      document.getElementById('deletePaymentMethodId').value = id;
      document.getElementById('deletePaymentMethodName').textContent = name;
      openCustomModal('modalDeletePaymentMethod');
    });
  });
}

/**
 * Handle error modals (reopen modal if validation failed)
 * This function is called with PHP data injected
 */
function handleErrorModals() {
  // This will be populated by PHP if there are errors
  // See settings.php for implementation
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initializeSettingsPage);
