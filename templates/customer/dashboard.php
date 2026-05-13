<?php /** @var App\Core\View $this */ $this->extend('layouts/customer'); ?>

<?php $this->section('content'); ?>
<h1 class="text-2xl font-bold text-gray-900">Olá, <?= e(auth()['name']) ?> 👋</h1>
<p class="text-sm text-gray-500 mt-1">Bem-vindo(a) à sua conta.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Orçamentos abertos</div>
        <div class="text-3xl font-bold mt-2">0</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Itens no carrinho</div>
        <div class="text-3xl font-bold mt-2">0</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Pedidos concluídos</div>
        <div class="text-3xl font-bold mt-2">0</div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
    <h2 class="font-semibold text-gray-900">Orçamentos recentes</h2>
    <p class="text-sm text-gray-500 mt-2">Nenhum orçamento ainda. <a href="<?= e(url('/')) ?>" class="text-gray-900 underline">Explorar o catálogo</a>.</p>
</div>
<?php $this->endSection(); ?>
