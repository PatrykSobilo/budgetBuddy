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
              <form>
                <div class="form-floating">
                  <input type="text" class="form-control" id="floatingPassword" placeholder="Name">
                  <label for="floatingPassword">Expense Type</label>
                </div>
                <div class="form-floating">
                  <input type="text" class="form-control" id="floatingPassword" placeholder="Surname">
                  <label for="floatingPassword">Description</label>
                </div>
                <div class="form-floating">
                  <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                  <label for="floatingInput">Amount</label>
                </div>
                <div class="form-floating">
                  <input type="date" class="form-control" id="floatingPassword" placeholder="mm/dd/yyyy">
                  <label for="floatingPassword">Date</label>
                </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save Changes</button>
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
              <form>
                <div class="form-floating">
                  <input type="text" class="form-control" id="floatingPassword" placeholder="Name">
                  <label for="floatingPassword">Income Type</label>
                </div>
                <div class="form-floating">
                  <input type="text" class="form-control" id="floatingPassword" placeholder="Surname">
                  <label for="floatingPassword">Description</label>
                </div>
                <div class="form-floating">
                  <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                  <label for="floatingInput">Amount</label>
                </div>
                <div class="form-floating">
                  <input type="date" class="form-control" id="floatingPassword" placeholder="mm/dd/yyyy">
                  <label for="floatingPassword">Date</label>
                </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save Changes</button>
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