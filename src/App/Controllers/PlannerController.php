<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{TransactionService, AuthService, Request, UserService};

class PlannerController
{
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService,
        private AuthService $auth,
        private Request $request,
        private UserService $userService
    ) {}

    public function planner()
    {
        $categoriesWithLimits = [];
        $selectedCategoryId = null;
        $timelineData = null;
        $selectedCategoryName = null;
        
        if ($this->auth->check()) {
            $userId = $this->auth->getUserId();
            $categoriesWithLimits = $this->transactionService->getCategoriesWithLimits($userId);
            
            // Obsługa wyboru kategorii do wykresu timeline
            if ($this->request->hasGet('category_id') && !empty($categoriesWithLimits)) {
                $selectedCategoryId = (int)$this->request->get('category_id');
                $timelineData = $this->transactionService->getCategoryTimeline($userId, $selectedCategoryId);
                
                // Znajdź nazwę wybranej kategorii
                foreach ($categoriesWithLimits as $cat) {
                    if ($cat['id'] === $selectedCategoryId) {
                        $selectedCategoryName = $cat['name'];
                        break;
                    }
                }
            }
        }
        
        $userId = $this->auth->getUserId();
        echo $this->view->render('planner.php', [
            'title' => 'Planner & Analyzer',
            'categoriesWithLimits' => $categoriesWithLimits,
            'selectedCategoryId' => $selectedCategoryId,
            'timelineData' => $timelineData,
            'selectedCategoryName' => $selectedCategoryName,
            'expenseCategories' => $userId ? $this->userService->getExpenseCategories($userId) : [],
            'incomeCategories' => $userId ? $this->userService->getIncomeCategories($userId) : [],
            'paymentMethods' => $userId ? $this->userService->getPaymentMethods($userId) : []
        ]);
    }
}

