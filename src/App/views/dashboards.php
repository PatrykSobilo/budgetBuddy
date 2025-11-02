<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4 h2">Dashboards</h1>
</div>

<section id="userParameters" name="userParameters">
    <div class="container">
        <form id="dateForm" method="post" action="/dashboards" class="d-flex flex-wrap align-items-center justify-content-center">
            <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
            
            <!-- Period Selection -->
            <div class="m-3 text-center">
                <label for="period">Period</label>
                <select class="form-select" id="period" name="period" onchange="toggleCustomDatesDashboard()">
                    <option value="all" <?php echo (!isset($_POST['period']) || $_POST['period'] === 'all') ? 'selected' : ''; ?>>All Time</option>
                    <option value="current_month" <?php echo (isset($_POST['period']) && $_POST['period'] === 'current_month') ? 'selected' : ''; ?>>Current Month</option>
                    <option value="last_month" <?php echo (isset($_POST['period']) && $_POST['period'] === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="last_30_days" <?php echo (isset($_POST['period']) && $_POST['period'] === 'last_30_days') ? 'selected' : ''; ?>>Last 30 Days</option>
                    <option value="last_90_days" <?php echo (isset($_POST['period']) && $_POST['period'] === 'last_90_days') ? 'selected' : ''; ?>>Last 90 Days</option>
                    <option value="current_year" <?php echo (isset($_POST['period']) && $_POST['period'] === 'current_year') ? 'selected' : ''; ?>>Current Year</option>
                    <option value="custom" <?php echo (isset($_POST['period']) && $_POST['period'] === 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                </select>
            </div>
            
            <!-- Custom Date Range -->
            <div class="m-3 text-center" id="startingDateDiv" style="display: <?php echo (isset($_POST['period']) && $_POST['period'] === 'custom') ? 'block' : 'none'; ?>;">
                <label for="startingDate">Starting Date</label>
                <input type="date" class="form-control" id="startingDate" name="startingDate" placeholder="mm/dd/yyyy" value="<?php echo htmlspecialchars($_POST['startingDate'] ?? ''); ?>">
            </div>
            <div class="m-3 text-center" id="endingDateDiv" style="display: <?php echo (isset($_POST['period']) && $_POST['period'] === 'custom') ? 'block' : 'none'; ?>;">
                <label for="endingDate">Ending Date</label>
                <input type="date" class="form-control" id="endingDate" name="endingDate" placeholder="mm/dd/yyyy" value="<?php echo htmlspecialchars($_POST['endingDate'] ?? ''); ?>">
            </div>
            
            <div class="m-3 text-center d-flex flex-column">
                <label class="invisible">Action</label>
                <button type="submit" class="btn btn-primary">Show Balance</button>
            </div>
        </form>
    </div>
</section>

<script>
function toggleCustomDatesDashboard() {
    const period = document.getElementById('period').value;
    const startDateDiv = document.getElementById('startingDateDiv');
    const endDateDiv = document.getElementById('endingDateDiv');
    if (period === 'custom') {
        startDateDiv.style.display = 'block';
        endDateDiv.style.display = 'block';
    } else {
        startDateDiv.style.display = 'none';
        endDateDiv.style.display = 'none';
    }
}
</script>

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
    document.addEventListener('DOMContentLoaded', function() {
        var currentMonthBtn = document.getElementById('currentMonthBtn');
        var dateForm = document.getElementById('dateForm');
        if (currentMonthBtn && dateForm) {
            currentMonthBtn.addEventListener('click', function() {
                var now = new Date();
                var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                var lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                var pad = n => n < 10 ? '0' + n : n;
                var yyyy = now.getFullYear();
                var mm = pad(now.getMonth() + 1);
                document.getElementById('startingDate').value = `${yyyy}-${mm}-01`;
                document.getElementById('endingDate').value = `${yyyy}-${mm}-${pad(lastDay.getDate())}`;
                dateForm.submit();
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('summaryPieChart').getContext('2d');
        var expenses = <?php echo json_encode($summary['expenses']); ?>;
        var incomes = <?php echo json_encode($summary['incomes']); ?>;
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Expenses', 'Incomes'],
                datasets: [{
                    data: [expenses, incomes],
                    backgroundColor: ['#ff6384', '#36a2eb'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    });
</script>