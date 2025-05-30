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
           <form method="POST" action="/mainPage" class="grid grid-cols-1 gap-6">

             <?php include $this->resolve("partials/_csrf.php"); ?>
             <div class="form-floating">
               <select class="form-control" id="expensesCategory" name="expensesCategory">
                 <option value="<?php echo e($oldFormData['expensesCategory'] ?? ''); ?>"></option>
               </select>
               <?php if (array_key_exists('expensesCategory', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['expensesCategory'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="expenseCategory">Expense Category</label>
             </div>

             <div class="form-floating">
               <select class="form-control" id="paymentMethods" name="paymentMethods">
                 <option value="<?php echo e($oldFormData['paymentMethods'] ?? ''); ?>"></option>
               </select>
               <?php if (array_key_exists('paymentMethods', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['paymentMethods'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="expenseCategory">Payment Method</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
               <?php if (array_key_exists('amount', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['amount'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="amount">Amount</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
               <?php if (array_key_exists('date', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['date'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="date">Date</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="description" name="description" placeholder="Description">
               <?php if (array_key_exists('description', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['description'][0]); ?>
                 </div>
               <?php endif; ?>
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
           <form method="POST" action="/mainPage" class="grid grid-cols-1 gap-6">

             <?php include $this->resolve("partials/_csrf.php"); ?>

             <div class="form-floating">
               <select class="form-control" id="incomesCategory" name="incomesCategory">
                 <option value="<?php echo e($oldFormData['incomesCategory'] ?? ''); ?>"></option>
               </select>
               <?php if (array_key_exists('incomesCategory', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['incomesCategory'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="incomesCategory">Income Category</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['amount'] ?? ''); ?>" type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
               <?php if (array_key_exists('amount', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['amount'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="amount">Amount</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['date'] ?? ''); ?>" type="date" class="form-control" id="date" name="date" placeholder="mm/dd/yyyy">
               <?php if (array_key_exists('date', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['date'][0]); ?>
                 </div>
               <?php endif; ?>
               <label for="date">Date</label>
             </div>

             <div class="form-floating">
               <input value="<?php echo e($oldFormData['description'] ?? ''); ?>" type="text" class="form-control" id="description" name="description" placeholder="Surname">
               <?php if (array_key_exists('description', $errors)) : ?>
                 <div class="bg-gray-100 mt-2 p-2 text-red-500">
                   <?php echo e($errors['description'][0]); ?>
                 </div>
               <?php endif; ?>
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

 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>