<?php /** @var App\Core\View $this */ $this->extend('layouts/customer'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => 'Solicitado',
    'quoted'          => 'Respondido',
    'approved'        => 'Aprovado',
    'rejected'        => 'Rejeitado',
    'expired'         => 'Expirado',
    'pending_payment' => 'Aguardando pagamento',
    'paid'            => 'Pago',
    'processing'      => 'Em produção',
    'shipped'         => 'Enviado',
    'delivered'       => 'Entregue',
    'canceled'        => 'Cancelado',
];
$showPriceForCustomer = in_array($order['status'], ['quoted', 'approved', 'pending_payment', 'paid', 'processing', 'shipped', 'delivered'], true);
?>

<div class="flex items-start justify-between gap-4">
    <div>
        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="text-sm text-gray-500 hover:text-gray-700">← Voltar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">Orçamento <?= e($order['order_number']) ?></h1>
        <p class="text-sm text-gray-500 mt-1">Criado em <?= e(date_br($order['created_at'], 'd/m/Y H:i')) ?></p>
    </div>
    <div class="text-right">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Status</div>
        <div class="text-lg font-semibold text-gray-900"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></div>
        <?php if ($order['expires_at']): ?>
            <div class="text-xs text-gray-500 mt-1">Válido até <?= e(date_br($order['expires_at'], 'd/m/Y')) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden mt-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Item</th>
                <th class="px-4 py-3 text-right">Qtd</th>
                <th class="px-4 py-3 text-right">Preço unit.</th>
                <th class="px-4 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($items as $i):
                $snap = is_array($i['product_snapshot'])
                    ? $i['product_snapshot']
                    : (json_decode((string) $i['product_snapshot'], true) ?: []);
            ?>
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900"><?= e($snap['name'] ?? '—') ?></div>
                        <div class="text-xs text-gray-500">SKU <?= e($snap['sku'] ?? '') ?></div>
                    </td>
                    <td class="px-4 py-3 text-right"><?= e((string) $i['quantity']) ?></td>
                    <td class="px-4 py-3 text-right">
                        <?= ($showPriceForCustomer || can_see_prices())
                              ? e(money_br((float) $i['unit_price']))
                              : '—' ?>
                    </td>
                    <td class="px-4 py-3 text-right font-medium">
                        <?= ($showPriceForCustomer || can_see_prices())
                              ? e(money_br((float) $i['total']))
                              : '—' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if ($showPriceForCustomer || can_see_prices()): ?>
        <tfoot class="bg-gray-50 text-gray-900 font-semibold text-sm">
            <tr>
                <td colspan="3" class="px-4 py-3 text-right">Subtotal</td>
                <td class="px-4 py-3 text-right"><?= e(money_br((float) $order['subtotal'])) ?></td>
            </tr>
            <?php if ((float) $order['discount'] > 0): ?>
            <tr>
                <td colspan="3" class="px-4 py-3 text-right text-green-700">Desconto</td>
                <td class="px-4 py-3 text-right text-green-700">− <?= e(money_br((float) $order['discount'])) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ((float) $order['shipping_cost'] > 0): ?>
            <tr>
                <td colspan="3" class="px-4 py-3 text-right">Frete</td>
                <td class="px-4 py-3 text-right"><?= e(money_br((float) $order['shipping_cost'])) ?></td>
            </tr>
            <?php endif; ?>
            <tr class="text-base">
                <td colspan="3" class="px-4 py-3 text-right">Total</td>
                <td class="px-4 py-3 text-right"><?= e(money_br((float) $order['total'])) ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<?php if (!empty($order['customer_notes'])): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-5 mt-6">
        <h3 class="font-semibold text-gray-900 text-sm mb-2">Observações</h3>
        <p class="text-sm text-gray-700 whitespace-pre-line"><?= e($order['customer_notes']) ?></p>
    </div>
<?php endif; ?>

<?php if ($order['status'] === 'quote_requested'): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mt-6 text-sm text-amber-900">
        Seu pedido de orçamento foi recebido. Nossa equipe responderá em até 1 dia útil.
    </div>
<?php endif; ?>
<?php $this->endSection(); ?>
