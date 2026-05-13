<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class ProductRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE id = :id AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE slug = :slug AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @return array<int, array>
     */
    public function imagesOf(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, file_path, alt_text, is_main, sort_order
               FROM product_images
              WHERE product_id = :p
           ORDER BY is_main DESC, sort_order ASC, id ASC"
        );
        $stmt->execute(['p' => $productId]);
        return $stmt->fetchAll();
    }

    public function mainImageOf(int $productId): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT file_path FROM product_images
              WHERE product_id = :p
           ORDER BY is_main DESC, sort_order ASC, id ASC
              LIMIT 1"
        );
        $stmt->execute(['p' => $productId]);
        $v = $stmt->fetchColumn();
        return $v ? (string) $v : null;
    }

    /**
     * Anexa main_image_path em cada produto de uma lista (1 query extra).
     * @param array<int, array> $products
     * @return array<int, array>
     */
    public function withMainImage(array $products): array
    {
        if ($products === []) {
            return [];
        }
        $ids = array_map(fn ($p) => (int) $p['id'], $products);
        $place = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT pi.product_id, pi.file_path
                  FROM product_images pi
                 WHERE pi.product_id IN ({$place})
              ORDER BY pi.is_main DESC, pi.sort_order ASC, pi.id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $r) {
            $pid = (int) $r['product_id'];
            if (!isset($map[$pid])) {
                $map[$pid] = (string) $r['file_path'];
            }
        }
        foreach ($products as &$p) {
            $p['main_image_path'] = $map[(int) $p['id']] ?? null;
        }
        return $products;
    }

    /** @return array<int, array> */
    public function featured(int $limit = 8): array
    {
        $sql = "SELECT * FROM products
                 WHERE is_active = 1 AND is_featured = 1 AND deleted_at IS NULL
              ORDER BY updated_at DESC
                 LIMIT {$limit}";
        $rows = $this->pdo->query($sql)->fetchAll();
        return $this->withMainImage($rows);
    }

    /**
     * Lista produtos por categoria (com descendentes), com filtros e paginação.
     *
     * @param int[] $categoryIds
     * @return array{items: array<int, array>, total: int, page: int, perPage: int, lastPage: int}
     */
    public function paginateByCategoryIds(
        array $categoryIds,
        int $page = 1,
        int $perPage = 12,
        string $sort = 'newest',
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): array {
        $page    = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset  = ($page - 1) * $perPage;

        if ($categoryIds === []) {
            return ['items' => [], 'total' => 0, 'page' => $page, 'perPage' => $perPage, 'lastPage' => 1];
        }

        $place  = implode(',', array_fill(0, count($categoryIds), '?'));
        $params = array_values($categoryIds);

        $where = "p.is_active = 1 AND p.deleted_at IS NULL";
        if ($minPrice !== null) {
            $where .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice !== null) {
            $where .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }

        $orderBy = match ($sort) {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            default       => 'p.created_at DESC',
        };

        $countSql = "SELECT COUNT(DISTINCT p.id)
                       FROM products p
                       JOIN product_categories pc ON pc.product_id = p.id
                      WHERE pc.category_id IN ({$place}) AND {$where}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT DISTINCT p.*
                  FROM products p
                  JOIN product_categories pc ON pc.product_id = p.id
                 WHERE pc.category_id IN ({$place}) AND {$where}
              ORDER BY {$orderBy}
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $this->withMainImage($stmt->fetchAll());

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * Busca por nome, SKU ou descrição (LIKE).
     * @return array{items: array<int, array>, total: int, page: int, perPage: int, lastPage: int}
     */
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $page    = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset  = ($page - 1) * $perPage;
        $like    = '%' . trim($query) . '%';

        $where = "is_active = 1 AND deleted_at IS NULL
                  AND (name LIKE ? OR sku LIKE ? OR description LIKE ?)";

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM products WHERE {$where}");
        $stmt->execute([$like, $like, $like]);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT * FROM products
                 WHERE {$where}
              ORDER BY name ASC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$like, $like, $like]);
        $items = $this->withMainImage($stmt->fetchAll());

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * Categorias associadas a um produto.
     * @return array<int, array>
     */
    public function categoriesOf(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.* FROM categories c
               JOIN product_categories pc ON pc.category_id = c.id
              WHERE pc.product_id = :p
           ORDER BY c.sort_order, c.name"
        );
        $stmt->execute(['p' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Produtos relacionados: outros da mesma categoria.
     * @return array<int, array>
     */
    public function relatedTo(int $productId, int $limit = 4): array
    {
        // ATTR_EMULATE_PREPARES = false não permite reutilizar o mesmo
        // placeholder — daí dois nomes diferentes para o mesmo valor.
        $sql = "SELECT DISTINCT p.*
                  FROM products p
                  JOIN product_categories pc ON pc.product_id = p.id
                 WHERE pc.category_id IN (
                          SELECT category_id FROM product_categories WHERE product_id = :pid_a
                       )
                   AND p.id <> :pid_b
                   AND p.is_active = 1 AND p.deleted_at IS NULL
              ORDER BY p.created_at DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid_a' => $productId, 'pid_b' => $productId]);
        return $this->withMainImage($stmt->fetchAll());
    }
}
