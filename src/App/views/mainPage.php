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

      <h3 class="mt-5">Twoje kategorie wydatków:</h3>
      <ul>
        <?php if (!empty($_SESSION['expenseCategories'])): ?>
          <?php foreach ($_SESSION['expenseCategories'] as $cat): ?>
            <li><?php echo e($cat['name']); ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Brak kategorii do wyświetlenia.</li>
        <?php endif; ?>
      </ul>

      <h3 class="mt-5">Twoje kategorie przychodów:</h3>
      <ul>
        <?php if (!empty($_SESSION['incomeCategories'])): ?>
          <?php foreach ($_SESSION['incomeCategories'] as $cat): ?>
            <li><?php echo e($cat['name']); ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Brak kategorii do wyświetlenia.</li>
        <?php endif; ?>
      </ul>

      <h3 class="mt-5">Twoje metody płatności:</h3>
      <ul>
        <?php if (!empty($_SESSION['paymentMethods'])): ?>
          <?php foreach ($_SESSION['paymentMethods'] as $method): ?>
            <li><?php echo e($method['name']); ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Brak metod do wyświetlenia.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>