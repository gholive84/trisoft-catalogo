<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
<p class="text-sm text-gray-500 mt-1">Visão geral do catálogo.</p>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Orçamentos pendentes</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) ($kpis['pending_quotes'] ?? 0)) ?></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Novos clientes (30d)</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) ($kpis['new_customers_30d'] ?? 0)) ?></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Produtos ativos</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) ($kpis['active_products'] ?? 0)) ?></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Carrinhos abandonados</div>
        <div class="text-3xl font-bold mt-2"><?= e((string) ($kpis['abandoned_carts'] ?? 0)) ?></div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
    <h2 class="font-semibold text-gray-900">Atalhos</h2>
    <div class="mt-3 flex flex-wrap gap-2">
        <a href="<?= e(url('admin/produtos')) ?>" class="px-3 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800">+ Novo produto</a>
        <a href="<?= e(url('admin/categorias')) ?>" class="px-3 py-2 text-sm bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300">+ Nova categoria</a>
        <a href="<?= e(url('admin/orcamentos')) ?>" class="px-3 py-2 text-sm bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300">Ver orçamentos</a>
    </div>
</div>
<?php $this->endSection(); ?>
