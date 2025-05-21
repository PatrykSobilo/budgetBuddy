<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;

class HomeController {
    public function __construct(private TemplateEngine $view) {
    //    $this->view = new TemplateEngine(Paths::VIEW);
    }

    public function home(){
        echo $this->view->render("index.php", [
            'title' => 'Home page'
        ]);
    }

    public function mainPageView(){
        echo $this->view->render("mainPage.php", [
            'title' => 'Main page'
        ]);
    }
}