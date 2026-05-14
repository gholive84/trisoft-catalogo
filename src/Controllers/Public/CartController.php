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
        if (Auth::guest()) {
            Session::flash('info', 'Faça login para visualizar seu orçamento.');
            Session::put('_intended_url', 'carrinho');
            Response::redirect(url('login'));
        }

        return $this->view->render('public/cart', [
            'title'   => 'Orçamento',
            'summary' => $this->cart->summary(),
        ]);
    }

    /**
     * Adiciona produto ao carrinho. Se visitante, redireciona para login
     * com a intenção pendente. Caso contrário, redireciona de volta à página
     * de origem e abre o drawer automaticamente.
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
            Session::put('_intended_url', ltrim(parse_url($request->referer(), PHP_URL_PATH) ?: '/', '/'));
            Session::flash('info', 'Crie sua conta ou faça login para adicionar ao orçamento.');
            Response::redirect(url('login'));
        }

        $ok = $this->cart->add($productId, $quantity);
        if (!$ok) {
            Session::flash('error', 'Não foi possível adicionar este produto.');
            Response::redirect($request->referer() ?: url('/'));
        }

        Session::flash('success', 'Adicionado ao orçamento.');

        // Destino após adicionar:
        //   _then=cart  → vai direto para /carrinho (botão "Solicitar Orçamento")
        //   default     → volta pra página de origem e abre o drawer
        if ($request->post('_then') === 'cart') {
            Response::redirect(url('carrinho'));
        }

        Session::flash('open_cart_drawer', true);
        Response::redirect($request->referer() ?: url('/'));
    }

    public function update(Request $request): never
    {
        if (Auth::guest()) Response::redirect(url('login'));

        $itemId   = (int) $request->post('item_id', 0);
        $quantity = (int) $request->post('quantity', 0);
        $fromDrawer = $request->post('_drawer') === '1';

        if ($itemId > 0) {
            $this->cart->updateQuantity($itemId, $quantity);
            if (!$fromDrawer) {
                Session::flash('success', $quantity <= 0 ? 'Item removido.' : 'Quantidade atualizada.');
            }
        }

        // Quando vem do drawer, abre ele de novo após o redirect
        if ($fromDrawer) {
            Session::flash('open_cart_drawer', true);
            Response::redirect($request->referer() ?: url('carrinho'));
        }
        Response::redirect(url('carrinho'));
    }

    public function remove(Request $request): never
    {
        if (Auth::guest()) Response::redirect(url('login'));

        $itemId = (int) $request->post('item_id', 0);
        $fromDrawer = $request->post('_drawer') === '1';
        if ($itemId > 0) {
            $this->cart->remove($itemId);
            if (!$fromDrawer) {
                Session::flash('success', 'Item removido do orçamento.');
            }
        }

        if ($fromDrawer) {
            Session::flash('open_cart_drawer', true);
            Response::redirect($request->referer() ?: url('carrinho'));
        }
        Response::redirect(url('carrinho'));
    }

    public function clear(Request $request): never
    {
        if (Auth::guest()) Response::redirect(url('login'));
        $this->cart->clear();
        Session::flash('success', 'Carrinho esvaziado.');
        Response::redirect(url('carrinho'));
    }

    public function requestQuote(Request $request): never
    {
        if (Auth::guest()) Response::redirect(url('login'));

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

        $repo  = new \App\Repositories\OrderRepository();
        $order = $repo->findById($orderId);

        Session::flash('success', "Pedido de orçamento {$order['order_number']} enviado!");
        Response::redirect(url('minha-conta/orcamentos/' . $order['order_number']));
    }
}
