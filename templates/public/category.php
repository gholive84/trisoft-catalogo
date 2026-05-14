<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$crumbItems = array_map(
    fn ($c) => ['url' => url('categoria/' . $c['slug']), 'name' => $c['name']],
    $breadcrumbs
);
?>

<!-- Header da categoria -->
<section class="bg-brand-radial text-white">
    <div class="max-w-7xl mx-auto px-4 pt-8 pb-12 lg:pt-10 lg:pb-16">
        <?php $this->partial('breadcrumb', ['items' => $crumbItems, 'darkMode' => true]); ?>

        <h1 class="font-display text-3xl md:text-5xl font-bold mt-4 max-w-3xl"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="text-white/80 mt-4 max-w-2xl text-lg"><?= e($category['description']) ?></p>
        <?php endif; ?>

        <div class="flex items-center gap-2 mt-6 text-sm text-white/70">
            <span class="font-semibold text-white"><?= e((string) $pagination['total']) ?></span>
            produto(s) nesta categoria
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">

    <!-- Sidebar de filtros -->
    <aside class="lg:sticky lg:top-24 h-fit space-y-4">
        <?php if (!empty($children)): ?>
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-soft">
                <h3 class="font-display font-semibold text-brand-ink mb-3 text-sm uppercase tracking-wider">Subcategorias</h3>
                <ul class="space-y-1.5">
                    <?php foreach ($children as $sub): ?>
                        <li>
                            <a href="<?= e(url('categoria/' . $sub['slug'])) ?>"
                               class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-brand-cream hover:text-brand-blue transition">
                                <?= e($sub['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="get" class="bg-white border border-gray-100 rounded-2xl p-5 shadow-soft space-y-5">
            <h3 class="font-display font-semibold text-brand-ink text-sm uppercase tracking-wider">Filtros</h3>

            <?php if (can_see_prices()): ?>
                <div>
                    <label class="block text-xs text-gray-500 uppercase tracking-wide mb-2 font-medium">Faixa de preço</label>
                    <div class="flex gap-2">
                        <input type="number" name="min" value="<?= e($minPrice ?? '') ?>" placeholder="Mín"
                               step="0.01" min="0"
                               class="w-full bg-brand-cream border border-transparent rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="number" name="max" value="<?= e($maxPrice ?? '') ?>" placeholder="Máx"
                               step="0.01" min="0"
                               class="w-full bg-brand-cream border border-transparent rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-2 font-medium">Ordenar por</label>
                <select name="sort" class="w-full bg-brand-cream border border-transparent rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    <?php
                    $opts = ['newest' => 'Mais recentes'];
                    if (can_see_prices()) {
                        $opts['price_asc']  = 'Menor preço';
                        $opts['price_desc'] = 'Maior preço';
                    }
                    $opts['name_asc']  = 'Nome A-Z';
                    $opts['name_desc'] = 'Nome Z-A';
                    foreach ($opts as $val => $label):
                        $sel = $sort === $val ? ' selected' : '';
                    ?>
                        <option value="<?= e($val) ?>"<?= $sel ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-brand-blue text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-brand-blue-dark shadow-soft transition">
                Aplicar filtros
            </button>
        </form>
    </aside>

    <!-- Conteúdo -->
    <main>
        <?php if ($pagination['items'] === []): ?>
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-16 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500 mt-4">Nenhum produto encontrado nesta categoria.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
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
    </main>
</div>
<?php $this->endSection(); ?>
