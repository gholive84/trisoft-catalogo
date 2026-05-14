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
     * Monta a cláusula WHERE para busca multi-palavra.
     *
     * Cada token (palavra separada por espaço) precisa aparecer em PELO MENOS
     * UM dos campos pesquisados (name, sku, subtitle, description). Entre
     * tokens é AND — todas as palavras precisam existir, mas podem estar em
     * campos ou posições diferentes.
     *
     * Ex.: "baffle classic" encontra um produto cujo `name` é "BAFFLE CLASSIC
     * STRAIGHT" (ambas as palavras no name), mas também um cujo `name` é
     * "BAFFLE FORM" + `description` "Classic ...".
     *
     * @return array{where: string, params: array<int, string>}
     */
    public static function buildSearchWhere(string $query, string $alias = 'p'): array
    {
        $query = trim($query);
        if ($query === '') return ['where' => '', 'params' => []];

        // Quebra em tokens (whitespace), descarta tokens com menos de 2 chars
        $tokens = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = array_values(array_filter($tokens, fn ($t) => mb_strlen($t) >= 2));
        if ($tokens === []) {
            // Token único de 1 char ainda pode ser válido para SKU
            $tokens = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }
        if ($tokens === []) return ['where' => '', 'params' => []];

        $clauses = [];
        $params  = [];
        foreach ($tokens as $tok) {
            $like = '%' . $tok . '%';
            $clauses[] = "({$alias}.name LIKE ? OR {$alias}.sku LIKE ? OR {$alias}.subtitle LIKE ? OR {$alias}.description LIKE ?)";
            array_push($params, $like, $like, $like, $like);
        }

        return [
            'where'  => '(' . implode(' AND ', $clauses) . ')',
            'params' => $params,
        ];
    }

    /**
     * Busca por nome, SKU ou descrição. Multi-palavra (AND entre tokens).
     * @return array{items: array<int, array>, total: int, page: int, perPage: int, lastPage: int}
     */
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $page    = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset  = ($page - 1) * $perPage;

        $base = "p.is_active = 1 AND p.deleted_at IS NULL";
        $searchPart = self::buildSearchWhere($query, 'p');
        $where = $searchPart['where'] !== ''
            ? "{$base} AND {$searchPart['where']}"
            : "{$base}";

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$where}");
        $stmt->execute($searchPart['params']);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT p.* FROM products p
                 WHERE {$where}
              ORDER BY p.name ASC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($searchPart['params']);
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
