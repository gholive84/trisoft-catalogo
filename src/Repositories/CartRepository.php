<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class CartRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    /**
     * Retorna o carrinho do usuário ou cria um novo.
     */
    public function findOrCreateForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM carts WHERE user_id = :uid ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }

        $this->pdo->prepare("INSERT INTO carts (user_id, last_activity_at) VALUES (?, NOW())")
            ->execute([$userId]);
        $id = (int) $this->pdo->lastInsertId();

        return $this->findById($id) ?? ['id' => $id, 'user_id' => $userId];
    }

    public function findOrCreateForSession(string $sessionId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM carts WHERE session_id = :s AND user_id IS NULL ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(['s' => $sessionId]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }

        $this->pdo->prepare("INSERT INTO carts (session_id, last_activity_at) VALUES (?, NOW())")
            ->execute([$sessionId]);
        $id = (int) $this->pdo->lastInsertId();

        return $this->findById($id) ?? ['id' => $id, 'session_id' => $sessionId];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findBySessionId(string $sessionId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM carts WHERE session_id = ? AND user_id IS NULL ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$sessionId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @return array<int, array> Itens com dados do produto (name, sku, price, main_image_path)
     */
    public function itemsWithProduct(int $cartId): array
    {
        $sql = "SELECT ci.id AS item_id, ci.cart_id, ci.product_id, ci.quantity, ci.added_at,
                       p.sku, p.name, p.slug, p.price, p.is_active, p.deleted_at,
                       (SELECT pi.file_path FROM product_images pi
                          WHERE pi.product_id = p.id
                       ORDER BY pi.is_main DESC, pi.sort_order ASC, pi.id ASC LIMIT 1) AS main_image_path
                  FROM cart_items ci
                  JOIN products p ON p.id = ci.product_id
                 WHERE ci.cart_id = ?
              ORDER BY ci.added_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cartId]);
        return $stmt->fetchAll();
    }

    public function countItems(int $cartId): int
    {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
        return (int) $stmt->fetchColumn();
    }

    public function addItem(int $cartId, int $productId, int $quantity): void
    {
        // ON DUPLICATE KEY soma a quantidade (unique key cart_id+product_id)
        $stmt = $this->pdo->prepare(
            "INSERT INTO cart_items (cart_id, product_id, quantity)
             VALUES (:c, :p, :q)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
        );
        $stmt->execute(['c' => $cartId, 'p' => $productId, 'q' => $quantity]);
        $this->touch($cartId);
    }

    public function updateItemQuantity(int $cartId, int $itemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cartId, $itemId);
            return;
        }
        $stmt = $this->pdo->prepare(
            "UPDATE cart_items SET quantity = :q WHERE id = :i AND cart_id = :c"
        );
        $stmt->execute(['q' => $quantity, 'i' => $itemId, 'c' => $cartId]);
        $this->touch($cartId);
    }

    public function removeItem(int $cartId, int $itemId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
        $stmt->execute([$itemId, $cartId]);
        $this->touch($cartId);
    }

    public function clear(int $cartId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
        $this->touch($cartId);
    }

    public function touch(int $cartId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE carts SET last_activity_at = NOW(), abandoned_email_sent_at = NULL WHERE id = ?"
        );
        $stmt->execute([$cartId]);
    }

    public function assignToUser(int $cartId, int $userId): void
    {
        $stmt = $this->pdo->prepare("UPDATE carts SET user_id = ?, session_id = NULL WHERE id = ?");
        $stmt->execute([$userId, $cartId]);
    }

    public function delete(int $cartId): void
    {
        $this->pdo->prepare("DELETE FROM carts WHERE id = ?")->execute([$cartId]);
    }
}
