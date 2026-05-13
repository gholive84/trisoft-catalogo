<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryTreeService;

final class ProductController
{
    private View $view;
    private ProductRepository $products;
    private CategoryRepository $cats;
    private CategoryTreeService $tree;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->products = new ProductRepository();
        $this->cats     = new CategoryRepository();
        $this->tree     = new CategoryTreeService($this->cats);
    }

    public function show(Request $request): string
    {
        $slug = (string) $request->param('slug');
        $product = $this->products->findBySlug($slug);
        if ($product === null || !$product['is_active']) {
            Response::abort(404, 'Produto não encontrado.');
        }

        $categories = $this->products->categoriesOf((int) $product['id']);
        $primaryCategory = $categories[0] ?? null;
        $breadcrumbs = $primaryCategory
            ? $this->tree->breadcrumbs((int) $primaryCategory['id'])
            : [];

        return $this->view->render('public/product', [
            'title'           => $product['name'],
            'metaDescription' => $product['meta_description'] ?? $product['short_description'] ?? null,
            'product'         => $product,
            'images'          => $this->products->imagesOf((int) $product['id']),
            'categories'      => $categories,
            'breadcrumbs'     => $breadcrumbs,
            'related'         => $this->products->relatedTo((int) $product['id'], 4),
        ]);
    }
}
