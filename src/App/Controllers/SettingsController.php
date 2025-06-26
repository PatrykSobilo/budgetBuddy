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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
            $type = $_POST['type'];
            $categoryName = trim($_POST['category_name'] ?? $_POST['name'] ?? '');
            $categoryName = mb_convert_case($categoryName, MB_CASE_TITLE, "UTF-8");
            $userId = $_SESSION['user'] ?? null;
            $errors = [];
            $old = [
                'name' => $categoryName,
                'type' => $type
            ];
            try {
                $this->validatorService->validateCategory(
                    ['name' => $categoryName],
                    ($type === 'expense_category' ? 'expense' : ($type === 'income_category' ? 'income' : $type)),
                    (int)$userId,
                    $this->settingsService->getDb()
                );
                if ($userId && $categoryName !== '') {
                    if ($type === 'expense_category' || $type === 'expense') {
                        $this->settingsService->addExpenseCategory((int)$userId, $categoryName);
                        $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                    } elseif ($type === 'income_category' || $type === 'income') {
                        $this->settingsService->addIncomeCategory((int)$userId, $categoryName);
                        $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                    }
                    header('Location: /settings');
                    exit;
                }
            } catch (\Framework\Exceptions\ValidationException $e) {
                $errors = $e->errors;
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
