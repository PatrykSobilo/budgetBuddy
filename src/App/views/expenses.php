<?php 
$pageScripts = ['charts-expenses-incomes.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>

<section id="historyExpensesPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h1 class="mb-4 text-center h2">Expenses</h1>
            
            <!-- Period Filter -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="/expenses" class="row g-3">
                        <div class="col-md-4">
                            <label for="period" class="form-label">Period</label>
                            <select class="form-select" id="period" name="period" onchange="toggleCustomDates()">
                                <option value="all" <?php echo (!isset($_GET['period']) || $_GET['period'] === 'all') ? 'selected' : ''; ?>>All Time</option>
                                <option value="current_month" <?php echo (isset($_GET['period']) && $_GET['period'] === 'current_month') ? 'selected' : ''; ?>>Current Month</option>
                                <option value="last_month" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                                <option value="last_30_days" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_30_days') ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="last_90_days" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_90_days') ? 'selected' : ''; ?>>Last 90 Days</option>
                                <option value="current_year" <?php echo (isset($_GET['period']) && $_GET['period'] === 'current_year') ? 'selected' : ''; ?>>Current Year</option>
                                <option value="custom" <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="startDateDiv" style="display: <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'block' : 'none'; ?>;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3" id="endDateDiv" style="display: <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'block' : 'none'; ?>;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            

            
            <!-- Search Box -->
            <?php include $this->resolve("partials/_searchForm.php"); ?>
            
            <!-- Charts Section -->
            <?php if (!empty($chartData['labels'])): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-bold text-center mb-3" style="font-size: 1.25rem;">Bar Chart - Expenses by Categories</div>
                            <canvas id="expensesBarChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-bold text-center mb-3" style="font-size: 1.25rem;">Pie Chart - Expenses by Category</div>
                            <canvas id="expensesPieChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
              // Initialize charts when DOM is ready
              document.addEventListener('DOMContentLoaded', function() {
                initializeTransactionCharts(
                  'expenses',
                  <?php echo json_encode($chartData['labels']); ?>,
                  <?php echo json_encode($chartData['data']); ?>
                );
              });
            </script>
            <?php endif; ?>
            
            <div class="table-responsive">
            <table class="table table-bordered table-transactions">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Payment Method</th>
                        <th class="amount-col">Amount</th>
                        <th class="date-col">Date</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php
                                $catName = '';
                                if (!empty($_SESSION['expenseCategories'])) {
                                    foreach ($_SESSION['expenseCategories'] as $cat) {
                                        if ($cat['id'] == ($expense['expense_category_assigned_to_user_id'] ?? null)) {
                                            $catName = $cat['name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($catName);
                            ?></td>
                            <td><?php
                                $payName = '';
                                if (!empty($_SESSION['paymentMethods'])) {
                                    foreach ($_SESSION['paymentMethods'] as $method) {
                                        if ($method['id'] == ($expense['payment_method_assigned_to_user_id'] ?? null)) {
                                            $payName = $method['name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($payName);
                            ?></td>
                            <td class="amount-col"><?php echo number_format($expense['amount'], 2, '.', ' '); ?></td>
                            <td class="date-col"><?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?></td>
                            <td class="actions-col">
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-icon"
                                    data-id="<?php echo htmlspecialchars($expense['id'] ?? ''); ?>"
                                    data-description="<?php echo htmlspecialchars($expense['description']); ?>"
                                    data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                    data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?>"
                                    data-category="<?php echo htmlspecialchars($expense['expense_category_assigned_to_user_id'] ?? ''); ?>"
                                    data-payment="<?php echo htmlspecialchars($expense['payment_method_assigned_to_user_id'] ?? ''); ?>"
                                >
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-id="<?php echo htmlspecialchars($expense['id'] ?? ''); ?>" data-description="<?php echo htmlspecialchars($expense['description']); ?>" data-type="Expense">
  <i class="bi bi-trash"></i>
</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</section>

<form id="deleteExpenseForm" method="POST" action="/expenses/delete" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
  <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
</form>

<?php include $this->resolve("partials/_footer.php"); ?>