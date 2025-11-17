<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\{TransactionService, AuthService, UserService, GeminiService};

class HomeController {
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService,
        private AuthService $auth,
        private UserService $userService,
        private GeminiService $geminiService
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

    public function getInsights(){
        header('Content-Type: application/json');
        $userId = $this->auth->getUserId();
        
        try {
            $insights = $this->geminiService->generateInsights($userId);
            
            echo json_encode([
                'success' => true,
                'data' => $insights,
                'csrfToken' => $_SESSION['token'] ?? ''
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to generate insights',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function chatMessage(){
        header('Content-Type: application/json');
        $userId = $this->auth->getUserId();
        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Message is required'
            ]);
            return;
        }

        try {
            $response = $this->geminiService->chat($userId, $message);
            // Add new CSRF token to response
            $response['csrfToken'] = $_SESSION['token'] ?? '';
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to process message',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function clearChat(){
        $userId = $this->auth->getUserId();
        
        try {
            $this->geminiService->clearChatHistory($userId);
            echo json_encode([
                'success' => true,
                'message' => 'Chat history cleared'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to clear chat',
                'error' => $e->getMessage()
            ]);
        }
    }
}
