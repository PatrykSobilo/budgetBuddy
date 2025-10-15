<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
<?php include $this->resolve("partials/_searchForm.php"); ?>

<section id="historyPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Incomes</h2>
            
            <!-- Period Filter -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="/incomes" class="row g-3">
                        <div class="col-md-4">
                            <label for="period" class="form-label">Period</label>
                            <select class="form-select" id="period" name="period" onchange="toggleCustomDatesIncomes()">
                                <option value="all" <?php echo (!isset($_GET['period']) || $_GET['period'] === 'all') ? 'selected' : ''; ?>>All Time</option>
                                <option value="current_month" <?php echo (isset($_GET['period']) && $_GET['period'] === 'current_month') ? 'selected' : ''; ?>>Current Month</option>
                                <option value="last_month" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                                <option value="last_30_days" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_30_days') ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="last_90_days" <?php echo (isset($_GET['period']) && $_GET['period'] === 'last_90_days') ? 'selected' : ''; ?>>Last 90 Days</option>
                                <option value="current_year" <?php echo (isset($_GET['period']) && $_GET['period'] === 'current_year') ? 'selected' : ''; ?>>Current Year</option>
                                <option value="custom" <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="startDateDivIncomes" style="display: <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'block' : 'none'; ?>;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3" id="endDateDivIncomes" style="display: <?php echo (isset($_GET['period']) && $_GET['period'] === 'custom') ? 'block' : 'none'; ?>;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            function toggleCustomDatesIncomes() {
                const period = document.getElementById('period').value;
                const startDateDiv = document.getElementById('startDateDivIncomes');
                const endDateDiv = document.getElementById('endDateDivIncomes');
                if (period === 'custom') {
                    startDateDiv.style.display = 'block';
                    endDateDiv.style.display = 'block';
                } else {
                    startDateDiv.style.display = 'none';
                    endDateDiv.style.display = 'none';
                }
            }
            </script>
            <table class="table table-bordered table-transactions">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th class="amount-col">Amount</th>
                        <th class="date-col">Date</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($incomes)): ?>
                    <?php foreach ($incomes as $income): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($income['description']); ?></td>
                            <td><?php
                                $catName = '';
                                if (!empty($_SESSION['incomeCategories'])) {
                                    foreach ($_SESSION['incomeCategories'] as $cat) {
                                        if ($cat['id'] == ($income['income_category_assigned_to_user_id'] ?? null)) {
                                            $catName = $cat['name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($catName);
                            ?></td>
                            <td class="amount-col"><?php echo number_format($income['amount'], 2, '.', ' '); ?></td>
                            <td class="date-col"><?php echo htmlspecialchars(date('Y-m-d', strtotime($income['date']))); ?></td>
                            <td class="actions-col">
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-income-icon"
                                    data-description="<?php echo htmlspecialchars($income['description'] ?? ''); ?>"
                                    data-amount="<?php echo htmlspecialchars($income['amount'] ?? ''); ?>"
                                    data-date="<?php echo htmlspecialchars(isset($income['date']) ? date('Y-m-d', strtotime($income['date'])) : ''); ?>"
                                    data-category="<?php echo htmlspecialchars($income['income_category_assigned_to_user_id'] ?? ''); ?>"
                                    data-id="<?php echo htmlspecialchars($income['id'] ?? ''); ?>"
                                >
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-id="<?php echo htmlspecialchars($income['id'] ?? ''); ?>" data-description="<?php echo htmlspecialchars($income['description'] ?? ''); ?>" data-type="Income">
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
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<form id="deleteIncomeForm" method="POST" action="/incomes/delete" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
  <input type="hidden" name="income_id" id="deleteIncomeId" value="">
</form>

<?php include $this->resolve("partials/_footer.php"); ?>