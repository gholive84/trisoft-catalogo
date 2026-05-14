<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryTreeService;

final class HomeController
{
    private View $view;
    private CategoryRepository $cats;
    private ProductRepository $products;
    private CategoryTreeService $tree;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->cats     = new CategoryRepository();
        $this->products = new ProductRepository();
        $this->tree     = new CategoryTreeService($this->cats);
    }

    public function index(Request $request): string
    {
        $pdo = Database::connection();

        // Todos os produtos ativos (sem paginação no MVP — pequena quantidade ainda).
        // Quando crescer, paginar ou usar lazy load.
        $stmt = $pdo->query(
            "SELECT * FROM products
              WHERE is_active = 1 AND deleted_at IS NULL
           ORDER BY is_featured DESC, name ASC
              LIMIT 60"
        );
        $rows = $stmt->fetchAll();
        $allProducts = $this->products->withMainImage($rows);

        return $this->view->render('public/home', [
            'title'          => 'Nossos Produtos',
            'tree'           => $this->tree->tree(),
            'allProducts'    => $allProducts,
            'activeCategory' => null,
            'query'          => '',
        ]);
    }
}
