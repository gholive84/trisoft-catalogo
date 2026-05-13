<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>

<section class="bg-gradient-to-b from-gray-900 to-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight">Catálogo Trisoft</h1>
        <p class="text-gray-300 mt-4 max-w-2xl mx-auto">
            Soluções acústicas. Navegue, monte seu orçamento e fale com nossa equipe.
        </p>
        <form method="get" action="<?= e(url('busca')) ?>" class="mt-8 max-w-xl mx-auto">
            <div class="relative">
                <input type="search" name="q" placeholder="Buscar produtos..."
                       class="w-full pl-12 pr-4 py-3 rounded-xl text-gray-900 focus:ring-2 focus:ring-white">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
            </div>
        </form>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex items-end justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Categorias</h2>
            <p class="text-sm text-gray-500 mt-1">Navegue por seção</p>
        </div>
    </div>

    <?php if ($rootCategories === []): ?>
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-12 text-center text-gray-500">
            <p>Nenhuma categoria cadastrada ainda.</p>
            <?php if (has_role('admin', 'editor')): ?>
                <a href="<?= e(url('admin/categorias')) ?>" class="text-gray-900 underline mt-2 inline-block">Cadastrar primeira categoria</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($rootCategories as $cat): ?>
                <a href="<?= e(url('categoria/' . $cat['slug'])) ?>"
                   class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-gray-400 hover:shadow-sm transition">
                    <div class="aspect-[4/3] bg-gray-100">
                        <?php if (!empty($cat['image_path'])): ?>
                            <img src="<?= e(upload_url('categories/' . $cat['image_path'])) ?>"
                                 alt="<?= e($cat['name']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-300 text-4xl font-light">
                                <?= e(mb_substr($cat['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="font-semibold text-gray-900 group-hover:text-gray-700"><?= e($cat['name']) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($featuredProducts !== []): ?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex items-end justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Em destaque</h2>
            <p class="text-sm text-gray-500 mt-1">Selecionados pela nossa equipe</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($featuredProducts as $product): ?>
            <?php $this->partial('product_card', ['product' => $product]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php $this->endSection(); ?>
