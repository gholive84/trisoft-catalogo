<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;

final class CategoryTreeService
{
    public function __construct(
        private CategoryRepository $repo = new CategoryRepository()
    ) {
    }

    /**
     * Constrói uma árvore hierárquica de categorias ativas.
     * Cada nó: ['id','parent_id','name','slug',...,'children' => [...]]
     *
     * @return array<int, array>
     */
    public function tree(): array
    {
        $flat = $this->repo->allActive();
        return $this->buildTree($flat);
    }

    /** @param array<int, array> $flat */
    public function buildTree(array $flat): array
    {
        $byId = [];
        foreach ($flat as $row) {
            $row['children'] = [];
            $byId[(int) $row['id']] = $row;
        }

        $tree = [];
        foreach ($byId as $id => $node) {
            $parentId = $node['parent_id'] !== null ? (int) $node['parent_id'] : null;
            if ($parentId !== null && isset($byId[$parentId])) {
                $byId[$parentId]['children'][] = &$byId[$id];
            } else {
                $tree[] = &$byId[$id];
            }
            unset($node);
        }
        return $tree;
    }

    /**
     * Breadcrumbs de uma categoria.
     * @return array<int, array>
     */
    public function breadcrumbs(int $categoryId): array
    {
        return $this->repo->ancestors($categoryId);
    }
}
