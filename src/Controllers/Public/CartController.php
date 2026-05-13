<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\CartService;
use App\Services\QuoteService;

final class CartController
{
    private View $view;
    private CartService $cart;
    private QuoteService $quotes;

    public function __construct()
    {
        $this->view   = new View(base_path('templates'));
        $this->cart   = new CartService();
        $this->quotes = new QuoteService();
    }

    public function show(Request $request): string
    {
        // O carrinho exige login (decisão do briefing: customer-only)
        if (Auth::guest()) {
            Session::flash('info', 'Faça login para visualizar seu carrinho de orçamento.');
            Session::put('_intended_url', 'carrinho');
            Response::redirect(url('login'));
        }

        $summary = $this->cart->summary();

        return $this->view->render('public/cart', [
            'title'    => 'Carrinho de orçamento',
            'summary'  => $summary,
        ]);
    }

    /**
     * Adiciona produto ao carrinho. Se visitante, redireciona para login
     * com a intenção pendente — após autenticar, o produto é incluído.
     */
    public function add(Request $request): never
    {
        $productId = (int) $request->post('product_id', 0);
        $quantity  = max(1, (int) $request->post('quantity', 1));

        if ($productId <= 0) {
            Session::flash('error', 'Produto inválido.');
            Response::back();
        }

        if (Auth::guest()) {
            Session::put('_pending_cart_add', [
                'product_id' => $productId,
                'quantity'   => $quantity,
            ]);
            Session::put('_intended_url', 'carrinho');
            Session::flash('info', 'Crie sua conta ou faça login para adicionar ao orçamento.');
            Response::redirect(url('login'));
        }

        $ok = $this->cart->add($productId, $quantity);
        if (!$ok) {
            Session::flash('error', 'Não foi possível adicionar este produto.');
        } else {
            Session::flash('success', 'Item adicionado ao orçamento.');
        }
        Response::redirect(url('carrinho'));
    }

    public function update(Request $request): never
    {
        if (Auth::guest()) {
            Response::redirect(url('login'));
        }
        $itemId   = (int) $request->post('item_id', 0);
        $quantity = (int) $request->post('quantity', 0);
        if ($itemId > 0) {
            $this->cart->updateQuantity($itemId, $quantity);
            Session::flash('success', $quantity <= 0 ? 'Item removido.' : 'Quantidade atualizada.');
        }
        Response::redirect(url('carrinho'));
    }

    public function remove(Request $request): never
    {
        if (Auth::guest()) {
            Response::redirect(url('login'));
        }
        $itemId = (int) $request->post('item_id', 0);
        if ($itemId > 0) {
            $this->cart->remove($itemId);
            Session::flash('success', 'Item removido do orçamento.');
        }
        Response::redirect(url('carrinho'));
    }

    public function clear(Request $request): never
    {
        if (Auth::guest()) {
            Response::redirect(url('login'));
        }
        $this->cart->clear();
        Session::flash('success', 'Carrinho esvaziado.');
        Response::redirect(url('carrinho'));
    }

    /**
     * Submete o carrinho como pedido de orçamento.
     */
    public function requestQuote(Request $request): never
    {
        if (Auth::guest()) {
            Response::redirect(url('login'));
        }

        $notes = trim((string) $request->post('notes', ''));

        try {
            $orderId = $this->quotes->createFromCart(
                (int) Auth::id(),
                $notes !== '' ? $notes : null
            );
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect(url('carrinho'));
        }

        // Recupera o número do orçamento para a tela de confirmação
        $repo = new \App\Repositories\OrderRepository();
        $order = $repo->findById($orderId);

        Session::flash('success', "Pedido de orçamento {$order['order_number']} enviado!");
        Response::redirect(url('minha-conta/orcamentos/' . $order['order_number']));
    }
}
