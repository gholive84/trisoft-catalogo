<?php

declare(strict_types=1);

namespace App\Controllers\Public;

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

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->cats     = new CategoryRepository();
        $this->products = new ProductRepository();
        $this->tree     = new CategoryTreeService($this->cats);
    }

    public function show(Request $request): string
    {
        $slug = (string) $request->param('slug');
        $category = $this->cats->findBySlug($slug);
        if ($category === null || !$category['is_active']) {
            Response::abort(404, 'Categoria não encontrada.');
        }

        $page    = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per', 12);
        $sort    = (string) $request->query('sort', 'newest');

        $minPrice = $request->query('min') !== null && $request->query('min') !== ''
            ? (float) $request->query('min') : null;
        $maxPrice = $request->query('max') !== null && $request->query('max') !== ''
            ? (float) $request->query('max') : null;

        $catIds = $this->cats->descendantIds((int) $category['id']);

        $result = $this->products->paginateByCategoryIds(
            $catIds, $page, $perPage, $sort, $minPrice, $maxPrice
        );

        return $this->view->render('public/category', [
            'title'       => $category['name'],
            'metaDescription' => $category['meta_description'] ?? null,
            'category'    => $category,
            'breadcrumbs' => $this->tree->breadcrumbs((int) $category['id']),
            'children'    => $this->cats->children((int) $category['id']),
            'pagination'  => $result,
            'sort'        => $sort,
            'minPrice'    => $minPrice,
            'maxPrice'    => $maxPrice,
        ]);
    }
}
