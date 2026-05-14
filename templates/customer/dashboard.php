<?php /** @var App\Core\View $this */ $this->extend('layouts/customer'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => ['Solicitado',  'bg-amber-50 text-amber-800'],
    'quoted'          => ['Respondido',  'bg-brand-blue/10 text-brand-blue'],
    'approved'        => ['Aprovado',    'bg-brand-green/10 text-brand-green-dark'],
    'rejected'        => ['Rejeitado',   'bg-rose-50 text-rose-800'],
    'expired'         => ['Expirado',    'bg-gray-100 text-gray-700'],
    'pending_payment' => ['Aguardando pagto', 'bg-orange-50 text-orange-800'],
    'paid'            => ['Pago',        'bg-brand-green/10 text-brand-green-dark'],
    'processing'      => ['Em produção', 'bg-brand-blue/10 text-brand-blue'],
    'shipped'         => ['Enviado',     'bg-indigo-50 text-indigo-800'],
    'delivered'       => ['Entregue',    'bg-brand-green/10 text-brand-green-dark'],
    'canceled'        => ['Cancelado',   'bg-gray-100 text-gray-700'],
];
?>
<div>
    <h1 class="font-display text-3xl font-bold text-brand-ink">Olá, <?= e(explode(' ', auth()['name'])[0]) ?> 👋</h1>
    <p class="text-gray-600 mt-1">Bem-vindo(a) à sua área Trisoft.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
    <a href="<?= e(url('minha-conta/orcamentos')) ?>"
       class="bg-white rounded-2xl border border-gray-100 p-6 shadow-soft hover:shadow-brand hover:border-brand-blue/30 transition group">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 bg-brand-blue/10 rounded-xl flex items-center justify-center text-brand-blue">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-blue group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Orçamentos abertos</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) $kpis['open_quotes']) ?></div>
    </a>
    <a href="<?= e(url('carrinho')) ?>"
       class="bg-white rounded-2xl border border-gray-100 p-6 shadow-soft hover:shadow-brand hover:border-brand-green/30 transition group">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 bg-brand-green/10 rounded-xl flex items-center justify-center text-brand-green-dark">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-green-dark group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Itens no carrinho</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) $kpis['cart_items']) ?></div>
    </a>
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-soft">
        <div class="w-11 h-11 bg-brand-teal/10 rounded-xl flex items-center justify-center text-brand-teal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Pedidos concluídos</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) $kpis['completed']) ?></div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display font-bold text-brand-ink text-lg">Orçamentos recentes</h2>
        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="text-sm text-brand-blue hover:underline">Ver todos →</a>
    </div>

    <?php if ($recentOrders === []): ?>
        <div class="text-center py-10">
            <p class="text-sm text-gray-500">Nenhum orçamento ainda.</p>
            <a href="<?= e(url('/')) ?>" class="inline-flex items-center mt-3 text-brand-blue font-medium hover:underline">
                Explorar o catálogo →
            </a>
        </div>
    <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($recentOrders as $o):
                [$label, $color] = $statusLabels[$o['status']] ?? [$o['status'], 'bg-gray-100 text-gray-700'];
            ?>
                <li class="py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <a href="<?= e(url('minha-conta/orcamentos/' . $o['order_number'])) ?>" class="font-medium text-brand-ink hover:text-brand-blue truncate">
                            <?= e($o['order_number']) ?>
                        </a>
                        <div class="text-xs text-gray-400"><?= e(date_br($o['created_at'])) ?></div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold <?= e($color) ?>">
                            <?= e($label) ?>
                        </span>
                        <?php if (can_see_prices()): ?>
                            <div class="font-display font-bold text-brand-ink"><?= e(money_br((float) $o['total'])) ?></div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
