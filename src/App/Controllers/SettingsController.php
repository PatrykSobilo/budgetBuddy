<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\UserService;
use App\Services\SettingsService;

class SettingsController
{
    public function __construct(
        private TemplateEngine $view,
        private UserService $userService,
        private SettingsService $settingsService
    ) {}

    public function settings()
    {
        $userData = null;
        if (isset($_SESSION['user'])) {
            $userData = $this->userService->getUserById($_SESSION['user']);
        }

        // Obsługa dodawania kategorii wydatków przez POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
            $categoryName = trim($_POST['name'] ?? '');
            $userId = $_SESSION['user'] ?? null;
            if ($userId && $categoryName !== '') {
                if ($_POST['type'] === 'expense') {
                    $this->settingsService->addExpenseCategory((int)$userId, $categoryName);
                    $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                } elseif ($_POST['type'] === 'income') {
                    $this->settingsService->addIncomeCategory((int)$userId, $categoryName);
                    $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                }
                // Przekierowanie, by uniknąć ponownego wysłania formularza
                header('Location: /settings');
                exit;
            }
            // Możesz dodać obsługę błędów tutaj
        }

        echo $this->view->render('settings.php', [
            'title' => 'Settings',
            'user' => $userData
        ]);
    }
}
