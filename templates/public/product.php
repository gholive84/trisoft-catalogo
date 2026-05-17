<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$mainImage = $images[0]['file_path'] ?? null;
$heroImage = $product['hero_image_path'] ?? $mainImage;
$heroUrl   = $heroImage ? upload_url('products/' . $heroImage) : null;

$specs = !empty($product['specifications'])
    ? (is_array($product['specifications']) ? $product['specifications'] : json_decode((string) $product['specifications'], true))
    : [];
$specs = is_array($specs) ? $specs : [];

$primaryCategory = $categories[0] ?? null;
?>

<!-- HERO com efeito Ken Burns + CTAs sobre a imagem -->
<section class="relative">
    <div class="h-[55vh] md:h-[65vh] lg:h-[72vh] max-h-[760px] bg-gray-900 overflow-hidden relative">
        <?php if ($heroUrl): ?>
            <!-- Ken Burns: zoom + pan lento (começa em scale=1 para preservar nitidez) -->
            <div class="absolute inset-0 ken-burns">
                <img src="<?= e($heroUrl) ?>" alt="<?= e($product['name']) ?>"
                     class="w-full h-full object-cover"
                     fetchpriority="high"
                     decoding="async">
            </div>
            <!-- Gradient só no rodapé (área superior da imagem fica limpa) -->
            <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none"></div>
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
            </div>
        <?php endif; ?>

        <div class="absolute bottom-0 left-0 right-0 px-6 lg:px-10 pb-10 lg:pb-14 text-white">
            <div class="max-w-content mx-auto">
                <?php if ($primaryCategory): ?>
                    <div class="text-xs uppercase tracking-widest opacity-80 mb-3">
                        <a href="<?= e(url('categoria/' . $primaryCategory['slug'])) ?>" class="hover:text-white"><?= e($primaryCategory['name']) ?></a>
                    </div>
                <?php endif; ?>
                <h1 class="display text-3xl md:text-5xl lg:text-6xl uppercase tracking-tight max-w-3xl drop-shadow-lg"><?= e($product['name']) ?></h1>
                <?php if (!empty($product['subtitle'])): ?>
                    <p class="text-base md:text-lg mt-3 uppercase tracking-widest opacity-90 drop-shadow"><?= e($product['subtitle']) ?></p>
                <?php endif; ?>

                <!-- CTAs sobre o hero -->
                <div class="flex flex-col sm:flex-row gap-3 mt-7">
                    <button type="button"
                            onclick="addToCart(<?= (int) $product['id'] ?>, 1, this)"
                            class="inline-flex items-center justify-center gap-2 bg-brand-blue text-white px-7 py-3.5 rounded-full font-medium hover:bg-brand-blue-dark transition shadow-2xl disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                        Adicionar ao Carrinho
                    </button>
                    <button type="button"
                            onclick="addToCart(<?= (int) $product['id'] ?>, 1, this).then(() => setTimeout(() => window.location.href='<?= e(url('carrinho')) ?>', 400))"
                            class="inline-flex items-center justify-center gap-2 bg-white/95 backdrop-blur text-brand-ink px-7 py-3.5 rounded-full font-medium hover:bg-white transition shadow-2xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Solicitar Orçamento
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Ken Burns sutil: começa em 1:1 (mantém nitidez) e expande lentamente.
       Cobertura via padrão CSS: o IMG enche o container e o transform aplica
       no wrapper sem cropar (overflow:hidden no parent já cuida). */
    @keyframes ken-burns {
        0%   { transform: scale(1.00) translate(0, 0); }
        50%  { transform: scale(1.07) translate(-1%, -0.6%); }
        100% { transform: scale(1.00) translate(0, 0); }
    }
    .ken-burns { animation: ken-burns 28s ease-in-out infinite; will-change: transform; transform-origin: center; }
    .ken-burns img { width: 100%; height: 100%; object-fit: cover; image-rendering: -webkit-optimize-contrast; }
</style>

