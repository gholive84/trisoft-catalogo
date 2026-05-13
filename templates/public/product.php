<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$crumbItems = array_map(
    fn ($c) => ['url' => url('categoria/' . $c['slug']), 'name' => $c['name']],
    $breadcrumbs
);
$crumbItems[] = ['url' => '#', 'name' => $product['name']];

$mainImage = $images[0]['file_path'] ?? null;
?>

<div class="max-w-7xl mx-auto px-4 py-6">
    <?php $this->partial('breadcrumb', ['items' => $crumbItems]); ?>
</div>

<div class="max-w-7xl mx-auto px-4 pb-12 grid grid-cols-1 lg:grid-cols-2 gap-10">

    <!-- Galeria -->
    <div x-data="{ main: <?= e(json_encode($mainImage ? upload_url('products/' . $mainImage) : null)) ?> }">
        <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-200">
            <template x-if="main">
                <img :src="main" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
            </template>
            <template x-if="!main">
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
                </div>
            </template>
        </div>
        <?php if (count($images) > 1): ?>
            <div class="grid grid-cols-5 gap-2 mt-3">
                <?php foreach ($images as $img):
                    $u = upload_url('products/' . $img['file_path']);
                ?>
                    <button type="button" @click="main = '<?= e($u) ?>'"
                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:border-gray-400">
                        <img src="<?= e($u) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Detalhes -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900"><?= e($product['name']) ?></h1>
        <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">SKU <?= e($product['sku']) ?></div>

        <div class="mt-6 flex items-baseline gap-3">
            <span class="text-3xl font-bold text-gray-900"><?= e(money_br((float) $product['price'])) ?></span>
            <span class="text-sm text-gray-500">à vista</span>
        </div>

        <?php if (!empty($product['short_description'])): ?>
            <p class="text-gray-700 mt-4"><?= e($product['short_description']) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= e(url('carrinho/adicionar')) ?>" class="mt-6 space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= e((string) $product['id']) ?>">

            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Quantidade:</label>
                <div class="inline-flex items-center border border-gray-300 rounded-lg" x-data="{ q: 1 }">
                    <button type="button" @click="q = Math.max(1, q-1)" class="px-3 py-2 text-gray-500 hover:text-gray-900">−</button>
                    <input type="number" name="quantity" min="1" x-model.number="q" class="w-14 text-center border-0 focus:ring-0">
                    <button type="button" @click="q++" class="px-3 py-2 text-gray-500 hover:text-gray-900">+</button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-900 text-white py-3 rounded-xl font-medium hover:bg-gray-800 inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/>
                </svg>
                Adicionar ao orçamento
            </button>
        </form>

        <?php if (!empty($product['description'])): ?>
            <div class="mt-8 prose prose-sm max-w-none">
                <h3 class="font-semibold text-gray-900">Descrição</h3>
                <div class="text-gray-700 mt-2 whitespace-pre-line"><?= e($product['description']) ?></div>
            </div>
        <?php endif; ?>

        <?php
        $specs = array_filter([
            'Peso (kg)'      => $product['weight_kg'] ?? null,
            'Largura (cm)'   => $product['width_cm'] ?? null,
            'Altura (cm)'    => $product['height_cm'] ?? null,
            'Comprimento'    => $product['length_cm'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');
        if ($specs !== []): ?>
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="font-semibold text-gray-900 mb-2">Especificações</h3>
                <dl class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($specs as $k => $v): ?>
                        <div class="flex justify-between border-b border-gray-100 py-1">
                            <dt class="text-gray-500"><?= e($k) ?></dt>
                            <dd class="text-gray-900 font-medium"><?= e((string) $v) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($related !== []): ?>
<section class="max-w-7xl mx-auto px-4 pb-16">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Produtos relacionados</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($related as $r): ?>
            <?php $this->partial('product_card', ['product' => $r]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php $this->endSection(); ?>
