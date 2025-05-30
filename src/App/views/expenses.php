<?php include $this->resolve("partials/_header.php"); ?>

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
                    <tr>
                        <td>Expense</td>
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

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>