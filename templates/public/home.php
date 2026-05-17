<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
// Pega TODOS os produtos ativos (sem filtrar por categoria) para a home/listagem geral.
// O filtro por categoria fica na lateral.
$tree           = $tree ?? [];
$allProducts    = $allProducts ?? [];
$activeCategory = $activeCategory ?? null;     // 'all' ou slug
$query          = $query ?? '';
?>
<section class="max-w-7xl mx-auto px-6 lg:px-10 pt-12 lg:pt-16 pb-6 text-center">
    <h1 class="display text-5xl md:text-6xl lg:text-7xl text-brand-ink">Nossos Produtos</h1>
    <p class="text-brand-muted mt-4 text-base md:text-lg">Soluções acústicas sustentáveis com design premium</p>

    <form method="get" action="<?= e(url('busca')) ?>" class="max-w-xl mx-auto mt-8">
        <div class="relative">
            <input type="search" name="q" value="<?= e($query) ?>" placeholder="Buscar produtos..."
                   class="w-full bg-gray-100 border border-transparent rounded-full pl-12 pr-6 py-3 text-sm placeholder:text-gray-400 focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
            <svg class="w-4 h-4 text-gray-400 absolute left-5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
        </div>
    </form>
</section>

<section class="max-w-7xl mx-auto px-6 lg:px-10 py-8 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-10">
    <!-- Sidebar de categorias -->
    <aside class="lg:sticky lg:top-8 h-fit">
        <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-3 px-3">Categorias</div>
        <nav class="flex flex-col gap-0.5">
            <a href="<?= e(url('/')) ?>"
               class="px-3 py-2.5 rounded-lg text-sm flex items-center justify-between <?= !$activeCategory ? 'bg-brand-ink text-white' : 'text-brand-ink hover:bg-gray-50' ?>">
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
            <?php
            };
            foreach ($tree as $node) $renderCategory($node, 0);
            ?>
        </nav>
    </aside>

    <!-- Grid de produtos -->
    <div>
        <?php if (empty($allProducts)): ?>
            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-16 text-center">
                <p class="text-brand-muted">Nenhum produto disponível ainda.</p>
                <?php if (has_role('admin', 'editor')): ?>
                    <a href="<?= e(url('admin/produtos')) ?>" class="inline-block text-brand-blue underline mt-2">Cadastrar produtos</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                <?php foreach ($allProducts as $product): ?>
                    <?php $this->partial('product_card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $this->endSection(); ?>
