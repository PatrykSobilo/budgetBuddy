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

    <section id="historyPanel" class="py-3 mb-4">
      <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
          <h2 class="mb-4">Incomes</h2>
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
                <td>Income</td>
                <td>PlayStation 4</td>
                <td>1500 PLN</td>
                <td>2024-10-05</td>
              </tr>
              <tr>
                <td>Income</td>
                <td>Payroll correction</td>
                <td>3000 PLN</td>
                <td>2024-10-15</td>
              </tr>
              <tr>
                <td>Income</td>
                <td>Intrests - Credit Agricole</td>
                <td>5000 PLN</td>
                <td>2024-10-25</td>
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