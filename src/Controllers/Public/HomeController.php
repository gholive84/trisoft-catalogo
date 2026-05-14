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
    private \PDO $pdo;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->cats     = new CategoryRepository();
        $this->products = new ProductRepository();
        $this->tree     = new CategoryTreeService($this->cats);
        $this->pdo      = Database::connection();
    }

    public function index(Request $request): string
    {
        $q       = trim((string) $request->query('q', ''));
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 24;
        $offset  = ($page - 1) * $perPage;

        $where  = "is_active = 1 AND deleted_at IS NULL";
        $params = [];
        if ($q !== '') {
            $where .= " AND (name LIKE :q OR sku LIKE :q OR subtitle LIKE :q OR description LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM products WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM products
                 WHERE {$where}
              ORDER BY is_featured DESC, name ASC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $this->products->withMainImage($stmt->fetchAll());

        $activeTags = [];
        if ($q !== '') {
            $activeTags[] = ['type' => 'search', 'id' => 'q', 'label' => 'Busca: ' . $q];
        }

        return $this->view->render('public/listing', [
            'title'           => 'Nossos Produtos',
            'tree'            => $this->tree->tree(),
            'allCategories'   => $this->cats->all(),
            'selectedCats'    => [],
            'query'           => $q,
            'activeTags'      => $activeTags,
            'initialCategory' => null,
            'pagination'      => [
                'items'    => $items,
                'total'    => $total,
                'page'     => $page,
                'perPage'  => $perPage,
                'lastPage' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
