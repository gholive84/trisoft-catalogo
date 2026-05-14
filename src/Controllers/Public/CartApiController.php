<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\CartService;

/**
 * Endpoints AJAX do carrinho. Retornam JSON com estado atualizado +
 * HTML pré-renderizado do conteúdo do drawer (mais simples que reimplementar
 * a renderização em Alpine).
 */
final class CartApiController
{
    private CartService $cart;
    private View $view;

    public function __construct()
    {
        $this->cart = new CartService();
        $this->view = new View(base_path('templates'));
    }

    public function add(Request $request): never
    {
        $this->ensureCsrf($request);

        if (Auth::guest()) {
            Response::json([
                'success'      => false,
                'requireLogin' => true,
                'redirect'     => url('login'),
                'message'      => 'Faça login para adicionar ao orçamento.',
            ], 401);
        }

        $payload = $this->readJsonPayload($request);
        $productId = (int) ($payload['product_id'] ?? $request->post('product_id', 0));
        $quantity  = max(1, (int) ($payload['quantity'] ?? $request->post('quantity', 1)));

        if ($productId <= 0) {
            Response::json(['success' => false, 'message' => 'Produto inválido.'], 422);
        }

        $ok = $this->cart->add($productId, $quantity);
        if (!$ok) {
            Response::json(['success' => false, 'message' => 'Não foi possível adicionar.'], 422);
        }

        Response::json($this->cartState(true));
    }

    public function update(Request $request): never
    {
        $this->ensureCsrf($request);
        if (Auth::guest()) {
            Response::json(['success' => false, 'requireLogin' => true], 401);
        }

        $payload  = $this->readJsonPayload($request);
        $itemId   = (int) ($payload['item_id'] ?? 0);
        $quantity = (int) ($payload['quantity'] ?? 0);

        if ($itemId > 0) {
            $this->cart->updateQuantity($itemId, $quantity);
        }
        Response::json($this->cartState());
    }

    public function remove(Request $request): never
    {
        $this->ensureCsrf($request);
        if (Auth::guest()) {
            Response::json(['success' => false, 'requireLogin' => true], 401);
        }
        $payload = $this->readJsonPayload($request);
        $itemId  = (int) ($payload['item_id'] ?? 0);
        if ($itemId > 0) {
            $this->cart->remove($itemId);
        }
        Response::json($this->cartState());
    }

    public function state(Request $request): never
    {
        Response::json($this->cartState());
    }

    private function cartState(bool $success = true): array
    {
        if (Auth::guest()) {
            $html = $this->renderDrawer([], 0, 0.0, true);
            return ['success' => $success, 'authenticated' => false, 'count' => 0, 'subtotal' => 0, 'html' => $html];
        }

        $summary = $this->cart->summary();
        $items   = $summary['items'];
        $count   = (int) $summary['count'];
        $subtotal = (float) ($summary['subtotal'] ?? 0);

        return [
            'success'       => $success,
            'authenticated' => true,
            'count'         => $count,
            'subtotal'      => $subtotal,
            'subtotal_br'   => money_br($subtotal),
            'html'          => $this->renderDrawer($items, $count, $subtotal, false),
        ];
    }

    private function renderDrawer(array $items, int $count, float $subtotal, bool $guest): string
    {
        return $this->view->render('partials/cart_drawer_content', [
            'cartItems'    => $items,
            'cartCount'    => $count,
            'cartSubtotal' => $subtotal,
            'isGuest'      => $guest,
        ]);
    }

    private function readJsonPayload(Request $request): array
    {
        $body = file_get_contents('php://input') ?: '';
        if ($body === '') return [];
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function ensureCsrf(Request $request): void
    {
        $token = $request->header('X-CSRF-Token') ?: $request->csrfToken();
        if (!Csrf::check($token)) {
            Response::json(['success' => false, 'message' => 'CSRF inválido.'], 419);
        }
    }
}
