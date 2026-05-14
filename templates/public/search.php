<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<section class="bg-brand-radial text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 lg:py-16">
        <h1 class="font-display text-3xl md:text-5xl font-bold">Buscar produtos</h1>

        <form method="get" action="<?= e(url('busca')) ?>" class="mt-6 max-w-2xl">
            <div class="relative">
                <input type="search" name="q" value="<?= e($query) ?>" autofocus
                       placeholder="Buscar por nome, SKU ou descrição..."
                       class="w-full pl-12 pr-4 py-4 rounded-2xl text-brand-ink bg-white/95 backdrop-blur placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
            </div>
        </form>

        <?php if ($query !== ''): ?>
            <div class="mt-4 text-sm text-white/80">
                <?= e((string) $pagination['total']) ?> resultado(s) para "<strong class="text-white"><?= e($query) ?></strong>"
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 py-10">
    <?php if ($query === ''): ?>
        <div class="text-center text-gray-500 py-12">Digite uma busca acima para encontrar produtos.</div>
    <?php elseif ($pagination['items'] === []): ?>
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 mt-4">Nenhum produto encontrado.</p>
            <p class="text-xs text-gray-400 mt-1">Tente outras palavras ou explore as categorias.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <?php foreach ($pagination['items'] as $product): ?>
                <?php $this->partial('product_card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>

        <?php $this->partial('pagination', [
            'pagination' => $pagination,
            'baseUrl'    => url('busca'),
            'query'      => ['q' => $query],
        ]); ?>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
