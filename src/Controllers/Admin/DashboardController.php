<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;

final class DashboardController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(base_path('templates'));
    }

    public function index(Request $request): string
    {
        $pdo = Database::connection();

        $kpis = [
            'pending_quotes'     => $this->safeCount($pdo, "SELECT COUNT(*) FROM orders WHERE status = 'quote_requested'"),
            'new_customers_30d'  => $this->safeCount($pdo, "SELECT COUNT(*) FROM users WHERE role = 'customer' AND created_at >= NOW() - INTERVAL 30 DAY AND deleted_at IS NULL"),
            'active_products'    => $this->safeCount($pdo, "SELECT COUNT(*) FROM products WHERE is_active = 1 AND deleted_at IS NULL"),
            'abandoned_carts'    => $this->safeCount($pdo, "SELECT COUNT(*) FROM carts WHERE last_activity_at < NOW() - INTERVAL 3 DAY"),
        ];

        return $this->view->render('admin/dashboard', [
            'title' => 'Dashboard',
            'kpis'  => $kpis,
        ]);
    }

    private function safeCount(\PDO $pdo, string $sql): int
    {
        try {
            return (int) $pdo->query($sql)->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }
}
