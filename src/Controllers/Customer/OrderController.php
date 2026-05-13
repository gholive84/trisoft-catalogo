<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\OrderRepository;

final class OrderController
{
    private View $view;
    private OrderRepository $orders;

    public function __construct()
    {
        $this->view   = new View(base_path('templates'));
        $this->orders = new OrderRepository();
    }

    public function index(Request $request): string
    {
        $page = (int) $request->query('page', 1);
        $orders = $this->orders->listForUser((int) Auth::id(), $page, 20);
        $total  = $this->orders->countForUser((int) Auth::id());

        return $this->view->render('customer/orders/index', [
            'title'    => 'Meus orçamentos',
            'orders'   => $orders,
            'total'    => $total,
            'page'     => $page,
            'lastPage' => max(1, (int) ceil($total / 20)),
        ]);
    }

    public function show(Request $request): string
    {
        $number = (string) $request->param('number');
        $order  = $this->orders->findByNumber($number);

        if ($order === null || (int) $order['user_id'] !== (int) Auth::id()) {
            Response::abort(404, 'Orçamento não encontrado.');
        }

        $items = $this->orders->itemsOf((int) $order['id']);
        return $this->view->render('customer/orders/show', [
            'title' => 'Orçamento ' . $order['order_number'],
            'order' => $order,
            'items' => $items,
        ]);
    }
}
