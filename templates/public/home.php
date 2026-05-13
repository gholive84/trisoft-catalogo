<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<section class="bg-gradient-to-b from-gray-900 to-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight">Catálogo Trisoft</h1>
        <p class="text-gray-300 mt-4 max-w-2xl mx-auto">
            Navegue pelos produtos, monte seu orçamento e fale com nossa equipe.
        </p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="<?= e(url('busca')) ?>" class="px-5 py-3 bg-white text-gray-900 rounded-lg font-medium hover:bg-gray-100">
                Explorar produtos
            </a>
            <?php if (!auth()): ?>
                <a href="<?= e(url('cadastro')) ?>" class="px-5 py-3 border border-white/40 text-white rounded-lg font-medium hover:bg-white/10">
                    Criar conta
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-16">
    <h2 class="text-2xl font-bold text-gray-900">Categorias</h2>
    <p class="text-sm text-gray-500 mt-1">As categorias serão renderizadas a partir do banco no Sprint 2.</p>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-gray-400 transition cursor-pointer">
                <div class="w-12 h-12 bg-gray-100 rounded-lg mb-3"></div>
                <div class="font-semibold text-gray-900">Categoria <?= $i ?></div>
                <div class="text-xs text-gray-500 mt-1">Em breve</div>
            </div>
        <?php endfor; ?>
    </div>
</section>
<?php $this->endSection(); ?>
