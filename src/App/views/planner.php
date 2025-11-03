<?php 
$pageScripts = ['charts-planner.js', 'category-limits.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken]); ?>

<section id="plannerPanel" class="py-4">
  <div class="container">
    <h1 class="mb-5 text-center h2">Planner & Analyzer</h1>
    <?php if (!empty($categoriesWithLimits)): ?>
      
      <!-- Progress Bars Dashboard (Opcja 1) -->
      <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
          <h3 class="h5 mb-0">Category Budget Overview</h3>
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
      <div class="card shadow-sm" id="timelineSection">
        <div class="card-header bg-primary text-white">
          <h3 class="h5 mb-0">Category Spending Timeline</h3>
        </div>
        <div class="card-body">
          <form method="GET" action="/planner#timelineSection" class="mb-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="category_id" class="form-label fw-bold">Select Category to Analyze:</label>
                <select class="form-select" id="category_id" name="category_id" onchange="this.form.submit()">
                  <option value="">-- Choose a category --</option>
                  <option value="all" <?php echo ($selectedCategoryId === 'all') ? 'selected' : ''; ?>>
                    🎯 All Categories (Overview)
                  </option>
                  <?php foreach ($categoriesWithLimits as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                      <?php echo ($selectedCategoryId === $category['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($category['name']); ?> 
                      (Limit: <?php echo number_format($category['limit'], 2); ?> PLN)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="timeline_month" class="form-label fw-bold">Select Month:</label>
                <select class="form-select" id="timeline_month" name="timeline_month" onchange="this.form.submit()">
                  <?php
                  $currentMonth = $selectedMonth ?? date('Y-m');
                  for ($i = 0; $i < 12; $i++) {
                    $monthDate = date('Y-m', strtotime("-$i months"));
                    $monthLabel = date('F Y', strtotime($monthDate . '-01'));
                    $selected = ($monthDate === $currentMonth) ? 'selected' : '';
                    echo "<option value=\"{$monthDate}\" {$selected}>{$monthLabel}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </form>

          <?php if ($selectedCategoryId && $timelineData): ?>
            <div class="mb-3">
              <h5 class="text-center">
                <?php echo $selectedCategoryId === 'all' ? 'All Categories' : htmlspecialchars($selectedCategoryName); ?> - Monthly Spending Trend
              </h5>
              <p class="text-center text-muted">
                Cumulative spending throughout <?php echo date('F Y', strtotime($selectedMonth . '-01')); ?>
              </p>
            </div>
            
            <canvas id="timelineChart" width="400" height="<?php echo $selectedCategoryId === 'all' ? '250' : '150'; ?>"></canvas>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const timelineData = <?php echo json_encode($timelineData); ?>;
                console.log('Timeline Data:', timelineData);
                <?php if ($selectedCategoryId === 'all'): ?>
                const categoriesData = <?php echo json_encode($categoriesWithLimits); ?>;
                console.log('Categories Data:', categoriesData);
                console.log('Calling initializeMultiCategoryChart');
                if (typeof initializeMultiCategoryChart === 'function') {
                  initializeMultiCategoryChart(timelineData, categoriesData);
                } else {
                  console.error('initializeMultiCategoryChart is not defined!');
                }
                <?php else: ?>
                const categoryLimit = <?php 
                  foreach ($categoriesWithLimits as $cat) {
                    if ($cat['id'] === $selectedCategoryId) {
                      echo $cat['limit'];
                      break;
                    }
                  }
                ?>;
                initializePlannerChart(timelineData, categoryLimit);
                <?php endif; ?>
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