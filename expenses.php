<?php
session_start();

if (!isset($_SESSION['zalogowany'])) {
  header('Location: loginForm.php');
  exit();
}

require_once "connect.php";
$user_id = $_SESSION['id'];

try {
  $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
  if ($polaczenie->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    $expensesQuery = $polaczenie->query("SELECT id, amount, date_of_expense AS date, expense_comment AS description, expense_category_assigned_to_user_id FROM expenses WHERE user_id = '$user_id' ORDER BY date_of_expense DESC");
    if (!$expensesQuery) throw new Exception($polaczenie->error);

    $expenses = [];
    while ($row = $expensesQuery->fetch_assoc()) {
      $expenses[] = $row;
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
  <title>Expenses</title>
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

    <section id="historyExpensesPanel" class="py-3 mb-4">
      <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
          <h2 class="mb-4">Expenses</h2>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($expenses as $expense): ?>
                <tr>
                  <td>Expense</td>
                  <td><?php echo htmlspecialchars($expense['description']); ?></td>
                  <td><?php echo number_format($expense['amount'], 2); ?> PLN</td>
                  <td><?php echo date('Y-m-d', strtotime($expense['date'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </header>

  <section id="footer"></section>
  <div class="container">
    <footer class="py-3">
      <p class="text-center text-body-secondary mt-5">© 2024 BudgetBuddy Sp. z o.o.</p>
    </footer>
  </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
