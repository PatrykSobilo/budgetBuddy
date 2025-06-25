<?php include $this->resolve("partials/_header.php"); ?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
<?php include $this->resolve("partials/_searchForm.php"); ?>

<section id="historyExpensesPanel" class="py-3 mb-4">
    <div class="container d-flex flex-wrap border">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Expenses</h2>
            <table class="table table-bordered table-transactions">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Payment Method</th>
                        <th class="amount-col">Amount</th>
                        <th class="date-col">Date</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php
                                $catName = '';
                                if (!empty($_SESSION['expenseCategories'])) {
                                    foreach ($_SESSION['expenseCategories'] as $cat) {
                                        if ($cat['id'] == ($expense['expense_category_assigned_to_user_id'] ?? null)) {
                                            $catName = $cat['name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($catName);
                            ?></td>
                            <td><?php
                                $payName = '';
                                if (!empty($_SESSION['paymentMethods'])) {
                                    foreach ($_SESSION['paymentMethods'] as $method) {
                                        if ($method['id'] == ($expense['payment_method_assigned_to_user_id'] ?? null)) {
                                            $payName = $method['name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($payName);
                            ?></td>
                            <td class="amount-col"><?php echo number_format($expense['amount'], 2, '.', ' '); ?></td>
                            <td class="date-col"><?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?></td>
                            <td class="actions-col">
                                <span title="Edit" style="cursor:pointer; color:#2563eb; margin-right:10px;" class="edit-icon"
                                    data-id="<?php echo htmlspecialchars($expense['id'] ?? ''); ?>"
                                    data-description="<?php echo htmlspecialchars($expense['description']); ?>"
                                    data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                    data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($expense['date']))); ?>"
                                    data-category="<?php echo htmlspecialchars($expense['expense_category_assigned_to_user_id'] ?? ''); ?>"
                                    data-payment="<?php echo htmlspecialchars($expense['payment_method_assigned_to_user_id'] ?? ''); ?>"
                                >
                                  <i class="bi bi-pencil"></i>
                                </span>
                                <span title="Delete" style="cursor:pointer; color:#dc3545;" class="delete-icon" data-id="<?php echo htmlspecialchars($expense['id'] ?? ''); ?>" data-description="<?php echo htmlspecialchars($expense['description']); ?>" data-type="Expense">
  <i class="bi bi-trash"></i>
</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td></td>
                        <td></td>
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

<form id="deleteExpenseForm" method="POST" action="/expenses/delete" style="display:none;">
  <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken ?? ($_SESSION['token'] ?? '')]); ?>
  <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
</form>

<?php include $this->resolve("partials/_footer.php"); ?>