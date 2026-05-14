<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<section class="max-w-7xl mx-auto px-6 lg:px-10 pt-12 lg:pt-16 pb-6 text-center">
    <h1 class="display text-5xl md:text-6xl text-brand-ink">Buscar</h1>
    <?php if ($query !== ''): ?>
        <p class="text-brand-muted mt-4">
            <?= e((string) $pagination['total']) ?> resultado(s) para "<strong class="text-brand-ink"><?= e($query) ?></strong>"
        </p>
    <?php endif; ?>

    <form method="get" action="<?= e(url('busca')) ?>" class="max-w-xl mx-auto mt-8">
        <div class="relative">
            <input type="search" name="q" value="<?= e($query) ?>" autofocus placeholder="Buscar produtos, SKU, descrição..."
                   class="w-full bg-gray-100 border border-transparent rounded-full pl-12 pr-6 py-3 text-sm placeholder:text-gray-400 focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
            <svg class="w-4 h-4 text-gray-400 absolute left-5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
        </div>
    </form>
</section>

<section class="max-w-7xl mx-auto px-6 lg:px-10 py-8">
    <?php if ($query === ''): ?>
        <div class="text-center text-brand-muted py-12">Digite uma busca acima para encontrar produtos.</div>
    <?php elseif ($pagination['items'] === []): ?>
        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-16 text-center">
            <p class="text-brand-muted">Nenhum produto encontrado.</p>
            <p class="text-xs text-gray-400 mt-1">Tente outras palavras.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10">
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
</section>
<?php $this->endSection(); ?>
