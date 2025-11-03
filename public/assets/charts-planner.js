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

/**
 * Initialize multi-category timeline chart
 * @param {Object} timelineData - Data from PHP {dates: [], categories: [{name, limit, data}, ...]}
 * @param {Array} categoriesData - Categories with limits info
 */
function initializeMultiCategoryChart(timelineData, categoriesData) {
  const ctx = document.getElementById('timelineChart');
  if (!ctx || !timelineData || !timelineData.categories || timelineData.categories.length === 0) {
    return;
  }

  // Prepare labels (days of month)
  const labels = timelineData.dates.map(date => {
    const d = new Date(date);
    return d.getDate();
  });

  // Color palette for categories
  const colors = [
    { border: 'rgb(75, 192, 192)', bg: 'rgba(75, 192, 192, 0.2)' },
    { border: 'rgb(255, 99, 132)', bg: 'rgba(255, 99, 132, 0.2)' },
    { border: 'rgb(54, 162, 235)', bg: 'rgba(54, 162, 235, 0.2)' },
    { border: 'rgb(255, 206, 86)', bg: 'rgba(255, 206, 86, 0.2)' },
    { border: 'rgb(153, 102, 255)', bg: 'rgba(153, 102, 255, 0.2)' },
    { border: 'rgb(255, 159, 64)', bg: 'rgba(255, 159, 64, 0.2)' },
    { border: 'rgb(199, 199, 199)', bg: 'rgba(199, 199, 199, 0.2)' },
    { border: 'rgb(83, 102, 255)', bg: 'rgba(83, 102, 255, 0.2)' },
    { border: 'rgb(40, 159, 64)', bg: 'rgba(40, 159, 64, 0.2)' },
    { border: 'rgb(210, 199, 199)', bg: 'rgba(210, 199, 199, 0.2)' }
  ];

  // Create datasets for each category
  const datasets = [];
  
  timelineData.categories.forEach((category, index) => {
    const colorIndex = index % colors.length;
    
    // Spending line
    datasets.push({
      label: `${category.name} (Spending)`,
      data: category.data,
      borderColor: colors[colorIndex].border,
      backgroundColor: colors[colorIndex].bg,
      tension: 0.3,
      fill: false,
      borderWidth: 2
    });
    
    // Limit line (dashed)
    datasets.push({
      label: `${category.name} (Limit)`,
      data: Array(labels.length).fill(category.limit),
      borderColor: colors[colorIndex].border,
      backgroundColor: 'transparent',
      borderDash: [5, 5],
      tension: 0,
      fill: false,
      borderWidth: 1,
      pointRadius: 0
    });
  });

  new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: datasets
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
          labels: {
            boxWidth: 12,
            font: {
              size: 10
            },
            filter: function(item, chart) {
              // Show only spending lines in legend (hide limit lines)
              return !item.text.includes('(Limit)');
            }
          }
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
