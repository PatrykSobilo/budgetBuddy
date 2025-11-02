<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\TransactionService;

class PlannerController
{
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService
    ) {}

    public function planner()
    {
        $categoriesWithLimits = [];
        $selectedCategoryId = null;
        $timelineData = null;
        $selectedCategoryName = null;
        
        if (isset($_SESSION['user'])) {
            $userId = (int)$_SESSION['user'];
            $categoriesWithLimits = $this->transactionService->getCategoriesWithLimits($userId);
            
            // Obsługa wyboru kategorii do wykresu timeline
            if (isset($_GET['category_id']) && !empty($categoriesWithLimits)) {
                $selectedCategoryId = (int)$_GET['category_id'];
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
        
        echo $this->view->render('planner.php', [
            'title' => 'Planner & Analyzer',
            'categoriesWithLimits' => $categoriesWithLimits,
            'selectedCategoryId' => $selectedCategoryId,
            'timelineData' => $timelineData,
            'selectedCategoryName' => $selectedCategoryName
        ]);
    }
}

