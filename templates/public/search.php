<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900">Buscar produtos</h1>

    <form method="get" action="<?= e(url('busca')) ?>" class="mt-6 max-w-2xl">
        <div class="relative">
            <input type="search" name="q" value="<?= e($query) ?>" autofocus
                   placeholder="Buscar por nome, SKU ou descrição..."
                   class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
        </div>
    </form>

    <?php if ($query === ''): ?>
        <p class="text-gray-500 mt-6">Digite acima para buscar produtos.</p>
    <?php else: ?>
        <div class="mt-6 text-sm text-gray-500">
            <?= e((string) $pagination['total']) ?> resultado(s) para "<strong><?= e($query) ?></strong>"
        </div>

        <?php if ($pagination['items'] === []): ?>
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-12 text-center text-gray-500 mt-6">
                Nenhum produto encontrado. Tente outras palavras.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
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
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
