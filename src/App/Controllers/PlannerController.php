<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;

class PlannerController
{
    public function __construct(private TemplateEngine $view) {}

    public function planner()
    {
        echo $this->view->render('planner.php', [
            'title' => 'Planner & Analyzer'
        ]);
    }
}
