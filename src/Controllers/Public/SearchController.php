<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\View;
use App\Repositories\ProductRepository;

final class SearchController
{
    private View $view;
    private ProductRepository $products;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->products = new ProductRepository();
    }

    public function index(Request $request): string
    {
        $query = trim((string) $request->query('q', ''));
        $page  = (int) $request->query('page', 1);

        $result = $query !== ''
            ? $this->products->search($query, $page, 12)
            : ['items' => [], 'total' => 0, 'page' => 1, 'perPage' => 12, 'lastPage' => 1];

        return $this->view->render('public/search', [
            'title'      => $query !== '' ? "Busca: {$query}" : 'Buscar',
            'query'      => $query,
            'pagination' => $result,
        ]);
    }
}
