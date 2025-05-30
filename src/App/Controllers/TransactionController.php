<?php

declare(strict_types=1);

namespace App\Controllers;


use Framework\Exceptions\ValidationException;
use Framework\TemplateEngine;
use App\Services\{ValidatorService, TransactionService};

class TransactionController
{
  public function __construct(
    private TemplateEngine $view,
    private ValidatorService $validatorService,
    private TransactionService $transactionService
  ) {}

  public function expensesView()
  {
    echo $this->view->render("expenses.php");
  }

  public function incomesView()
  {
    echo $this->view->render("incomes.php");
  }

  public function dashboardsView()
  {
    echo $this->view->render("dashboards.php");
  }

  public function createView()
  {
    echo $this->view->render("transactions/create.php");
  }

  //   public function create()
  //   {
  //     $this->validatorService->validateTransaction($_POST);

  //     $this->transactionService->create($_POST);

  //     redirectTo('/');
  //   }

  //   public function editView(array $params)
  //   {
  //     $transaction = $this->transactionService->getUserTransaction(
  //       $params['transaction']
  //     );

  //     if (!$transaction) {
  //       redirectTo('/');
  //     }

  //     echo $this->view->render('transactions/edit.php', [
  //       'transaction' => $transaction
  //     ]);
  //   }

  //   public function edit(array $params)
  //   {
  //     $transaction = $this->transactionService->getUserTransaction(
  //       $params['transaction']
  //     );

  //     if (!$transaction) {
  //       redirectTo('/');
  //     }

  //     $this->validatorService->validateTransaction($_POST);

  //     $this->transactionService->update($_POST, $transaction['id']);

  //     redirectTo($_SERVER['HTTP_REFERER']);
  //   }

  //   public function delete(array $params)
  //   {
  //     $this->transactionService->delete((int) $params['transaction']);

  //     redirectTo('/');
  //   }

  public function addTransaction()
  {
    try {
      $this->validatorService->validateTransaction($_POST);
      // Jeśli walidacja przejdzie, możesz dodać logikę zapisu do bazy
      // redirectTo('/mainPage'); // tymczasowo nie przekierowujemy
    } catch (\Framework\Exceptions\ValidationException $e) {
      // Przekazujemy stare dane i błędy do widoku
      echo $this->view->render('mainPage.php', [
        'oldFormData' => $_POST,
        'errors' => $e->errors
      ]);
      return;
    }

    // Jeśli wszystko OK, przekieruj lub wyświetl sukces
    echo $this->view->render('mainPage.php', [
      'oldFormData' => [],
      'errors' => []
    ]);
  }
}
