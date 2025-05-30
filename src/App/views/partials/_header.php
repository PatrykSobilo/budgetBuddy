<?php
$oldFormData = $oldFormData ?? [];
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo e($title); ?> - Budget Buddy</title>

  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>

<body>
  <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
          <li>
            <a href="mainPage" class="nav-link text-secondary">
              Home
            </a>
          </li>
          <li>
            <a href="expenses" class="nav-link text-white">
              Expenses
            </a>
          </li>
          <li>
            <a href="incomes" class="nav-link text-white">
              Incomes
            </a>
          </li>
          <li>
            <a href="dashboards" class="nav-link text-white">
              Dashboards
            </a>
          </li>
          <li>
            <a href="about" class="nav-link text-white">
              About
            </a>
          </li>
          <li>
            <a href="" class="nav-link text-white">
              Planner & Analyzer
            </a>
          </li>
          <li>
            <a href="/logout" class="nav-link text-white">
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
            <form action="/mainPage" method="post">
              <div class="form-floating">
                <select class="form-control" id="expensesCategory" name="expensesCategory">
                  <option value=""></option>
                </select>
                <label for="expenseCategory">Expense Category</label>
              </div>

              <div class="form-floating">
                <select class="form-control" id="paymentMethods" name="paymentMethods">
                  <option value=""></option>
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
            <form action="/mainPage" method="post">
              <div class="form-floating">
                <select class="form-control" id="incomesCategory" name="incomesCategory">
                  <option value=""></option>
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
</body>