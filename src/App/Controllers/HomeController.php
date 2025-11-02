<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\TransactionService;

class HomeController {
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService
    ) {}

    public function home(){
        echo $this->view->render("index.php", [
            'title' => 'Home page'
        ]);
    }

    public function mainPageView(){
        $transactions = null;
        $budgetSummary = null;
        
        if (isset($_SESSION['user'])) {
            $transactions = $this->transactionService->getUserTransactions(10);
            $budgetSummary = $this->transactionService->getBudgetSummary((int)$_SESSION['user']);
        }
        
        echo $this->view->render("mainPage.php", [
            'title' => 'Main page',
            'transactions' => $transactions,
            'budgetSummary' => $budgetSummary
        ]);
    }
}