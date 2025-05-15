<?php include $this->resolve("partials/_header.php"); ?>

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
              <form action="addIncome.php" method="post">
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

    <section id="historyPanel" class="py-3 mb-4">
      <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
          <h2 class="mb-4">Previous 10 Incomes/Expenses</h2>
          <table class="table table-bordered" name="balance">
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
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </header>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

<?php include $this->resolve("partials/_footer.php"); ?>