/**
 * Charts for Expenses and Incomes pages
 * Handles period selection and Chart.js visualizations
 */

/**
 * Toggle custom date inputs based on period selection
 */
function toggleCustomDates() {
  const period = document.getElementById('period').value;
  const startDateDiv = document.getElementById('startDateDiv');
  const endDateDiv = document.getElementById('endDateDiv');
  if (period === 'custom') {
    startDateDiv.style.display = 'block';
    endDateDiv.style.display = 'block';
  } else {
    startDateDiv.style.display = 'none';
    endDateDiv.style.display = 'none';
  }
}

/**
 * Initialize charts for transactions (Expenses or Incomes)
 * @param {string} type - 'expenses' or 'incomes'
 * @param {Array} labels - Category labels from PHP
 * @param {Array} data - Amounts data from PHP
 */
function initializeTransactionCharts(type, labels, data) {
  if (!labels || !data || labels.length === 0) {
    return;
  }

  // Generate colors
  const colors = [
    'rgba(255, 99, 132, 0.7)',
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 206, 86, 0.7)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)',
    'rgba(255, 159, 64, 0.7)',
    'rgba(199, 199, 199, 0.7)',
    'rgba(83, 102, 255, 0.7)',
    'rgba(255, 99, 255, 0.7)',
    'rgba(99, 255, 132, 0.7)'
  ];

  const barCanvasId = type === 'expenses' ? 'expensesBarChart' : 'incomesBarChart';
  const pieCanvasId = type === 'expenses' ? 'expensesPieChart' : 'incomesPieChart';

  // Bar Chart
  const barCtx = document.getElementById(barCanvasId);
  if (barCtx) {
    new Chart(barCtx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Amount (PLN)',
          data: data,
          backgroundColor: colors.slice(0, data.length),
          borderColor: colors.slice(0, data.length).map(c => c.replace('0.7', '1')),
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return value.toFixed(2) + ' PLN';
              }
            }
          }
        }
      }
    });
  }

  // Pie Chart
  const pieCtx = document.getElementById(pieCanvasId);
  if (pieCtx) {
    new Chart(pieCtx.getContext('2d'), {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors.slice(0, data.length),
          borderColor: '#ffffff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.parsed.toFixed(2) + ' PLN';
                return label;
              }
            }
          }
        }
      }
    });
  }
}
