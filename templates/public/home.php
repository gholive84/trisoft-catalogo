<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>

<!-- HERO -->
<section class="relative overflow-hidden bg-brand-radial text-white">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-brand-green/40 blur-3xl"></div>
        <div class="absolute bottom-0 -right-32 w-96 h-96 rounded-full bg-brand-teal/40 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-28 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur text-xs font-medium uppercase tracking-widest mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span>
                Catálogo Trisoft
            </div>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05]">
                Revestimentos<br>
                <span class="text-brand-green">funcionais</span> que<br>
                transformam ambientes.
            </h1>
            <p class="text-white/80 text-lg mt-6 max-w-xl">
                Painéis acústicos, difusores e absorvedores de alta performance para arquitetura, áudio profissional e estúdios de gravação.
            </p>

            <form method="get" action="<?= e(url('busca')) ?>" class="mt-8 max-w-xl">
                <div class="relative">
                    <input type="search" name="q" placeholder="Buscar produtos, SKU, categoria..."
                           class="w-full pl-12 pr-32 py-4 rounded-2xl text-brand-ink bg-white/95 backdrop-blur placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-brand-blue text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-brand-blue-dark transition">
                        Buscar
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-6 mt-10 text-sm text-white/70">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-green" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Orçamento personalizado
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-green" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Resposta em 1 dia útil
                </div>
            </div>
        </div>

        <!-- Mascote em destaque -->
        <div class="hidden lg:flex justify-end items-end relative">
            <div class="relative">
                <div class="absolute inset-0 bg-brand-green/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <img src="<?= e(asset('images/logo-mark.png')) ?>" alt="Trisoft" class="h-64 w-auto">
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/10">
                        <div class="text-center">
                            <div class="font-display text-2xl font-bold text-brand-green"><?= e((string) count($rootCategories ?? [])) ?>+</div>
                            <div class="text-xs text-white/70 uppercase tracking-wide mt-1">Categorias</div>
                        </div>
                        <div class="text-center border-l border-r border-white/10">
                            <div class="font-display text-2xl font-bold text-brand-green">30</div>
                            <div class="text-xs text-white/70 uppercase tracking-wide mt-1">Anos</div>
                        </div>
                        <div class="text-center">
                            <div class="font-display text-2xl font-bold text-brand-green">100%</div>
                            <div class="text-xs text-white/70 uppercase tracking-wide mt-1">Nacional</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIAS -->
<section class="max-w-7xl mx-auto px-4 py-16 lg:py-20">
    <div class="flex items-end justify-between mb-10">
        <div>
            <div class="text-xs uppercase tracking-widest text-brand-blue font-semibold">Catálogo</div>
            <h2 class="font-display text-3xl md:text-4xl font-bold text-brand-ink mt-2">Navegue por categoria</h2>
        </div>
        <a href="<?= e(url('busca')) ?>" class="hidden sm:inline-flex items-center gap-1 text-brand-blue font-medium hover:underline">
            Ver todos
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <?php if (empty($rootCategories)): ?>
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-16 text-center">
            <p class="text-gray-500">Nenhuma categoria cadastrada ainda.</p>
            <?php if (has_role('admin', 'editor')): ?>
                <a href="<?= e(url('admin/categorias')) ?>" class="inline-block text-brand-blue underline mt-2">Cadastrar primeira categoria</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <?php
            $gradients = [
                'from-brand-blue/90 to-brand-teal/80',
                'from-brand-teal/90 to-brand-green/80',
                'from-brand-green/90 to-brand-blue/80',
                'from-brand-blue-900/90 to-brand-blue/80',
            ];
            foreach ($rootCategories as $i => $cat):
                $gradient = $gradients[$i % count($gradients)];
            ?>
                <a href="<?= e(url('categoria/' . $cat['slug'])) ?>"
                   class="group relative bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-brand transition-all duration-300 hover:-translate-y-1">
                    <div class="aspect-[4/3] bg-gradient-to-br <?= $gradient ?> relative overflow-hidden">
                        <?php if (!empty($cat['image_path'])): ?>
                            <img src="<?= e(upload_url('categories/' . $cat['image_path'])) ?>"
                                 alt="<?= e($cat['name']) ?>"
                                 class="w-full h-full object-cover mix-blend-overlay group-hover:scale-105 transition duration-500">
                        <?php else: ?>
                            <!-- Pattern decorativo quando não há imagem -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/30" fill="currentColor" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="20"/>
                                    <circle cx="20" cy="20" r="8" opacity="0.5"/>
                                    <circle cx="80" cy="80" r="12" opacity="0.5"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent"></div>
                    </div>
                    <div class="p-5">
                        <div class="font-display font-bold text-brand-ink group-hover:text-brand-blue transition"><?= e($cat['name']) ?></div>
                        <div class="text-xs text-brand-blue font-medium mt-2 inline-flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            Explorar
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- FEATURED -->
<?php if (!empty($featuredProducts)): ?>
<section class="bg-white py-16 lg:py-20 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <div class="text-xs uppercase tracking-widest text-brand-green-dark font-semibold">Selecionados</div>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-brand-ink mt-2">Em destaque</h2>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <?php foreach ($featuredProducts as $product): ?>
                <?php $this->partial('product_card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="max-w-7xl mx-auto px-4 py-16 lg:py-20">
    <div class="relative overflow-hidden bg-brand-blue rounded-3xl p-10 md:p-16 text-white shadow-brand">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-brand-green/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-brand-teal/30 rounded-full blur-3xl"></div>

        <div class="relative max-w-2xl">
            <h2 class="font-display text-3xl md:text-4xl font-bold leading-tight">
                Pronto para começar seu projeto?
            </h2>
            <p class="text-white/85 mt-4 text-lg">
                Monte seu orçamento online e receba uma proposta personalizada da nossa equipe em até 1 dia útil.
            </p>
            <div class="flex flex-wrap items-center gap-3 mt-8">
                <?php if (!auth()): ?>
                    <a href="<?= e(url('cadastro')) ?>" class="inline-flex items-center px-6 py-3.5 rounded-xl bg-white text-brand-blue font-semibold hover:bg-brand-cream transition shadow-soft">
                        Criar conta gratuita
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php else: ?>
                    <a href="<?= e(url('carrinho')) ?>" class="inline-flex items-center px-6 py-3.5 rounded-xl bg-white text-brand-blue font-semibold hover:bg-brand-cream transition shadow-soft">
                        Ver meu orçamento
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
                <a href="<?= e(url('busca')) ?>" class="inline-flex items-center px-6 py-3.5 rounded-xl border border-white/30 hover:bg-white/10 font-medium transition">
                    Explorar catálogo
                </a>
            </div>
        </div>
    </div>
</section>
<?php $this->endSection(); ?>
