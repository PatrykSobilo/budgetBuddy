<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    UserService,
    SettingsService,
    ValidatorService,
    ResponseService,
    FlashService,
    SessionService,
    Request,
    AuthService,
    ViewHelperService
};
use Framework\Database;

class SettingsController
{
    public function __construct(
        private TemplateEngine $view,
        private UserService $userService,
        private SettingsService $settingsService,
        private ValidatorService $validatorService,
        private ResponseService $response,
        private FlashService $flash,
        private SessionService $session,
        private Request $request,
        private AuthService $auth,
        private ViewHelperService $viewHelper
    ) {}

    public function deleteCategory()
    {
        $userId = $this->auth->getUserId();
        $type = $this->request->post('type', '');
        $categoryId = (int)$this->request->post('category_id', 0);

        if (!$userId || !$categoryId || !$type) {
            $this->response->redirectWithFlash('/settings', 'Invalid request.', 'error');
        }

        if ($type === 'expense_category_delete') {
            $category = $this->viewHelper->findExpenseCategoryById($categoryId);
            $categoryName = $category['name'] ?? 'Unknown';
            
            if ($this->settingsService->isExpenseCategoryUsed($categoryId, $userId)) {
                $this->flash->error('Cannot delete: category "' . htmlspecialchars($categoryName) . '" is used in expenses.');
                $this->flash->set('settings_section', 'expense-categories');
            } else {
                $this->settingsService->deleteExpenseCategory($categoryId, $userId);
                $this->session->set('expenseCategories', $this->userService->getExpenseCategories($userId));
            }
        } elseif ($type === 'income_category_delete') {
            $category = $this->viewHelper->findIncomeCategoryById($categoryId);
            $categoryName = $category['name'] ?? 'Unknown';
            
            if ($this->settingsService->isIncomeCategoryUsed($categoryId, $userId)) {
                $this->flash->error('Cannot delete: category "' . htmlspecialchars($categoryName) . '" is used in incomes.');
                $this->flash->set('settings_section', 'incomes-categories');
            } else {
                $this->settingsService->deleteIncomeCategory($categoryId, $userId);
                $this->session->set('incomeCategories', $this->userService->getIncomeCategories($userId));
            }
        } elseif ($type === 'payment_method_delete') {
            $method = $this->viewHelper->findPaymentMethodById($categoryId);
            $methodName = $method['name'] ?? 'Unknown';
            
            if ($this->settingsService->isPaymentMethodUsed($categoryId, $userId)) {
                $this->flash->error('Cannot delete: payment method "' . htmlspecialchars($methodName) . '" is used in expenses.');
                $this->flash->set('settings_section', 'payment-methods');
            } else {
                $this->settingsService->deletePaymentMethod($categoryId, $userId);
                $this->session->set('paymentMethods', $this->userService->getPaymentMethods($userId));
            }
        } else {
            $this->flash->error('Invalid request type.');
        }

        $this->response->redirect('/settings');
    }

    public function settings()
    {
        $userData = null;
        if (isset($_SESSION['user'])) {
            $userData = $this->userService->getUserById((int)$_SESSION['user']);
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
                        $_SESSION['settings_section'] = 'expense-categories';
                    } elseif ($type === 'income_category' || $type === 'income') {
                        $this->settingsService->addIncomeCategory((int)$userId, $categoryName);
                        $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                        $_SESSION['settings_section'] = 'incomes-categories';
                    } elseif ($type === 'payment_method') {
                        $this->settingsService->addPaymentMethod((int)$userId, $categoryName);
                        $_SESSION['paymentMethods'] = $this->userService->getPaymentMethods($userId);
                        $_SESSION['settings_section'] = 'payment-methods';
                    } elseif ($type === 'payment_method_edit') {
                        $methodId = (int)($_POST['category_id'] ?? 0);
                        $this->settingsService->updatePaymentMethod((int)$userId, $methodId, $categoryName);
                        $_SESSION['paymentMethods'] = $this->userService->getPaymentMethods($userId);
                        $_SESSION['settings_section'] = 'payment-methods';
                    } elseif ($type === 'expense_category_edit') {
                        $catId = (int)($_POST['category_id'] ?? 0);
                        $categoryLimit = isset($_POST['category_limit']) && $_POST['category_limit'] !== '' 
                            ? (float)$_POST['category_limit'] 
                            : null;
                        $this->settingsService->updateExpenseCategory((int)$userId, $catId, $categoryName, $categoryLimit);
                        $_SESSION['expenseCategories'] = $this->userService->getExpenseCategories($userId);
                        $_SESSION['settings_section'] = 'expense-categories';
                    } elseif ($type === 'income_category_edit') {
                        $catId = (int)($_POST['category_id'] ?? 0);
                        $this->settingsService->updateIncomeCategory((int)$userId, $catId, $categoryName);
                        $_SESSION['incomeCategories'] = $this->userService->getIncomeCategories($userId);
                        $_SESSION['settings_section'] = 'incomes-categories';
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

    /**
     * Usuwa konto użytkownika i wszystkie powiązane dane
     */
    public function deleteAccount()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        $userId = $_SESSION['user'];
        $this->userService->deleteUserAndData($userId);
        // Wyloguj użytkownika i wyczyść sesję
        unset($_SESSION['user']);
        session_destroy();
        session_regenerate_id();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        header('Location: /');
        exit;
    }
}
