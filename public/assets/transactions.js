function openCustomModal(id) {
  document.getElementById(id).style.display = 'flex';
}

function getTodayDate() {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
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
    if (input.type !== 'hidden') {
      if (input.type === 'date') {
        input.value = getTodayDate();
      } else {
        input.value = '';
      }
    }
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
      const form = document.getElementById('expenseForm');
      if (form) form.action = '/expenses/edit';
      openCustomModal('customAddExpenseModal');
    });
  });
  document.querySelectorAll('.edit-income-icon, .edit-icon[data-type="Income"]').forEach(function(icon) {
    icon.addEventListener('click', function() {
      setIncomeModalHeader(true, this.dataset.description);
      const idInput = document.getElementById('income_id');
      if (idInput) idInput.value = this.dataset.id || '';
      const catSelect = document.getElementById('incomesCategory');
      if (catSelect) {
        catSelect.value = '';
        Array.from(catSelect.options).forEach(opt => {
          if (opt.value == this.dataset.category) opt.selected = true;
        });
      }
      const amountInput = document.getElementById('income_amount');
      if (amountInput) amountInput.value = this.dataset.amount || '';
      const dateInput = document.getElementById('income_date');
      if (dateInput) dateInput.value = this.dataset.date || '';
      const descInput = document.getElementById('income_description');
      if (descInput) descInput.value = this.dataset.description || '';
      const form = document.getElementById('incomeForm');
      if (form) form.action = '/incomes/edit';
      openCustomModal('customAddIncomeModal');
    });
  });
  document.querySelectorAll('[onclick*="openCustomModal(\'customAddExpenseModal\')"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      setExpenseModalHeader(false);
      const idInput = document.getElementById('expense_id');
      if (idInput) idInput.value = '';
      const dateInput = document.getElementById('date');
      if (dateInput && !dateInput.value) dateInput.value = getTodayDate();
      const form = document.getElementById('expenseForm');
      if (form) form.action = '/transactions/add';
    });
  });
  document.querySelectorAll('[onclick*="openCustomModal(\'customAddIncomeModal\')"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      setIncomeModalHeader(false);
      const idInput = document.getElementById('income_id');
      if (idInput) idInput.value = '';
      const amountInput = document.getElementById('income_amount');
      if (amountInput) amountInput.value = '';
      const dateInput = document.getElementById('income_date');
      if (dateInput) dateInput.value = getTodayDate();
      const descInput = document.getElementById('income_description');
      if (descInput) descInput.value = '';
      const catSelect = document.getElementById('incomesCategory');
      if (catSelect) catSelect.selectedIndex = 0;
      const form = document.getElementById('incomeForm');
      if (form) form.action = '/transactions/add';
    });
  });
  document.body.addEventListener('click', function(e) {
    const icon = e.target.closest('.delete-icon');
    if (icon) {
      const type = icon.dataset.type;
      const id = icon.dataset.id;
      const description = icon.dataset.description;
      if (id && description !== undefined) {
        if (confirm(`Are you sure you want to delete "${description}"?`)) {
          if (type === 'Income') {
            const form = document.getElementById('deleteIncomeForm');
            const input = document.getElementById('deleteIncomeId');
            if (form && input) {
              input.value = id;
              form.submit();
            }
          } else if (type === 'Expense') {
            const form = document.getElementById('deleteExpenseForm');
            const input = document.getElementById('deleteExpenseId');
            if (form && input) {
              input.value = id;
              form.submit();
            }
          }
        }
      }
    }
  });
});
