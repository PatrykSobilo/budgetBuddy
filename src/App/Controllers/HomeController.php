<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\{TransactionService, AuthService};

class HomeController {
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService,
        private AuthService $auth
    ) {}

    public function home(){
        echo $this->view->render("index.php", [
            'title' => 'Home page'
        ]);
    }

    public function mainPageView(){
        $transactions = null;
        $budgetSummary = null;
        
        if ($this->auth->check()) {
            $transactions = $this->transactionService->getUserTransactions(10);
            $budgetSummary = $this->transactionService->getBudgetSummary($this->auth->getUserId());
        }
        
        echo $this->view->render("mainPage.php", [
            'title' => 'Main page',
            'transactions' => $transactions,
            'budgetSummary' => $budgetSummary
        ]);
    }
}