<!-- CONTEÚDO (max 1280px) -->
<section class="max-w-content mx-auto px-6 lg:px-10 py-10 lg:py-14">

    <div class="text-sm text-brand-muted flex items-center gap-2 mb-6">
        <a href="<?= e(url('/')) ?>" class="hover:text-brand-ink flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar
        </a>
        <?php if ($primaryCategory): ?>
            <span class="text-gray-300">/</span>
            <a href="<?= e(url('categoria/' . $primaryCategory['slug'])) ?>" class="hover:text-brand-ink"><?= e($primaryCategory['name']) ?></a>
        <?php endif; ?>
    </div>

    <?php if (!empty($product['short_description'])): ?>
        <p class="text-lg text-brand-ink leading-relaxed mb-10 max-w-3xl"><?= e($product['short_description']) ?></p>
    <?php endif; ?>

    <?php if (count($images) > 1): ?>
        <div class="mb-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php foreach (array_slice($images, 0, 4) as $img):
                    $u = upload_url('products/' . $img['file_path']);
                ?>
                    <a href="<?= e($u) ?>" target="_blank" class="aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden hover:opacity-90 transition block">
                        <img src="<?= e($u) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($specs)): ?>
        <!-- Imagem técnica de dimensões (quando o PDF tem o desenho com cotas) -->
        <?php if (!empty($product['dimensions_image_path'])): ?>
            <div class="text-center mb-8">
                <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-4">Dimensões</div>
                <img src="<?= e(upload_url('products/' . $product['dimensions_image_path'])) ?>"
                     alt="Diagrama de dimensões"
                     class="max-w-full mx-auto"
                     style="max-height: 260px;"
                     loading="lazy">
            </div>
        <?php endif; ?>

        <!-- Sugestões de Modulação (apenas se o PDF tinha) -->
        <?php if (!empty($product['modulation_image_path'])): ?>
            <div class="text-center mb-6">
                <div class="text-xs uppercase tracking-widest text-brand-muted font-medium">Sugestões de Modulação</div>
                <div class="mt-5">
                    <img src="<?= e(upload_url('products/' . $product['modulation_image_path'])) ?>"
                         alt="Sugestões de modulação"
                         class="max-w-full mx-auto"
                         style="max-height: 200px;"
                         loading="lazy">
                </div>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto mb-10 border border-brand-line rounded-2xl">
            <table class="w-full text-sm">
                <thead class="border-b border-brand-line">
                    <tr class="text-xs uppercase tracking-widest text-brand-muted bg-gray-50">
                        <?php
                        $columns = [
                            'code'           => 'Code',
                            'thickness'      => 'Thickness',
                            'a'              => '"A" (mm)',
                            'b'              => '"B" (mm)',
                            'c'              => '"C" (mm)',
                            'd'              => '"D" (mm)',
                            'pieces_per_box' => 'Peças/cx',
                            'coverage_area'  => 'Cobertura',
                            'pet_bottles'    => 'PET Bottles',
                        ];
                        $firstSpec = $specs[0] ?? [];
                        $presentColumns = array_intersect_key($columns, $firstSpec);
                        foreach ($presentColumns as $key => $label): ?>
                            <th class="px-4 py-3 text-left font-medium"><?= e($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-line bg-white">
                    <?php foreach ($specs as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <?php foreach ($presentColumns as $key => $_): ?>
                                <td class="px-4 py-3 text-brand-ink">
                                    <?= e((string) ($row[$key] ?? '—')) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (can_see_prices()): ?>
        <div class="text-center mb-8">
            <div class="display text-4xl md:text-5xl text-brand-ink"><?= e(money_br((float) $product['price'])) ?></div>
            <div class="text-xs uppercase tracking-widest text-brand-muted mt-1">À vista</div>
            <?php if (can_see_cost() && !empty($product['cost'])): ?>
                <div class="text-xs text-gray-400 mt-2 italic">Custo (interno): <?= e(money_br((float) $product['cost'])) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- CTAs secundários (logo abaixo dos detalhes — backup pros usuários que rolaram a página) -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-10">
        <button type="button"
                onclick="addToCart(<?= (int) $product['id'] ?>, 1, this)"
                class="inline-flex items-center justify-center gap-2 bg-brand-blue text-white px-8 py-3.5 rounded-full font-medium hover:bg-brand-blue-dark transition min-w-[220px] disabled:opacity-60">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            Adicionar ao Carrinho
        </button>
        <a href="<?= e(url('carrinho')) ?>"
           class="inline-flex items-center justify-center gap-2 bg-brand-ink text-white px-8 py-3.5 rounded-full font-medium hover:bg-black transition min-w-[220px]">
            Ver carrinho
        </a>
    </div>

    <?php if (!empty($product['description'])): ?>
        <div class="text-sm text-brand-muted leading-relaxed space-y-2 max-w-3xl mx-auto">
            <?php
            $lines = explode("\n", trim($product['description']));
            foreach ($lines as $line):
                $line = trim($line);
                if ($line === '') continue;
            ?>
                <p>
                <?php if (preg_match('#^([\w \-/]+):\s*(.+)$#u', $line, $m)): ?>
                    <strong class="text-brand-ink"><?= e($m[1]) ?>:</strong> <?= e($m[2]) ?>
                <?php else: ?>
                    <?= e($line) ?>
                <?php endif; ?>
                </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if (!empty($related)): ?>
<section class="bg-gray-50 py-14">
    <div class="max-w-content mx-auto px-6 lg:px-10">
        <div class="mb-8">
            <h2 class="display text-3xl text-brand-ink">Você também pode gostar</h2>
            <?php if ($primaryCategory): ?>
                <p class="text-sm text-brand-muted mt-1">Outros produtos da categoria <?= e($primaryCategory['name']) ?></p>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
            <?php foreach ($related as $r): ?>
                <?php $this->partial('product_card', ['product' => $r]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php $this->endSection(); ?>
