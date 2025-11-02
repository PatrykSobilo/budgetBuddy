/**
 * Planner Timeline Chart
 * Category spending timeline with Chart.js
 */

/**
 * Initialize planner timeline chart
 * @param {Array} timelineData - Daily spending data from PHP [{date, amount}, ...]
 * @param {number} categoryLimit - Budget limit for selected category
 */
function initializePlannerChart(timelineData, categoryLimit) {
  const ctx = document.getElementById('timelineChart');
  if (!ctx || !timelineData || timelineData.length === 0) {
    return;
  }

  // Prepare data for chart
  const labels = timelineData.map(item => {
    const date = new Date(item.date);
    return date.getDate(); // day of month
  });

  const spentData = timelineData.map(item => item.amount);
  const limitData = timelineData.map(() => categoryLimit);

  new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Cumulative Spending',
          data: spentData,
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1,
          fill: true
        },
        {
          label: 'Budget Limit',
          data: limitData,
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.1)',
          borderDash: [5, 5],
          tension: 0,
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              label += context.parsed.y.toFixed(2) + ' PLN';
              return label;
            }
          }
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Day of Month'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Amount (PLN)'
          },
          ticks: {
            callback: function(value) {
              return value.toFixed(0) + ' PLN';
            }
          }
        }
      }
    }
  });
}

/**
 * Scroll to timeline section (instant, no animation)
 */
function scrollToTimelineSection() {
  if (window.location.hash === '#timelineSection') {
    const element = document.getElementById('timelineSection');
    if (element) {
      const elementRect = element.getBoundingClientRect();
      const absoluteElementTop = elementRect.top + window.pageYOffset;
      const middle = absoluteElementTop - (window.innerHeight / 2) + (elementRect.height / 2);
      window.scrollTo({ top: middle, behavior: 'instant' });
    }
  }
}

// Auto-scroll on page load
document.addEventListener('DOMContentLoaded', scrollToTimelineSection);
