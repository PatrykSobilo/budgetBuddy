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
    //wyswietlanie kategorii
    $userExpenseCategoryQuery = $polaczenie->query("SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = '$user_id'");
    if (!$userExpenseCategoryQuery) throw new Exception($polaczenie->error);
    $expensesCategories = [];
    while ($row = $userExpenseCategoryQuery->fetch_assoc()) {
      $expensesCategories[] = $row;
    }

    $userIncomeCategoryQuery = $polaczenie->query("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = '$user_id'");
    if (!$userIncomeCategoryQuery) throw new Exception($polaczenie->error);
    $incomesCategories = [];
    while ($row = $userIncomeCategoryQuery->fetch_assoc()) {
      $incomesCategories[] = $row;
    }
    //koniec wyswietlania kategorii

    $userPaymentMethodQuery = $polaczenie->query("SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = '$user_id'");
    if (!$userPaymentMethodQuery) throw new Exception($polaczenie->error);
    $paymentMethods = [];
    while ($row = $userPaymentMethodQuery->fetch_assoc()) {
      $paymentMethods[] = $row;
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

  <title>Document</title>
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

    <section id="actionButtons" class="text-end d-flex justify-content-center p-4">
      <button type="button" class="btn btn-primary m-1" data-bs-toggle="modal" data-bs-target="#addExpenseDialogBox">+
        Add Expense</button>

      <div class="modal fade" id="addExpenseDialogBox" tabindex="-1" aria-labelledby="addExpenseDialogBoxLabel"
        aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addExpenseDialogBoxLabel">Add Expense</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <form action="addExpense.php" method="post">
                <div class="form-floating">
                  <select class="form-control" id="expensesCategory" name="expensesCategory">
                    <?php foreach ($expensesCategories as $expensesCategory): ?>
                      <option value="<?php echo $expensesCategory['id']; ?>"><?php echo $expensesCategory['name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label for="expenseCategory">Expense Category</label>
                </div>

                <div class="form-floating">
                  <select class="form-control" id="paymentMethods" name="paymentMethods">
                    <?php foreach ($paymentMethods as $paymentMethods): ?>
                      <option value="<?php echo $paymentMethods['id']; ?>"><?php echo $paymentMethods['name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label for="expenseCategory">Payment Method</label>
                </div>

                <div class="form-floating">
                  <input type="number" class="form-control" id="floatingInput" name="amount" placeholder="Amount">
                  <label for="amount">Amount</label>
                </div>
                <div class="form-floating">
                  <input type="date" class="form-control" id="floatingDate" name="date" placeholder="mm/dd/yyyy">
                  <label for="date">Date</label>
                </div>
                <div class="form-floating">
                  <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                  <label for="description">Description</label>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-primary m-1" data-bs-toggle="modal" data-bs-target="#addIncomeDialogBox">+
        Add Income</button>

      <div class="modal fade" id="addIncomeDialogBox" tabindex="-1" aria-labelledby="addIncomeDialogBoxLabel"
        aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addIncomeDialogBoxLabel">Add Income</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <form action="addIncome.php" method="post">
                <div class="form-floating">
                  <select class="form-control" id="incomesCategory" name="incomesCategory">
                    <?php foreach ($incomesCategories as $incomesCategory): ?>
                      <option value="<?php echo $incomesCategory['id']; ?>"><?php echo $incomesCategory['name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label for="incomesCategory">Income Category</label>
                </div>
                <div class="form-floating">
                  <input type="number" class="form-control" id="floatingInput" name="amount" placeholder="Amount">
                  <label for="amount">Amount</label>
                </div>
                <div class="form-floating">
                  <input type="date" class="form-control" id="floatingPassword" name="date" placeholder="mm/dd/yyyy">
                  <label for="date">Date</label>
                </div>
                <div class="form-floating">
                  <input type="text" class="form-control" id="floatingPassword" name="description" placeholder="Surname">
                  <label for="description">Description</label>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="historyPanel" class="py-3 mb-4">
      <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
          <h2 class="mb-4">Previous Incomes/Expenses</h2>
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
              <tr>
                <td>Expense</td>
                <td>Counter Strike 2.0</td>
                <td>200 PLN</td>
                <td>2024-10-01</td>
              </tr>
              <tr>
                <td>Income</td>
                <td>PlayStation 4</td>
                <td>1500 PLN</td>
                <td>2024-10-05</td>
              </tr>
              <tr>
                <td>Expense</td>
                <td>Hosting fee</td>
                <td>100 PLN</td>
                <td>2024-10-10</td>
              </tr>
              <tr>
                <td>Income</td>
                <td>Payroll correction</td>
                <td>3000 PLN</td>
                <td>2024-10-15</td>
              </tr>
              <tr>
                <td>Expense</td>
                <td>Nvidia GeForce 3060Ti</td>
                <td>2500 PLN</td>
                <td>2024-10-20</td>
              </tr>
              <tr>
                <td>Income</td>
                <td>Intrests - Credit Agricole</td>
                <td>5000 PLN</td>
                <td>2024-10-25</td>
              </tr>
              <tr>
                <td>Expense</td>
                <td>Garage Rent Fee</td>
                <td>800 PLN</td>
                <td>2024-10-30</td>
              </tr>
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