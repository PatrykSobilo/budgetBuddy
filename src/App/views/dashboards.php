<?php include $this->resolve("partials/_header.php"); ?>

<section id="userParameters" name="userParameters">
    <div class="container">
        <form id="dateForm" method="post" action="dashboards.php" class="d-flex flex-wrap align-items-center justify-content-center">
            <div class="m-5 text-center">
                <label for="startingDate">Starting Date</label>
                <input type="date" class="form-control" id="startingDate" name="startingDate" placeholder="mm/dd/yyyy">
            </div>
            <div class="m-5 text-center">
                <label for="endingDate">Ending Date</label>
                <input type="date" class="form-control" id="endingDate" name="endingDate" placeholder="mm/dd/yyyy">
            </div>
            <div class="m-5 text-center">
                <button type="submit" class="btn btn-primary">Show Balance</button>
            </div>
        </form>
    </div>
</section>

<section id="summary" name="summary">
    <div id="generalSummary" class="generalSummary container d-flex flex-wrap border">
        <div class="d-flex flex-row w-100">
            <div class="table-container">
                <div class="container mt-5">
                    <h2 class="mb-4 justify-content-center">Balance</h2>
                    <table class="table table-bordered" name="balance">
                        <thead>
                            <tr>
                                <th>Expenses</th>
                                <th>Incomes</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="chart-container mt-5 mb-5 ms-5">
                <div class="text-center">
                    <h2 class="mb-4">Expenses/Incomes Summary</h2>
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="summaryByCategories" name="summaryByCategories" class="mt-5 mb-5">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 justify-content-center">Balance by Categories</h2>
            <table class="table table-bordered" name="balance">
                <thead>
                    <tr>
                        <th>Category Type</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        var ctx = document.getElementById('myPieChart').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Expenses', 'Incomes'],
                datasets: [{
                    data: [<?php echo $expensesSummary; ?>, <?php echo $incomesSummary; ?>],
                    backgroundColor: ['#ff9999', '#66b3ff'],
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
</script>
</body>

</html>