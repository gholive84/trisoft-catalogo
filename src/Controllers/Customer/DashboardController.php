<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Core\Auth;
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
        $userId = (int) Auth::id();
        $pdo = Database::connection();

        $openQuotes = (int) $pdo->query(
            "SELECT COUNT(*) FROM orders
              WHERE user_id = {$userId}
                AND status IN ('quote_requested','quoted')"
        )->fetchColumn();

        $cartItems = (int) $pdo->query(
            "SELECT COALESCE(SUM(ci.quantity), 0)
               FROM cart_items ci
               JOIN carts c ON c.id = ci.cart_id
              WHERE c.user_id = {$userId}"
        )->fetchColumn();

        $completed = (int) $pdo->query(
            "SELECT COUNT(*) FROM orders
              WHERE user_id = {$userId}
                AND status IN ('delivered','paid')"
        )->fetchColumn();

        $recent = $pdo->prepare(
            "SELECT order_number, status, total, created_at
               FROM orders
              WHERE user_id = :u
           ORDER BY created_at DESC
              LIMIT 5"
        );
        $recent->execute(['u' => $userId]);

        return $this->view->render('customer/dashboard', [
            'title'      => 'Minha conta',
            'kpis'       => [
                'open_quotes' => $openQuotes,
                'cart_items'  => $cartItems,
                'completed'   => $completed,
            ],
            'recentOrders' => $recent->fetchAll(),
        ]);
    }
}
