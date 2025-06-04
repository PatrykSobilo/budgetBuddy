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
    $openModal = null;
    if (isset($_POST['expensesCategory'])) {
      $openModal = 'customAddExpenseModal';
    } elseif (isset($_POST['incomesCategory'])) {
      $openModal = 'customAddIncomeModal';
    }
    try {
      $this->validatorService->validateTransaction($_POST);
    } catch (\Framework\Exceptions\ValidationException $e) {
      // Regeneruj token po nieudanym POST (walidacja)
      $_SESSION['token'] = bin2hex(random_bytes(32));
      $csrfToken = $_SESSION['token'];
      echo $this->view->render('mainPage.php', [
        'oldFormData' => $_POST,
        'errors' => $e->errors,
        'openModal' => $openModal,
        'csrfToken' => $csrfToken
      ]);
      return;
    }

    // Dodawanie do bazy
    if (isset($_POST['expensesCategory'])) {
      $this->transactionService->createExpense($_POST);
    } elseif (isset($_POST['incomesCategory'])) {
      $this->transactionService->createIncome($_POST);
    }

    // Po sukcesie odśwież token i wyczyść formularz
    $_SESSION['token'] = bin2hex(random_bytes(32));
    echo $this->view->render('mainPage.php', [
      'oldFormData' => [],
      'errors' => [],
      'openModal' => null,
      'csrfToken' => $_SESSION['token']
    ]);
  }
}
