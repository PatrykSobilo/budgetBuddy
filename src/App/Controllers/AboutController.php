<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;

class AboutController
{
    public function __construct(private TemplateEngine $view)
    {
    }

    public function about() {
        echo $this->view->render('about.php', [
            'title' => 'About'

        ]);
    }
    public function planner() {
        echo $this->view->render('planner.php', [
            'title' => 'Planner & Analyzer'
        ]);
    }
}
