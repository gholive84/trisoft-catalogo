<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Repositories\AnalyticsRepository;

final class DashboardController
{
    private View $view;
    private \PDO $pdo;

    public function __construct()
    {
        $this->view = new View(base_path('templates'));
        $this->pdo  = Database::connection();
    }

    public function index(Request $request): string
    {
        $kpis = [
            'pending_quotes'     => $this->safeCount("SELECT COUNT(*) FROM orders WHERE status = 'quote_requested'"),
            'quoted_count'       => $this->safeCount("SELECT COUNT(*) FROM orders WHERE status = 'quoted'"),
            'new_customers_30d'  => $this->safeCount("SELECT COUNT(*) FROM users WHERE role = 'customer' AND created_at >= NOW() - INTERVAL 30 DAY AND deleted_at IS NULL"),
            'active_products'    => $this->safeCount("SELECT COUNT(*) FROM products WHERE is_active = 1 AND deleted_at IS NULL"),
            'abandoned_carts'    => $this->safeCount("SELECT COUNT(DISTINCT c.id) FROM carts c JOIN cart_items ci ON ci.cart_id = c.id WHERE c.last_activity_at < NOW() - INTERVAL 3 DAY"),
        ];

        // Online agora (analytics)
        try {
            $analytics = new AnalyticsRepository($this->pdo);
            $activeNow = $analytics->activeSessionsCount(15);
        } catch (\Throwable) {
            $activeNow = 0;
        }
        $kpis['active_now'] = $activeNow;

        // Últimos 5 orçamentos pendentes
        $pending = $this->pdo->query(
            "SELECT o.id, o.order_number, o.total, o.created_at, u.name AS customer_name
               FROM orders o
               JOIN users u ON u.id = o.user_id
              WHERE o.status = 'quote_requested'
           ORDER BY o.created_at DESC
              LIMIT 5"
        )->fetchAll();

        // Últimos clientes
        $newCustomers = $this->pdo->query(
            "SELECT id, name, email, created_at
               FROM users
              WHERE role = 'customer' AND deleted_at IS NULL
           ORDER BY created_at DESC
              LIMIT 5"
        )->fetchAll();

        return $this->view->render('admin/dashboard', [
            'title'         => 'Dashboard',
            'kpis'          => $kpis,
            'pendingOrders' => $pending,
            'newCustomers'  => $newCustomers,
        ]);
    }

    private function safeCount(string $sql): int
    {
        try {
            return (int) $this->pdo->query($sql)->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }
}
