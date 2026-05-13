<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\View;

final class HomeController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(base_path('templates'));
    }

    public function index(Request $request): string
    {
        return $this->view->render('public/home', [
            'title' => 'Início',
        ]);
    }
}
