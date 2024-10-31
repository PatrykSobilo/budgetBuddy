document.addEventListener('DOMContentLoaded', (event) => {
  var ctx = document.getElementById('myPieChart').getContext('2d');
  var myPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: ['Expenses', 'Incomes'],
          datasets: [{
              data: [3600, 9500],
              backgroundColor: ['#ff9999','#66b3ff'],
          }]
      },
      options: {
          responsive: true,
          plugins: {
              legend: {
                  position: 'top',
              },
              title: {
                  display: true,
                  text: 'Incomes/Expenses PieChart'
              }
          }
      },
  });
});