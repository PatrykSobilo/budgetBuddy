<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    ValidatorService,
    UserService,
    ResponseService,
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

        $this->userService->createUser($this->request->postAll());

        $this->response->redirect('/mainPage');
    }

    public function loginView()
    {
        echo $this->view->render("login.php");
    }

    public function login()
    {
        $this->validatorService->validateLogin($this->request->postAll());

        $this->userService->login($this->request->postAll());

        $this->response->redirect('/mainPage');
    }

    public function logout()
    {
        $this->userService->logout();

        $this->response->redirect('/');
    }
}
