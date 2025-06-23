<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\UserService;
use App\Services\SettingsService;
use App\Services\ValidatorService;
use Framework\Database;

class SettingsController
{
    public function __construct(
        private TemplateEngine $view,
        private UserService $userService,
        private SettingsService $settingsService,
        private ValidatorService $validatorService
    ) {}

    public function settings()
    {
        $userData = null;
        if (isset($_SESSION['user'])) {
            $userData = $this->userService->getUserById($_SESSION['user']);
        }
        $csrfToken = $_SESSION['token'] ?? '';

        // Obsługa dodawania kategorii wydatków/przychodów przez POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
            $categoryName = trim($_POST['name'] ?? '');
            $userId = $_SESSION['user'] ?? null;
            $errors = [];
            $old = [
                'name' => $categoryName,
                'type' => $_POST['type'] ?? ''
            ];
            try {
                $this->validatorService->validateCategory(
                    ['name' => $categoryName],
                    $_POST['type'],
                    (int)$userId,
                    $this->settingsService->getDb() // poprawne pobranie instancji Database
                );
                if ($userId && $categoryName !== '') {
                    if ($_POST['type'] === 'expense') {
                        $this->settingsService->addExpenseCategory((int)$userId, $categoryName);
                        $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                    } elseif ($_POST['type'] === 'income') {
                        $this->settingsService->addIncomeCategory((int)$userId, $categoryName);
                        $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                    }
                    header('Location: /settings');
                    exit;
                }
            } catch (\Framework\Exceptions\ValidationException $e) {
                $errors = $e->errors;
                // Odśwież token po błędzie walidacji
                $_SESSION['token'] = bin2hex(random_bytes(32));
                $csrfToken = $_SESSION['token'];
            }
            echo $this->view->render('settings.php', [
                'title' => 'Settings',
                'user' => $userData,
                'categoryErrors' => $errors,
                'categoryOld' => $old,
                'csrfToken' => $csrfToken
            ]);
            return;
        }

        echo $this->view->render('settings.php', [
            'title' => 'Settings',
            'user' => $userData,
            'csrfToken' => $csrfToken
        ]);
    }
}
