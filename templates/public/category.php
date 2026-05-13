<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$crumbItems = array_map(
    fn ($c) => ['url' => url('categoria/' . $c['slug']), 'name' => $c['name']],
    $breadcrumbs
);
?>

<div class="max-w-7xl mx-auto px-4 py-6">
    <?php $this->partial('breadcrumb', ['items' => $crumbItems]); ?>
</div>

<div class="max-w-7xl mx-auto px-4 pb-12 grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-8">

    <!-- Sidebar de filtros -->
    <aside class="lg:sticky lg:top-20 h-fit">
        <?php if ($children !== []): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
                <h3 class="font-semibold text-gray-900 mb-2">Subcategorias</h3>
                <ul class="space-y-1 text-sm">
                    <?php foreach ($children as $sub): ?>
                        <li>
                            <a href="<?= e(url('categoria/' . $sub['slug'])) ?>"
                               class="text-gray-700 hover:text-gray-900 hover:underline">
                                <?= e($sub['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="get" class="bg-white border border-gray-200 rounded-xl p-4 space-y-4">
            <h3 class="font-semibold text-gray-900">Filtros</h3>

            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Faixa de preço</label>
                <div class="flex gap-2">
                    <input type="number" name="min" value="<?= e($minPrice ?? '') ?>" placeholder="Mín"
                           step="0.01" min="0"
                           class="w-full text-sm border border-gray-300 rounded px-2 py-1.5 focus:ring-2 focus:ring-gray-900">
                    <input type="number" name="max" value="<?= e($maxPrice ?? '') ?>" placeholder="Máx"
                           step="0.01" min="0"
                           class="w-full text-sm border border-gray-300 rounded px-2 py-1.5 focus:ring-2 focus:ring-gray-900">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Ordenar por</label>
                <select name="sort" class="w-full text-sm border border-gray-300 rounded px-2 py-1.5 focus:ring-2 focus:ring-gray-900">
                    <?php
                    $opts = [
                        'newest'     => 'Mais recentes',
                        'price_asc'  => 'Menor preço',
                        'price_desc' => 'Maior preço',
                        'name_asc'   => 'Nome A-Z',
                        'name_desc'  => 'Nome Z-A',
                    ];
                    foreach ($opts as $val => $label):
                        $sel = $sort === $val ? ' selected' : '';
                    ?>
                        <option value="<?= e($val) ?>"<?= $sel ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded text-sm font-medium hover:bg-gray-800">
                Aplicar filtros
            </button>
        </form>
    </aside>

    <!-- Conteúdo -->
    <main>
        <h1 class="text-3xl font-bold text-gray-900"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="text-gray-600 mt-2"><?= e($category['description']) ?></p>
        <?php endif; ?>

        <div class="mt-4 text-sm text-gray-500">
            <?= e((string) $pagination['total']) ?> produto(s)
        </div>

        <?php if ($pagination['items'] === []): ?>
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-12 text-center text-gray-500 mt-6">
                Nenhum produto encontrado nesta categoria.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
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
