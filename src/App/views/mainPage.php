<?php include $this->resolve("partials/_header.php"); ?>

<?php include $this->resolve("transactions/_transactionButtons.php"); ?>

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

<?php include $this->resolve("partials/_footer.php"); ?>