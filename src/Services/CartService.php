<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Session;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;

final class CartService
{
    public function __construct(
        private CartRepository $carts = new CartRepository(),
        private ProductRepository $products = new ProductRepository(),
    ) {
    }

    /**
     * Carrinho ativo do usuário atual (logado) ou da sessão (visitante).
     */
    public function currentCart(): array
    {
        if (Auth::check()) {
            return $this->carts->findOrCreateForUser((int) Auth::id());
        }
        return $this->carts->findOrCreateForSession(session_id());
    }

    /**
     * @return array{items: array, count: int, subtotal: float}
     */
    public function summary(): array
    {
        $cart  = $this->currentCart();
        $items = $this->carts->itemsWithProduct((int) $cart['id']);

        $count = 0;
        $subtotal = 0.0;
        foreach ($items as $i) {
            $count += (int) $i['quantity'];
            $subtotal += (float) $i['price'] * (int) $i['quantity'];
        }

        return [
            'cart'     => $cart,
            'items'    => $items,
            'count'    => $count,
            'subtotal' => $subtotal,
        ];
    }

    public function add(int $productId, int $quantity = 1): bool
    {
        $product = $this->products->findById($productId);
        if ($product === null || !$product['is_active']) {
            return false;
        }
        $cart = $this->currentCart();
        $this->carts->addItem((int) $cart['id'], $productId, max(1, $quantity));
        return true;
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = $this->currentCart();
        $this->carts->updateItemQuantity((int) $cart['id'], $itemId, $quantity);
    }

    public function remove(int $itemId): void
    {
        $cart = $this->currentCart();
        $this->carts->removeItem((int) $cart['id'], $itemId);
    }

    public function clear(): void
    {
        $cart = $this->currentCart();
        $this->carts->clear((int) $cart['id']);
    }

    /**
     * Mescla o carrinho de visitante (session) no carrinho do usuário recém-logado.
     * Chame esta função imediatamente após Auth::login().
     */
    public function mergeGuestIntoUser(int $userId, ?string $previousSessionId = null): void
    {
        $sid = $previousSessionId ?? session_id();
        $guestCart = $this->carts->findBySessionId($sid);
        if ($guestCart === null) {
            return;
        }
        $userCart = $this->carts->findByUserId($userId);

        if ($userCart === null) {
            // Promove o cart do guest para o usuário
            $this->carts->assignToUser((int) $guestCart['id'], $userId);
            return;
        }

        // Move itens (soma quantidades em duplicatas)
        $guestItems = $this->carts->itemsWithProduct((int) $guestCart['id']);
        foreach ($guestItems as $i) {
            $this->carts->addItem((int) $userCart['id'], (int) $i['product_id'], (int) $i['quantity']);
        }
        $this->carts->delete((int) $guestCart['id']);
    }

    public function badge(): int
    {
        try {
            return $this->summary()['count'];
        } catch (\Throwable) {
            return 0;
        }
    }
}
