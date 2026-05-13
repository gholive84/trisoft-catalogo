<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class CategoryRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Todas as categorias ativas (flat). Ordenadas por sort_order, name.
     * @return array<int, array>
     */
    public function allActive(): array
    {
        $sql = "SELECT id, parent_id, name, slug, description, image_path, sort_order, is_active
                  FROM categories
                 WHERE is_active = 1
              ORDER BY sort_order ASC, name ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    /** @return array<int, array> */
    public function all(): array
    {
        return $this->pdo->query(
            "SELECT id, parent_id, name, slug, sort_order, is_active
               FROM categories
           ORDER BY sort_order ASC, name ASC"
        )->fetchAll();
    }

    /**
     * Filhas diretas de uma categoria (ou raízes se $parentId for null).
     * @return array<int, array>
     */
    public function children(?int $parentId): array
    {
        if ($parentId === null) {
            $sql = "SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order, name";
            return $this->pdo->query($sql)->fetchAll();
        }
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE parent_id = :pid AND is_active = 1 ORDER BY sort_order, name"
        );
        $stmt->execute(['pid' => $parentId]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna IDs da categoria + todos descendentes (para filtrar produtos).
     * @return int[]
     */
    public function descendantIds(int $categoryId): array
    {
        $all = $this->allActive();
        $byParent = [];
        foreach ($all as $c) {
            $pid = $c['parent_id'] === null ? 0 : (int) $c['parent_id'];
            $byParent[$pid][] = (int) $c['id'];
        }
        $ids = [$categoryId];
        $stack = [$categoryId];
        while ($stack !== []) {
            $current = array_pop($stack);
            foreach ($byParent[$current] ?? [] as $childId) {
                $ids[] = $childId;
                $stack[] = $childId;
            }
        }
        return array_values(array_unique($ids));
    }

    /**
     * Caminho da raiz até a categoria (breadcrumb).
     * @return array<int, array>
     */
    public function ancestors(int $categoryId): array
    {
        $chain = [];
        $current = $this->findById($categoryId);
        $guard = 0;
        while ($current !== null && $guard++ < 20) {
            array_unshift($chain, $current);
            if ($current['parent_id'] === null) {
                break;
            }
            $current = $this->findById((int) $current['parent_id']);
        }
        return $chain;
    }

    /** Categorias raiz ativas (para Home). */
    public function rootCategories(int $limit = 10): array
    {
        $sql = "SELECT * FROM categories
                 WHERE parent_id IS NULL AND is_active = 1
              ORDER BY sort_order ASC, name ASC
                 LIMIT {$limit}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories
                (parent_id, name, slug, description, image_path, sort_order, is_active, meta_title, meta_description)
             VALUES (:parent, :name, :slug, :desc, :img, :sort, :active, :mt, :md)"
        );
        $stmt->execute([
            'parent' => $data['parent_id'] ?? null,
            'name'   => $data['name'],
            'slug'   => $data['slug'],
            'desc'   => $data['description'] ?? null,
            'img'    => $data['image_path'] ?? null,
            'sort'   => (int) ($data['sort_order'] ?? 0),
            'active' => (int) ($data['is_active'] ?? 1),
            'mt'     => $data['meta_title'] ?? null,
            'md'     => $data['meta_description'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
