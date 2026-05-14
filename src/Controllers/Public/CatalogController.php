<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryTreeService;

final class CatalogController
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

    /**
     * Página da categoria — usa o mesmo layout/grid unificado da Home/Search,
     * mas vem com a categoria pré-selecionada.
     */
    public function show(Request $request): string
    {
        $slug = (string) $request->param('slug');
        $category = $this->cats->findBySlug($slug);
        if ($category === null || !$category['is_active']) {
            Response::abort(404, 'Categoria não encontrada.');
        }

        $params = [
            'cats'  => [(int) $category['id']],
            'q'     => trim((string) $request->query('q', '')),
            'page'  => max(1, (int) $request->query('page', 1)),
        ];

        $data = $this->buildListing($params);
        $data['title']           = $category['name'];
        $data['metaDescription'] = $category['meta_description'] ?? null;
        $data['initialCategory'] = $category;

        return $this->view->render('public/listing', $data);
    }

    /**
     * Endpoint AJAX para refresh do grid sem recarregar página.
     * Retorna {html: ...grid..., total, tags}.
     */
    public function listJson(Request $request): never
    {
        $params = [
            'cats' => $this->parseCatsParam($request->query('cats', '')),
            'q'    => trim((string) $request->query('q', '')),
            'page' => max(1, (int) $request->query('page', 1)),
        ];

        $data = $this->buildListing($params);

        $grid = $this->view->render('partials/products_grid', [
            'pagination' => $data['pagination'],
        ]);

        Response::json([
            'success' => true,
            'total'   => $data['pagination']['total'],
            'tags'    => $data['activeTags'],
            'html'    => $grid,
        ]);
    }

    /**
     * Constrói o estado de listagem comum (categorias selecionadas, busca, etc.).
     */
    private function buildListing(array $params): array
    {
        $catIds  = $params['cats'] ?? [];
        $query   = $params['q'] ?? '';
        $page    = (int) ($params['page'] ?? 1);
        $perPage = 24;

        // Expansão: cada categoria seleciona ela + descendentes
        $allCatIds = [];
        foreach ($catIds as $cid) {
            $allCatIds = array_merge($allCatIds, $this->cats->descendantIds((int) $cid));
        }
        $allCatIds = array_values(array_unique($allCatIds));

        $pagination = $this->fetchProducts($allCatIds, $query, $page, $perPage);

        // Tags de filtros ativos (categorias selecionadas + termo de busca)
        $activeTags = [];
        foreach ($catIds as $cid) {
            $cat = $this->cats->findById((int) $cid);
            if ($cat) {
                $activeTags[] = ['type' => 'category', 'id' => (int) $cat['id'], 'label' => $cat['name']];
            }
        }
        if ($query !== '') {
            $activeTags[] = ['type' => 'search', 'id' => 'q', 'label' => 'Busca: ' . $query];
        }

        return [
            'tree'         => $this->tree->tree(),
            'selectedCats' => array_map('intval', $catIds),
            'query'        => $query,
            'pagination'   => $pagination,
            'activeTags'   => $activeTags,
            'allCategories' => $this->cats->all(),
        ];
    }

    private function fetchProducts(array $catIds, string $query, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where  = "p.is_active = 1 AND p.deleted_at IS NULL";
        $params = [];
        $joins  = "";

        if ($catIds !== []) {
            $place = implode(',', array_fill(0, count($catIds), '?'));
            $joins .= " JOIN product_categories pc ON pc.product_id = p.id AND pc.category_id IN ({$place})";
            $params = array_merge($params, $catIds);
        }
        if ($query !== '') {
            $searchPart = ProductRepository::buildSearchWhere($query, 'p');
            if ($searchPart['where'] !== '') {
                $where .= " AND " . $searchPart['where'];
                $params = array_merge($params, $searchPart['params']);
            }
        }

        $countSql = "SELECT COUNT(DISTINCT p.id) FROM products p {$joins} WHERE {$where}";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT DISTINCT p.*
                  FROM products p
                  {$joins}
                 WHERE {$where}
              ORDER BY p.is_featured DESC, p.name ASC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $this->products->withMainImage($stmt->fetchAll());

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    private function parseCatsParam(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map('intval', $raw)));
        }
        $s = (string) $raw;
        if ($s === '') return [];
        return array_values(array_filter(array_map('intval', explode(',', $s))));
    }
}
