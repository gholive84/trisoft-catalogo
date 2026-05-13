<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

final class HomeController
{
    private View $view;
    private CategoryRepository $cats;
    private ProductRepository $products;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->cats     = new CategoryRepository();
        $this->products = new ProductRepository();
    }

    public function index(Request $request): string
    {
        return $this->view->render('public/home', [
            'title'             => 'Início',
            'rootCategories'    => $this->cats->rootCategories(8),
            'featuredProducts'  => $this->products->featured(8),
        ]);
    }
}
