<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
<?php include $this->resolve("partials/_searchForm.php"); ?>

<section id="historyExpensesPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Expenses</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['type']); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?></td>
                            <td>
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-icon" data-description="<?php echo htmlspecialchars($expense['description']); ?>">
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-description="<?php echo htmlspecialchars($expense['description']); ?>">
                                    <i class="bi bi-trash"></i>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Expense</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include $this->resolve("partials/_footer.php"); ?>