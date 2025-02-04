<?php
session_start();

if (!isset($_SESSION['zalogowany'])) {
  header('Location: loginForm.php');
  exit();
}

require_once "connect.php";
$user_id = $_SESSION['id'];

$startingDate = isset($_POST['startingDate']) ? $_POST['startingDate'] : null;
$endingDate = isset($_POST['endingDate']) ? $_POST['endingDate'] : null;

try {
  $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
  if ($polaczenie->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    if ($startingDate && $endingDate) {
      $recentExpensesQuery = $polaczenie->query("SELECT expenses_category_assigned_to_users.name AS category_name, SUM(expenses.amount) AS expensesSummary FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id = '$user_id' AND expenses.date_of_expense BETWEEN '$startingDate' AND '$endingDate' GROUP BY expenses_category_assigned_to_users.name
      ");
      if (!$recentExpensesQuery) throw new Exception($polaczenie->error);

      $expensesByCategory = [];
      while ($row = $recentExpensesQuery->fetch_assoc()) {
        $expensesByCategory[$row['category_name']] = $row['expensesSummary'];
      }

      $recentIncomesQuery = $polaczenie->query("SELECT incomes_category_assigned_to_users.name AS category_name, SUM(incomes.amount) AS incomesSummary FROM incomes LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id = '$user_id' AND incomes.date_of_income BETWEEN '$startingDate' AND '$endingDate' GROUP BY incomes_category_assigned_to_users.name
      ");
      if (!$recentIncomesQuery) throw new Exception($polaczenie->error);

      $incomesByCategory = [];
      while ($row = $recentIncomesQuery->fetch_assoc()) {
        $incomesByCategory[$row['category_name']] = $row['incomesSummary'];
      }
      $recentExpensesQuery = $polaczenie->query("SELECT SUM(amount) AS expensesSummary FROM expenses WHERE user_id = '$user_id' AND date_of_expense BETWEEN '$startingDate' AND '$endingDate'");
      if (!$recentExpensesQuery) throw new Exception($polaczenie->error);
      $expensesSummary = $recentExpensesQuery->fetch_assoc()['expensesSummary'];

      $recentIncomesQuery = $polaczenie->query("SELECT SUM(amount) AS incomesSummary FROM incomes WHERE user_id = '$user_id' AND date_of_income BETWEEN '$startingDate' AND '$endingDate'");
      if (!$recentIncomesQuery) throw new Exception($polaczenie->error);
      $incomesSummary = $recentIncomesQuery->fetch_assoc()['incomesSummary'];

      $balance = $incomesSummary - $expensesSummary;
    } else {
      $expensesSummary = 0;
      $incomesSummary = 0;
      $balance = 0;
      $expensesByCategory = [];
      $incomesByCategory = [];
    }
    $polaczenie->close();
  }
} catch (Exception $e) {
  echo '<span style="color:red;">Błąd serwera!</span>';
  echo '<br />Informacja developerska: ' . $e;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Balance Summary</title>
</head>

<body>
  <header>
  <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li>
              <a href="mainPage.php" class="nav-link text-secondary">
                Home
              </a>
            </li>
            <li>
              <a href="expenses.php" class="nav-link text-white">
                Expenses
              </a>
            </li>
            <li>
              <a href="incomes.php" class="nav-link text-white">
                Incomes
              </a>
            </li>
            <li>
              <a href="dashboards.php" class="nav-link text-white">
                Dashboards
              </a>
            </li>
            <li>
              <a href="#" class="nav-link text-white">
                Planner & Analyzer
              </a>
            </li>
            <li>
              <a href="logout.php" class="nav-link text-white">
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </header>

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
                  <td><?php echo $expensesSummary; ?></td>
                  <td><?php echo $incomesSummary; ?></td>
                  <td><?php echo $balance; ?></td>
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
          <tbody>
            <?php
            foreach ($expensesByCategory as $categoryName => $expenseAmount) {
              echo "<tr><td>Expense</td><td>{$categoryName}</td><td>{$expenseAmount}</td></tr>";
            }
            foreach ($incomesByCategory as $categoryName => $incomeAmount) {
              echo "<tr><td>Income</td><td>{$categoryName}</td><td>{$incomeAmount}</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section id="footer" class="d-flex justify-content-center">
    <div class="container">
      <footer class="py-3">
        <p class="text-center text-body-secondary mt-5">© 2024 BudgetBuddy Sp. z o.o.</p>
      </footer>
    </div>
  </section>

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