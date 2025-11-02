<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>

<section id="plannerPanel" class="py-4">
  <div class="container">
    <h1 class="mb-4 text-center h2">📊 Budget Planner & Analyzer</h1>
    <p class="text-center text-muted mb-5">Track your spending against category limits - <?php echo date('F Y'); ?></p>

    <?php if (!empty($categoriesWithLimits)): ?>
      
      <!-- Progress Bars Dashboard (Opcja 1) -->
      <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
          <h3 class="h5 mb-0">💰 Category Budget Overview</h3>
        </div>
        <div class="card-body">
          <?php foreach ($categoriesWithLimits as $category): ?>
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                  <?php if ($category['status'] === 'exceeded'): ?>
                    <span class="badge bg-danger ms-2">EXCEEDED!</span>
                  <?php elseif ($category['status'] === 'warning'): ?>
                    <span class="badge bg-warning text-dark ms-2">WARNING</span>
                  <?php endif; ?>
                </div>
                <div class="text-end">
                  <span class="fw-bold"><?php echo number_format($category['spent'], 2); ?></span> / 
                  <span class="text-muted"><?php echo number_format($category['limit'], 2); ?> PLN</span>
                </div>
              </div>
              
              <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-<?php 
                  echo $category['status'] === 'exceeded' ? 'danger' : 
                       ($category['status'] === 'warning' ? 'warning' : 'success'); 
                ?>" 
                role="progressbar" 
                style="width: <?php echo min($category['percentage'], 100); ?>%;" 
                aria-valuenow="<?php echo $category['percentage']; ?>" 
                aria-valuemin="0" 
                aria-valuemax="100">
                  <span class="fw-bold"><?php echo number_format($category['percentage'], 1); ?>%</span>
                </div>
              </div>
              
              <?php if ($category['status'] === 'exceeded'): ?>
                <small class="text-danger">
                  ⚠️ Over budget by <?php echo number_format($category['spent'] - $category['limit'], 2); ?> PLN
                </small>
              <?php elseif ($category['status'] === 'warning'): ?>
                <small class="text-warning">
                  ⚠️ Approaching limit - <?php echo number_format($category['limit'] - $category['spent'], 2); ?> PLN remaining
                </small>
              <?php else: ?>
                <small class="text-success">
                  ✓ <?php echo number_format($category['limit'] - $category['spent'], 2); ?> PLN remaining
                </small>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Category Timeline Chart (Opcja 3) -->
      <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
          <h3 class="h5 mb-0">📈 Category Spending Timeline</h3>
        </div>
        <div class="card-body">
          <form method="GET" action="/planner" class="mb-4">
            <div class="row">
              <div class="col-md-8">
                <label for="category_id" class="form-label fw-bold">Select Category to Analyze:</label>
                <select class="form-select" id="category_id" name="category_id" onchange="this.form.submit()">
                  <option value="">-- Choose a category --</option>
                  <?php foreach ($categoriesWithLimits as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                      <?php echo ($selectedCategoryId === $category['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($category['name']); ?> 
                      (Limit: <?php echo number_format($category['limit'], 2); ?> PLN)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </form>

          <?php if ($selectedCategoryId && $timelineData): ?>
            <div class="mb-3">
              <h5 class="text-center">
                <?php echo htmlspecialchars($selectedCategoryName); ?> - Monthly Spending Trend
              </h5>
              <p class="text-center text-muted">
                Cumulative spending throughout <?php echo date('F Y'); ?>
              </p>
            </div>
            
            <canvas id="timelineChart" width="400" height="150"></canvas>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('timelineChart').getContext('2d');
                
                // Dane z PHP
                const timelineData = <?php echo json_encode($timelineData); ?>;
                const categoryLimit = <?php 
                  foreach ($categoriesWithLimits as $cat) {
                    if ($cat['id'] === $selectedCategoryId) {
                      echo $cat['limit'];
                      break;
                    }
                  }
                ?>;
                
                // Przygotuj dane dla wykresu
                const labels = timelineData.map(item => {
                  const date = new Date(item.date);
                  return date.getDate(); // dzień miesiąca
                });
                
                const spentData = timelineData.map(item => item.amount);
                const limitData = timelineData.map(() => categoryLimit);
                
                new Chart(ctx, {
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
              });
            </script>
          <?php else: ?>
            <div class="alert alert-info text-center">
              <i class="bi bi-info-circle me-2"></i>
              Select a category from the dropdown above to view spending timeline
            </div>
          <?php endif; ?>
        </div>
      </div>

    <?php else: ?>
      <div class="alert alert-warning text-center">
        <h4><i class="bi bi-exclamation-triangle me-2"></i>No Budget Limits Set</h4>
        <p>You haven't set any category limits yet. Go to <a href="/settings" class="alert-link">Settings</a> to set monthly spending limits for your expense categories.</p>
        <a href="/settings" class="btn btn-primary mt-2">Go to Settings</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>