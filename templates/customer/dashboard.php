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
?>
<h1 class="text-2xl font-bold text-gray-900">Olá, <?= e(auth()['name']) ?></h1>
<p class="text-sm text-gray-500 mt-1">Bem-vindo(a) à sua conta.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-400 hover:shadow-sm transition">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Orçamentos abertos</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) $kpis['open_quotes']) ?></div>
    </a>
    <a href="<?= e(url('carrinho')) ?>" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-400 hover:shadow-sm transition">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Itens no carrinho</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) $kpis['cart_items']) ?></div>
    </a>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Pedidos concluídos</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) $kpis['completed']) ?></div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-gray-900">Orçamentos recentes</h2>
        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="text-sm text-gray-600 hover:text-gray-900">Ver todos →</a>
    </div>

    <?php if ($recentOrders === []): ?>
        <p class="text-sm text-gray-500">Nenhum orçamento ainda. <a href="<?= e(url('/')) ?>" class="text-gray-900 underline">Explorar o catálogo</a>.</p>
    <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($recentOrders as $o): ?>
                <li class="py-3 flex items-center justify-between">
                    <div>
                        <a href="<?= e(url('minha-conta/orcamentos/' . $o['order_number'])) ?>" class="font-medium text-gray-900 hover:underline">
                            <?= e($o['order_number']) ?>
                        </a>
                        <div class="text-xs text-gray-500"><?= e(date_br($o['created_at'])) ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-600"><?= e($statusLabels[$o['status']] ?? $o['status']) ?></div>
                        <?php if (can_see_prices()): ?>
                            <div class="text-sm font-medium text-gray-900"><?= e(money_br((float) $o['total'])) ?></div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
