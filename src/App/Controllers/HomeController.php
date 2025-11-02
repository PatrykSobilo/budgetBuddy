<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\{TransactionService, AuthService, UserService};

class HomeController {
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService,
        private AuthService $auth,
        private UserService $userService
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
            $userId = $this->auth->getUserId();
            $transactions = $this->transactionService->getUserTransactions($userId, 10);
            $budgetSummary = $this->transactionService->getBudgetSummary($userId);
        }
        
        $userId = $this->auth->getUserId();
        echo $this->view->render("mainPage.php", [
            'title' => 'Main page',
            'transactions' => $transactions,
            'budgetSummary' => $budgetSummary,
            'expenseCategories' => $userId ? $this->userService->getExpenseCategories($userId) : [],
            'incomeCategories' => $userId ? $this->userService->getIncomeCategories($userId) : [],
            'paymentMethods' => $userId ? $this->userService->getPaymentMethods($userId) : []
        ]);
    }
}