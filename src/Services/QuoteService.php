<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use RuntimeException;

final class QuoteService
{
    public function __construct(
        private OrderRepository $orders = new OrderRepository(),
        private CartRepository $carts   = new CartRepository(),
        private UserRepository $users   = new UserRepository(),
        private MailService $mail       = new MailService(),
    ) {
    }

    /**
     * Converte o carrinho atual em um Order com status `quote_requested`.
     * Limpa o carrinho ao final. Dispara emails para cliente e vendedores.
     *
     * @return int order ID
     */
    public function createFromCart(int $userId, ?string $customerNotes = null): int
    {
        $cart = $this->carts->findByUserId($userId);
        if ($cart === null) {
            throw new RuntimeException('Nenhum carrinho ativo encontrado.');
        }
        $items = $this->carts->itemsWithProduct((int) $cart['id']);
        $items = array_values(array_filter($items, fn ($i) => (int) $i['is_active'] === 1 && $i['deleted_at'] === null));
        if ($items === []) {
            throw new RuntimeException('Seu carrinho está vazio.');
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $subtotal = 0.0;
            foreach ($items as $i) {
                $subtotal += (float) $i['price'] * (int) $i['quantity'];
            }

            $expirationDays = (int) Config::get('QUOTE_EXPIRATION_DAYS', 15);
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expirationDays} days"));

            $orderId = $this->orders->create([
                'order_number'       => $this->orders->nextOrderNumber(),
                'user_id'            => $userId,
                'created_by_user_id' => Auth::isStaff() ? Auth::id() : null,
                'status'             => 'quote_requested',
                'subtotal'           => $subtotal,
                'total'              => $subtotal,
                'customer_notes'     => $customerNotes,
                'expires_at'         => $expiresAt,
            ]);

            foreach ($items as $i) {
                $unit = (float) $i['price'];
                $qty  = (int) $i['quantity'];
                $this->orders->addItem($orderId, [
                    'product_id' => (int) $i['product_id'],
                    'quantity'   => $qty,
                    'unit_price' => $unit,
                    'total'      => $unit * $qty,
                    'product_snapshot' => [
                        'sku'           => $i['sku'],
                        'name'          => $i['name'],
                        'slug'          => $i['slug'],
                        'image_path'    => $i['main_image_path'] ?? null,
                        'price_at_time' => $unit,
                    ],
                ]);
            }

            $this->carts->clear((int) $cart['id']);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Logger::error('Falha ao criar orçamento a partir do carrinho', [
                'user_id' => $userId, 'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $this->dispatchNotifications($orderId, $userId);

        return $orderId;
    }

    private function dispatchNotifications(int $orderId, int $userId): void
    {
        $order = $this->orders->findById($orderId);
        $user  = $this->users->findById($userId);
        if ($order === null || $user === null) {
            return;
        }
        $items = $this->orders->itemsOf($orderId);

        // Email para o cliente
        $this->mail->send(
            (string) $user['email'],
            'Recebemos seu pedido de orçamento — ' . $order['order_number'],
            'quote_requested_customer',
            ['order' => $order, 'items' => $items, 'user' => $user]
        );

        // Email para vendedores
        $sellersTo = (string) Config::get('MAIL_SELLERS_TO', '');
        if ($sellersTo !== '') {
            $recipients = array_map('trim', explode(',', $sellersTo));
            $this->mail->send(
                $recipients,
                "Novo orçamento solicitado — {$order['order_number']}",
                'quote_requested_sellers',
                ['order' => $order, 'items' => $items, 'user' => $user]
            );
        }
    }

    /**
     * Vendedor responde com preços ajustados / descontos.
     */
    public function respond(int $orderId, int $sellerId, array $payload): void
    {
        $extra = array_intersect_key($payload, array_flip([
            'subtotal', 'discount', 'shipping_cost', 'total', 'internal_notes', 'customer_notes', 'expires_at',
        ]));
        $extra['quoted_by_user_id'] = $sellerId;
        $extra['quoted_at']         = date('Y-m-d H:i:s');

        $this->orders->updateStatus($orderId, 'quoted', $extra);
    }

    public function markApproved(int $orderId): void
    {
        $this->orders->updateStatus($orderId, 'approved', ['approved_at' => date('Y-m-d H:i:s')]);
    }

    public function markRejected(int $orderId): void
    {
        $this->orders->updateStatus($orderId, 'rejected');
    }

    public function expireOlderThanNow(): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            "UPDATE orders
                SET status = 'expired'
              WHERE status IN ('quote_requested', 'quoted')
                AND expires_at IS NOT NULL
                AND expires_at < NOW()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }
}
