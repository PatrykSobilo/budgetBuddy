<?php 
$pageScripts = ['charts-dashboards.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4 h2">Dashboards</h1>
</div>

<section id="userParameters" name="userParameters">
    <div class="container">
        <?php 
        $action = '/dashboards';
        $method = 'POST';
        $csrfToken = $csrfToken ?? ($_SESSION['token'] ?? '');
        $startDateName = 'startingDate';
        $endDateName = 'endingDate';
        $submitLabel = 'Show Balance';
        $onChangeHandler = 'toggleCustomDatesDashboard';
        include $this->resolve("partials/_periodFilter.php");
        ?>
    </div>
</section>



<section id="summary" name="summary">
    <div id="generalSummary" class="generalSummary container d-flex flex-column align-items-center justify-content-center border">
        <div class="d-flex flex-column align-items-center w-100">
            <div class="table-container w-auto">
                <div class="container mt-5 text-center">
                    <h2 class="mb-4 justify-content-center">Balance</h2>
                    <div class="table-responsive">
                    <table class="table table-bordered mx-auto" name="balance" style="width:auto;">
                        <thead>
                            <tr>
                                <th>Expenses</th>
                                <th>Incomes</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                $service = $transactionService ?? null;
                                $startDate = $startDate ?? null;
                                $endDate = $endDate ?? null;
                                $summary = $service ? $service->calculateTransactions($startDate, $endDate) : ['expenses' => 0, 'incomes' => 0, 'balance' => 0];
                                ?>
                                <td><?php echo number_format($summary['expenses'], 2, '.', ' '); ?></td>
                                <td><?php echo number_format($summary['incomes'], 2, '.', ' '); ?></td>
                                <td><?php echo number_format($summary['balance'], 2, '.', ' '); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="container mt-4 mb-4 text-center">
                    <h4 class="mb-3">Expenses vs Incomes</h4>
                    <canvas id="summaryPieChart" width="300" height="300" style="display: block; margin: 0 auto;"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            </div>
        </div>
    </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>

<script>
  // Initialize dashboard chart when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    initializeSummaryChart(
      <?php echo json_encode($summary['expenses']); ?>,
      <?php echo json_encode($summary['incomes']); ?>
    );
  });
</script>