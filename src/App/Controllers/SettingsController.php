<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\UserService;

class SettingsController
{
    public function __construct(private TemplateEngine $view, private UserService $userService) {}

    public function settings()
    {
        $userData = null;
        if (isset($_SESSION['user'])) {
            $userData = $this->userService->getUserById($_SESSION['user']);
        }
        echo $this->view->render('settings.php', [
            'title' => 'Settings',
            'user' => $userData
        ]);
    }
}
