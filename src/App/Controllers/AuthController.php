<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    ValidatorService,
    UserService,
    ResponseService,
    SessionService,
    Request
};

use function Framework\dd;

class AuthController
{
    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validatorService,
        private UserService $userService,
        private ResponseService $response,
        private SessionService $session,
        private Request $request
    ) {}

    public function registerView()
    {
        echo $this->view->render("register.php");
    }

    public function register()
    {
        $this->validatorService->validateRegister($this->request->postAll());

        $this->userService->isEmailTaken($this->request->post('email'));

        $userData = $this->userService->createUser($this->request->postAll());
        
        session_regenerate_id();
        $this->session->setUserData(
            $userData['userId'],
            $userData['expenseCategories'],
            $userData['incomeCategories'],
            $userData['paymentMethods']
        );

        $this->response->redirect('/mainPage');
    }

    public function loginView()
    {
        echo $this->view->render("login.php");
    }

    public function login()
    {
        $this->validatorService->validateLogin($this->request->postAll());

        $userData = $this->userService->login($this->request->postAll());
        
        session_regenerate_id();
        $this->session->setUserData(
            $userData['userId'],
            $userData['expenseCategories'],
            $userData['incomeCategories'],
            $userData['paymentMethods']
        );

        // Handle "Remember Me" - extend session cookie lifetime if checked
        $remember = $this->request->post('remember');
        if ($remember) {
            // Extend session cookie to 30 days
            $lifetime = time() + (60 * 60 * 24 * 30); // 30 days from now
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                session_id(),
                $lifetime,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        $this->response->redirect('/mainPage');
    }

    public function logout()
    {
        // UserService::logout() handles full session cleanup
        $this->userService->logout();

        $this->response->redirect('/');
    }
}
