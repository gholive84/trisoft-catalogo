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

<div class="max-w-7xl mx-auto px-4 pt-6">
    <?php $this->partial('breadcrumb', ['items' => $crumbItems]); ?>
</div>

<div class="max-w-7xl mx-auto px-4 py-8 lg:py-12 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

    <!-- Galeria -->
    <div x-data="{ main: <?= e(json_encode($mainImage ? upload_url('products/' . $mainImage) : null)) ?> }">
        <div class="aspect-square bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-soft">
            <template x-if="main">
                <img :src="main" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
            </template>
            <template x-if="!main">
                <div class="w-full h-full flex items-center justify-center text-gray-300 bg-brand-cream">
                    <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
                </div>
            </template>
        </div>
        <?php if (count($images) > 1): ?>
            <div class="grid grid-cols-5 gap-2.5 mt-3">
                <?php foreach ($images as $img):
                    $u = upload_url('products/' . $img['file_path']);
                ?>
                    <button type="button" @click="main = '<?= e($u) ?>'"
                            :class="main === '<?= e($u) ?>' ? 'border-brand-blue ring-2 ring-brand-blue/20' : 'border-gray-100 hover:border-brand-blue/40'"
                            class="aspect-square bg-white rounded-xl overflow-hidden border-2 transition">
                        <img src="<?= e($u) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Detalhes -->
    <div>
        <?php if (!empty($product['is_featured'])): ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-brand-green/10 text-brand-green-dark text-xs font-bold uppercase tracking-wider mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-2"></span>
                Produto em destaque
            </span>
        <?php endif; ?>

        <h1 class="font-display text-3xl md:text-4xl font-bold text-brand-ink"><?= e($product['name']) ?></h1>
        <div class="text-xs text-gray-400 uppercase tracking-widest mt-2">SKU <?= e($product['sku']) ?></div>

        <?php if (can_see_prices()): ?>
            <div class="mt-7 flex items-baseline gap-3">
                <span class="font-display text-4xl font-bold text-brand-ink"><?= e(money_br((float) $product['price'])) ?></span>
                <span class="text-sm text-gray-500">à vista</span>
                <?php if (can_see_cost() && !empty($product['cost'])): ?>
                    <span class="ml-2 text-xs text-gray-400 italic" title="Custo (visível apenas para vendedor/admin)">
                        custo <?= e(money_br((float) $product['cost'])) ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="mt-7 bg-brand-blue/5 border border-brand-blue/20 rounded-2xl p-5">
                <div class="text-xs font-bold uppercase tracking-wider text-brand-blue">Preço sob consulta</div>
                <p class="text-gray-700 text-sm mt-1.5">
                    Adicione ao orçamento e nossa equipe responde com proposta personalizada em até 1 dia útil.
                </p>
            </div>
        <?php endif; ?>

        <?php if (!empty($product['short_description'])): ?>
            <p class="text-gray-700 mt-6 leading-relaxed"><?= e($product['short_description']) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= e(url('carrinho/adicionar')) ?>" class="mt-8 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= e((string) $product['id']) ?>">

            <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700">Quantidade</label>
                <div class="inline-flex items-center bg-brand-cream rounded-xl p-1" x-data="{ q: 1 }">
                    <button type="button" @click="q = Math.max(1, q-1)" class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-brand-blue rounded-lg hover:bg-white transition">−</button>
                    <input type="number" name="quantity" min="1" x-model.number="q" class="w-12 text-center bg-transparent border-0 focus:ring-0 font-semibold">
                    <button type="button" @click="q++" class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-brand-blue rounded-lg hover:bg-white transition">+</button>
                </div>
            </div>

            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 bg-brand-blue text-white py-4 rounded-2xl font-semibold hover:bg-brand-blue-dark shadow-brand transition group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/>
                </svg>
                Adicionar ao orçamento
                <svg class="w-4 h-4 ml-1 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </form>

        <!-- Selos de confiança -->
        <div class="grid grid-cols-3 gap-3 mt-6">
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <svg class="w-5 h-5 text-brand-green shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <div><strong class="text-brand-ink">Orçamento</strong><br>personalizado</div>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <svg class="w-5 h-5 text-brand-green shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <div><strong class="text-brand-ink">Resposta</strong><br>em 1 dia útil</div>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <svg class="w-5 h-5 text-brand-green shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <div><strong class="text-brand-ink">Equipe</strong><br>especializada</div>
            </div>
        </div>

        <?php if (!empty($product['description'])): ?>
            <div class="mt-10 border-t border-gray-100 pt-8">
                <h3 class="font-display font-bold text-brand-ink text-lg mb-3">Sobre este produto</h3>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line"><?= e($product['description']) ?></div>
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
            <div class="mt-6 bg-brand-cream rounded-2xl p-5">
                <h3 class="font-display font-semibold text-brand-ink mb-3">Especificações técnicas</h3>
                <dl class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($specs as $k => $v): ?>
                        <div class="flex justify-between bg-white rounded-lg px-3 py-2">
                            <dt class="text-gray-500"><?= e($k) ?></dt>
                            <dd class="text-brand-ink font-semibold"><?= e((string) $v) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($related)): ?>
<section class="bg-white py-16 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="font-display text-2xl md:text-3xl font-bold text-brand-ink mb-8">Produtos relacionados</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <?php foreach ($related as $r): ?>
                <?php $this->partial('product_card', ['product' => $r]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php $this->endSection(); ?>
