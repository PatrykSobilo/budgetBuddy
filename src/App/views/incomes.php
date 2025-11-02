<?php 
$pageScripts = ['charts-expenses-incomes.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken]); ?>

<section id="historyPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h1 class="mb-4 text-center h2">Incomes</h1>
            
            <!-- Period Filter -->
            <?php 
            $action = '/incomes';
            $method = 'GET';
            $onChangeHandler = 'toggleCustomDates';
            include $this->resolve("partials/_periodFilter.php"); 
            ?>
            
            <!-- Search Box -->
            <?php include $this->resolve("partials/_searchForm.php"); ?>
            
            <!-- Charts Section -->
            <?php if (!empty($chartData['labels'])): ?>
            <div class="row mb-4">
                <?php 
                $title = 'Bar Chart - Incomes by Categories';
                $canvasId = 'incomesBarChart';
                include $this->resolve("partials/_chartCard.php"); 
                ?>
                <?php 
                $title = 'Pie Chart - Incomes by Category';
                $canvasId = 'incomesPieChart';
                include $this->resolve("partials/_chartCard.php"); 
                ?>
            </div>
            
            <script>
              // Initialize charts when DOM is ready
              document.addEventListener('DOMContentLoaded', function() {
                initializeTransactionCharts(
                  'incomes',
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
                                if (!empty($incomeCategories)) {
                                    foreach ($incomeCategories as $cat) {
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
    </div>
</section>

<form id="deleteIncomeForm" method="POST" action="/incomes/delete" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken]); ?>
  <input type="hidden" name="income_id" id="deleteIncomeId" value="">
</form>

<?php include $this->resolve("partials/_footer.php"); ?>