<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class OrderRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNumber(string $number): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
        $stmt->execute([$number]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Próximo número de orçamento no formato ORC-YYYY-NNNNN.
     * Conta o número de orders no ano atual + 1.
     */
    public function nextOrderNumber(): string
    {
        $year = (int) date('Y');
        $prefix = "ORC-{$year}-";
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM orders WHERE order_number LIKE :p"
        );
        $stmt->execute(['p' => $prefix . '%']);
        $next = ((int) $stmt->fetchColumn()) + 1;
        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO orders
            (order_number, user_id, created_by_user_id, status,
             subtotal, discount, shipping_cost, total,
             customer_notes, internal_notes, expires_at)
            VALUES
            (:num, :uid, :cby, :status,
             :sub, :disc, :ship, :tot,
             :cnotes, :inotes, :exp)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'num'    => $data['order_number'],
            'uid'    => $data['user_id'],
            'cby'    => $data['created_by_user_id'] ?? null,
            'status' => $data['status'] ?? 'quote_requested',
            'sub'    => $data['subtotal'] ?? 0,
            'disc'   => $data['discount'] ?? 0,
            'ship'   => $data['shipping_cost'] ?? 0,
            'tot'    => $data['total'] ?? 0,
            'cnotes' => $data['customer_notes'] ?? null,
            'inotes' => $data['internal_notes'] ?? null,
            'exp'    => $data['expires_at'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addItem(int $orderId, array $item): void
    {
        $sql = "INSERT INTO order_items
            (order_id, product_id, quantity, unit_price, discount, total, product_snapshot)
            VALUES (:o, :p, :q, :u, :d, :t, :snap)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'o'    => $orderId,
            'p'    => $item['product_id'],
            'q'    => $item['quantity'],
            'u'    => $item['unit_price'],
            'd'    => $item['discount'] ?? 0,
            't'    => $item['total'],
            'snap' => json_encode($item['product_snapshot'], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function updateStatus(int $orderId, string $status, array $extra = []): void
    {
        $allowed = ['quoted_by_user_id', 'quoted_at', 'approved_at', 'completed_at', 'canceled_at',
                    'subtotal', 'discount', 'shipping_cost', 'total', 'internal_notes', 'customer_notes',
                    'expires_at'];
        $fields = ['status = :status'];
        $params = ['status' => $status, 'id' => $orderId];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $extra)) {
                $fields[] = "{$col} = :{$col}";
                $params[$col] = $extra[$col];
            }
        }
        $sql = "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->pdo->prepare($sql)->execute($params);
    }

    /** @return array<int, array> */
    public function itemsOf(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Lista de orçamentos do cliente.
     * @return array<int, array>
     */
    public function listForUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function countForUser(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Lista geral para admin (com filtro de status opcional).
     * @return array<int, array>
     */
    public function listForAdmin(?string $status = null, int $page = 1, int $perPage = 30): array
    {
        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $where  = '1=1';
        $params = [];
        if ($status !== null && $status !== '') {
            $where = 'o.status = :status';
            $params['status'] = $status;
        }
        $sql = "SELECT o.*, u.name AS customer_name, u.email AS customer_email
                  FROM orders o
                  JOIN users u ON u.id = o.user_id
                 WHERE {$where}
              ORDER BY o.created_at DESC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countPendingQuotes(): int
    {
        return (int) $this->pdo->query(
            "SELECT COUNT(*) FROM orders WHERE status = 'quote_requested'"
        )->fetchColumn();
    }
}
