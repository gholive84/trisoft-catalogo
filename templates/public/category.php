<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<section class="max-w-7xl mx-auto px-6 lg:px-10 pt-12 lg:pt-16 pb-6 text-center">
    <h1 class="display text-5xl md:text-6xl lg:text-7xl text-brand-ink"><?= e($category['name']) ?></h1>
    <?php if (!empty($category['description'])): ?>
        <p class="text-brand-muted mt-4 text-base md:text-lg max-w-2xl mx-auto"><?= e($category['description']) ?></p>
    <?php else: ?>
        <p class="text-brand-muted mt-4 text-sm"><?= e((string) $pagination['total']) ?> produto(s) nesta categoria</p>
    <?php endif; ?>
</section>

<section class="max-w-7xl mx-auto px-6 lg:px-10 py-8 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-10">
    <aside class="lg:sticky lg:top-8 h-fit">
        <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-3 px-3">Categorias</div>
        <nav class="flex flex-col gap-0.5">
            <a href="<?= e(url('/')) ?>" class="px-3 py-2.5 rounded-lg text-sm flex items-center justify-between text-brand-ink hover:bg-gray-50">
                <span>Todos os Produtos</span>
            </a>
            <?php
            $renderCategory = function (array $node, int $depth = 0) use (&$renderCategory, $activeCategory) {
                $isActive = $activeCategory === $node['slug'];
                $hasChildren = !empty($node['children']);
                $href = url('categoria/' . $node['slug']);
            ?>
                <div x-data="{ open: <?= ($hasChildren && category_contains_active($node, $activeCategory)) ? 'true' : 'false' ?> }">
                    <div class="flex items-center">
                        <a href="<?= e($href) ?>"
                           style="padding-left: <?= 12 + ($depth * 16) ?>px"
                           class="flex-1 pr-3 py-2.5 rounded-lg text-sm <?= $isActive ? 'bg-brand-ink text-white' : 'text-brand-ink hover:bg-gray-50' ?>">
                            <?= e($node['name']) ?>
                        </a>
                        <?php if ($hasChildren): ?>
                            <button type="button" @click="open = !open"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-muted hover:bg-gray-50">
                                <svg :class="open ? 'rotate-90' : ''" class="w-3 h-3 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($hasChildren): ?>
                        <div x-show="open" x-cloak class="flex flex-col gap-0.5">
                            <?php foreach ($node['children'] as $child) $renderCategory($child, $depth + 1); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php };
            foreach ($tree as $node) $renderCategory($node, 0); ?>
        </nav>
    </aside>

    <div>
        <?php if ($pagination['items'] === []): ?>
            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-16 text-center">
                <p class="text-brand-muted">Nenhum produto nesta categoria ainda.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                <?php foreach ($pagination['items'] as $product): ?>
                    <?php $this->partial('product_card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>

            <?php $this->partial('pagination', [
                'pagination' => $pagination,
                'baseUrl'    => url('categoria/' . $category['slug']),
                'query'      => ['sort' => $sort, 'min' => $minPrice, 'max' => $maxPrice],
            ]); ?>
        <?php endif; ?>
    </div>
</section>
<?php $this->endSection(); ?>
