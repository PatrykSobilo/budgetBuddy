/**
 * Category Limits - Asynchronous checking and warnings
 * budgetBuddy - Killer Feature
 */

// Debounce helper function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Sprawdza limit kategorii asynchronicznie z retry mechanism
 * @param {number} categoryId - ID kategorii wydatków
 * @param {number} amount - Kwota wydatku
 * @param {number|null} expenseId - ID wydatku (dla edycji, null dla nowego)
 * @param {number} retries - Liczba ponownych prób (domyślnie 2)
 * @returns {Promise<Object>} Dane o stanie limitu
 */
async function checkCategoryLimit(categoryId, amount, expenseId = null, retries = 2) {
  try {
    const params = new URLSearchParams({
      category_id: categoryId,
      amount: amount
    });
    
    if (expenseId) {
      params.append('expense_id', expenseId);
    }

    const response = await fetch(`/api/check-category-limit?${params.toString()}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      if (response.status >= 500 && retries > 0) {
        // Server error - retry after delay
        await new Promise(resolve => setTimeout(resolve, 1000));
        return checkCategoryLimit(categoryId, amount, expenseId, retries - 1);
      }
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data;
  } catch (error) {
    if (retries > 0 && error.name === 'TypeError') {
      // Network error - retry after delay
      console.warn(`Network error, retrying... (${retries} attempts left)`);
      await new Promise(resolve => setTimeout(resolve, 1000));
      return checkCategoryLimit(categoryId, amount, expenseId, retries - 1);
    }
    
    console.error('Error checking category limit:', error);
    return {
      error: true,
      message: 'Unable to check budget limit. Please try again.',
      details: error.message
    };
  }
}

/**
 * Wyświetla ostrzeżenie o limitzie w formularzu
 * @param {string} containerId - ID kontenera na alert
 * @param {Object} limitData - Dane z API o limicie
 */
function displayLimitWarning(containerId, limitData) {
  const container = document.getElementById(containerId);
  if (!container) return;

  // Usuń poprzednie ostrzeżenie
  container.innerHTML = '';

  // Obsługa błędu API
  if (limitData && limitData.error) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-warning d-flex align-items-center mb-3';
    alertDiv.setAttribute('role', 'alert');
    
    const icon = document.createElement('i');
    icon.className = 'bi bi-wifi-off me-2';
    
    const messageDiv = document.createElement('div');
    messageDiv.innerHTML = `
      <strong>Connection Issue</strong><br>
      ${limitData.message}
      <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="location.reload()">
        Retry
      </button>
    `;
    
    alertDiv.appendChild(icon);
    alertDiv.appendChild(messageDiv);
    container.appendChild(alertDiv);
    return;
  }

  if (!limitData || !limitData.hasLimit || limitData.status === 'ok') {
    return;
  }

  // Utwórz alert
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${limitData.level} d-flex align-items-center mb-3`;
  alertDiv.setAttribute('role', 'alert');
  
  // Ikona
  const icon = document.createElement('i');
  icon.className = limitData.level === 'danger' 
    ? 'bi bi-exclamation-triangle-fill me-2' 
    : 'bi bi-exclamation-circle-fill me-2';
  
  // Treść wiadomości
  const messageDiv = document.createElement('div');
  messageDiv.innerHTML = `
    <strong>${limitData.status === 'exceeded' ? 'Budget Exceeded!' : 'Budget Warning!'}</strong><br>
    ${limitData.message}<br>
    <small class="text-muted">
      Current total: ${limitData.newTotal.toFixed(2)} PLN / Limit: ${limitData.limit.toFixed(2)} PLN
    </small>
  `;

  alertDiv.appendChild(icon);
  alertDiv.appendChild(messageDiv);
  container.appendChild(alertDiv);

  // Scroll do ostrzeżenia
  container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Inicjalizuje sprawdzanie limitu dla formularza Add/Edit Expense
 */
function initExpenseLimitCheck() {
  const categorySelect = document.getElementById('expensesCategory');
  const amountInput = document.getElementById('expenseAmount');
  const expenseIdInput = document.getElementById('expense_id');
  const warningContainer = document.getElementById('addExpenseLimitWarning');

  if (!categorySelect || !amountInput || !warningContainer) {
    return;
  }

  const checkLimit = debounce(async () => {
    const categoryId = parseInt(categorySelect.value);
    const amount = parseFloat(amountInput.value);
    const expenseId = expenseIdInput && expenseIdInput.value ? parseInt(expenseIdInput.value) : null;

    if (!categoryId || !amount || amount <= 0) {
      warningContainer.innerHTML = '';
      return;
    }

    const limitData = await checkCategoryLimit(categoryId, amount, expenseId);
    displayLimitWarning('addExpenseLimitWarning', limitData);
  }, 500);

  // Nasłuchuj zmian
  categorySelect.addEventListener('change', checkLimit);
  amountInput.addEventListener('input', checkLimit);
}

// Inicjalizacja po załadowaniu DOM
document.addEventListener('DOMContentLoaded', function() {
  initExpenseLimitCheck();
});

// Export dla użycia w innych skryptach
if (typeof window !== 'undefined') {
  window.CategoryLimits = {
    check: checkCategoryLimit,
    displayWarning: displayLimitWarning
  };
}
