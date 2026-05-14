<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<div class="flex items-end justify-between mb-8">
    <div>
        <h1 class="font-display text-3xl font-bold text-brand-ink">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Visão geral do catálogo Trisoft.</p>
    </div>
    <div class="text-sm text-gray-500">Olá, <strong class="text-brand-ink"><?= e(auth()['name']) ?></strong></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Orçamentos pendentes</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) ($kpis['pending_quotes'] ?? 0)) ?></div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-2a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Novos clientes (30d)</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) ($kpis['new_customers_30d'] ?? 0)) ?></div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-brand-green/10 text-brand-green-dark flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Produtos ativos</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) ($kpis['active_products'] ?? 0)) ?></div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            </div>
        </div>
        <div class="text-xs text-gray-500 uppercase tracking-wider mt-4">Carrinhos abandonados</div>
        <div class="font-display text-3xl font-bold text-brand-ink mt-1"><?= e((string) ($kpis['abandoned_carts'] ?? 0)) ?></div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 mt-6">
    <h2 class="font-display font-bold text-brand-ink mb-3">Atalhos</h2>
    <div class="flex flex-wrap gap-2">
        <a href="<?= e(url('admin/produtos')) ?>" class="px-4 py-2.5 text-sm bg-brand-blue text-white rounded-xl hover:bg-brand-blue-dark shadow-soft transition">+ Novo produto</a>
        <a href="<?= e(url('admin/categorias')) ?>" class="px-4 py-2.5 text-sm bg-brand-cream text-brand-ink rounded-xl hover:bg-gray-200 transition">+ Nova categoria</a>
        <a href="<?= e(url('admin/orcamentos')) ?>" class="px-4 py-2.5 text-sm bg-brand-cream text-brand-ink rounded-xl hover:bg-gray-200 transition">Ver orçamentos pendentes</a>
    </div>
</div>
<?php $this->endSection(); ?>
