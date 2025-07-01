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
    public function deleteCategory()
    {
        $userId = $_SESSION['user'] ?? null;
        $type = $_POST['type'] ?? '';
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $redirectUrl = '/settings';

        if (!$userId || !$categoryId || !$type) {
            $_SESSION['flash_error'] = 'Invalid request.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($type === 'expense_category_delete') {
            $categoryName = null;
            foreach ($_SESSION['expenseCategories'] ?? [] as $cat) {
                if ((int)$cat['id'] === $categoryId) {
                    $categoryName = $cat['name'];
                    break;
                }
            }
            if ($this->settingsService->isExpenseCategoryUsed($categoryId, $userId)) {
                $_SESSION['flash_error'] = 'Cannot delete: category "' . htmlspecialchars($categoryName) . '" is used in expenses.';
                $_SESSION['settings_section'] = 'expense-categories';
            } else {
                $this->settingsService->deleteExpenseCategory($categoryId, $userId);
                $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
            }
        } elseif ($type === 'income_category_delete') {
            $categoryName = null;
            foreach ($_SESSION['incomeCategories'] ?? [] as $cat) {
                if ((int)$cat['id'] === $categoryId) {
                    $categoryName = $cat['name'];
                    break;
                }
            }
            if ($this->settingsService->isIncomeCategoryUsed($categoryId, $userId)) {
                $_SESSION['flash_error'] = 'Cannot delete: category "' . htmlspecialchars($categoryName) . '" is used in incomes.';
                $_SESSION['settings_section'] = 'incomes-categories';
            } else {
                $this->settingsService->deleteIncomeCategory($categoryId, $userId);
                $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
            }
        } elseif ($type === 'payment_method_delete') {
            $methodName = null;
            foreach ($_SESSION['paymentMethods'] ?? [] as $method) {
                if ((int)$method['id'] === $categoryId) {
                    $methodName = $method['name'];
                    break;
                }
            }
            if ($this->settingsService->isPaymentMethodUsed($categoryId, $userId)) {
                $_SESSION['flash_error'] = 'Cannot delete: payment method "' . htmlspecialchars($methodName) . '" is used in expenses.';
                $_SESSION['settings_section'] = 'payment-methods';
            } else {
                $this->settingsService->deletePaymentMethod($categoryId, $userId);
                $_SESSION['paymentMethods'] = $this->userService->getPaymentMethods($userId);
            }
        } else {
            $_SESSION['flash_error'] = 'Invalid request type.';
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

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
            if (in_array($type, ['expense_category', 'income_category', 'expense_category_edit', 'income_category_edit', 'expense', 'income', 'payment_method', 'payment_method_edit'])) {
                $categoryName = mb_convert_case($categoryName, MB_CASE_TITLE, "UTF-8");
            }
            $userId = $_SESSION['user'] ?? null;
            $errors = [];
            $old = [
                'name' => $categoryName,
                'type' => $type
            ];
            try {
                // Przy edycji przekazujemy także id edytowanego rekordu do walidatora
                if ($type === 'payment_method') {
                    $this->validatorService->validateCategory(
                        ['name' => $categoryName],
                        'payment',
                        (int)$userId,
                        $this->settingsService->getDb()
                    );
                } elseif ($type === 'payment_method_edit') {
                    $methodId = (int)($_POST['category_id'] ?? 0);
                    $this->validatorService->validateCategory(
                        ['name' => $categoryName, 'id' => $methodId],
                        'payment',
                        (int)$userId,
                        $this->settingsService->getDb()
                    );
                } elseif ($type === 'expense_category_edit') {
                    $catId = (int)($_POST['category_id'] ?? 0);
                    $this->validatorService->validateCategory(
                        ['name' => $categoryName, 'id' => $catId],
                        'expense',
                        (int)$userId,
                        $this->settingsService->getDb()
                    );
                } elseif ($type === 'income_category_edit') {
                    $catId = (int)($_POST['category_id'] ?? 0);
                    $this->validatorService->validateCategory(
                        ['name' => $categoryName, 'id' => $catId],
                        'income',
                        (int)$userId,
                        $this->settingsService->getDb()
                    );
                } else {
                    $this->validatorService->validateCategory(
                        ['name' => $categoryName],
                        ($type === 'expense_category' ? 'expense' : ($type === 'income_category' ? 'income' : $type)),
                        (int)$userId,
                        $this->settingsService->getDb()
                    );
                }
                if ($userId && $categoryName !== '') {
                    if ($type === 'expense_category' || $type === 'expense') {
                        $this->settingsService->addExpenseCategory((int)$userId, $categoryName);
                        $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                    } elseif ($type === 'income_category' || $type === 'income') {
                        $this->settingsService->addIncomeCategory((int)$userId, $categoryName);
                        $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                    } elseif ($type === 'payment_method') {
                        $this->settingsService->addPaymentMethod((int)$userId, $categoryName);
                        $_SESSION['paymentMethods'] = $this->userService->getPaymentMethods($userId);
                    } elseif ($type === 'payment_method_edit') {
                        $methodId = (int)($_POST['category_id'] ?? 0);
                        $this->settingsService->updatePaymentMethod((int)$userId, $methodId, $categoryName);
                        $_SESSION['paymentMethods'] = $this->userService->getPaymentMethods($userId);
                    } elseif ($type === 'expense_category_edit') {
                        $catId = (int)($_POST['category_id'] ?? 0);
                        $this->settingsService->updateExpenseCategory((int)$userId, $catId, $categoryName);
                        $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                    } elseif ($type === 'income_category_edit') {
                        $catId = (int)($_POST['category_id'] ?? 0);
                        $this->settingsService->updateIncomeCategory((int)$userId, $catId, $categoryName);
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

    public function editUser()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        $userId = $_SESSION['user'];
        $type = $_POST['type'] ?? '';
        $errors = [];
        $old = $_POST;
        $csrfToken = $_SESSION['token'] ?? '';
        try {
            if ($type === 'email') {
                $this->userService->updateEmail($userId, $_POST['email'] ?? '', $this->validatorService);
            } elseif ($type === 'age') {
                $this->userService->updateAge($userId, $_POST['age'] ?? '', $this->validatorService);
            } elseif ($type === 'password') {
                $this->userService->updatePassword($userId, $_POST, $this->validatorService, $this->userService->getDb());
            }
            // Odśwież dane użytkownika w sesji
            $userData = $this->userService->getUserById($userId);
            $_SESSION['userData'] = $userData;
            header('Location: /settings');
            exit;
        } catch (\Framework\Exceptions\ValidationException $e) {
            $errors = $e->errors;
            $_SESSION['token'] = bin2hex(random_bytes(32));
            $csrfToken = $_SESSION['token'];
        }
        echo $this->view->render('settings.php', [
            'title' => 'Settings',
            'user' => $this->userService->getUserById($userId),
            'editUserErrors' => $errors,
            'editUserOld' => $old,
            'csrfToken' => $csrfToken
        ]);
    }
}
