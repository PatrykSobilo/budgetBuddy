/**
 * Dashboards Charts
 * Balance summary and period selection
 */

/**
 * Toggle custom date inputs for dashboards
 */
function toggleCustomDatesDashboard() {
  const period = document.getElementById('period').value;
  const startDateDiv = document.getElementById('startingDateDiv');
  const endDateDiv = document.getElementById('endingDateDiv');
  if (period === 'custom') {
    startDateDiv.style.display = 'block';
    endDateDiv.style.display = 'block';
  } else {
    startDateDiv.style.display = 'none';
    endDateDiv.style.display = 'none';
  }
}

/**
 * Initialize current month button handler
 */
function initializeCurrentMonthButton() {
  const currentMonthBtn = document.getElementById('currentMonthBtn');
  const dateForm = document.getElementById('dateForm');
  
  if (currentMonthBtn && dateForm) {
    currentMonthBtn.addEventListener('click', function() {
      const now = new Date();
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
      const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
      const pad = n => n < 10 ? '0' + n : n;
      const yyyy = now.getFullYear();
      const mm = pad(now.getMonth() + 1);
      
      document.getElementById('startingDate').value = `${yyyy}-${mm}-01`;
      document.getElementById('endingDate').value = `${yyyy}-${mm}-${pad(lastDay.getDate())}`;
      dateForm.submit();
    });
  }
}

/**
 * Initialize summary pie chart
 * @param {number} expenses - Total expenses from PHP
 * @param {number} incomes - Total incomes from PHP
 */
function initializeSummaryChart(expenses, incomes) {
  const ctx = document.getElementById('summaryPieChart');
  if (!ctx) {
    return;
  }

  new Chart(ctx.getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Expenses', 'Incomes'],
      datasets: [{
        data: [expenses, incomes],
        backgroundColor: ['#ff6384', '#36a2eb'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: false,
      plugins: {
        legend: {
          position: 'bottom'
        },
        title: {
          display: false
        }
      }
    }
  });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
  initializeCurrentMonthButton();
});
