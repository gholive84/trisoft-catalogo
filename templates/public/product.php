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

<!-- HERO Full-bleed -->
<section class="relative">
    <div class="aspect-[16/7] md:aspect-[21/8] bg-gray-100 overflow-hidden relative">
        <?php if ($heroUrl): ?>
            <img src="<?= e($heroUrl) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent"></div>
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
            </div>
        <?php endif; ?>

        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12 lg:p-16 text-white">
            <h1 class="display text-3xl md:text-5xl lg:text-6xl uppercase tracking-tight max-w-4xl"><?= e($product['name']) ?></h1>
            <?php if (!empty($product['subtitle'])): ?>
                <p class="text-base md:text-lg mt-2 uppercase tracking-widest opacity-90"><?= e($product['subtitle']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CONTEÚDO -->
<section class="max-w-4xl mx-auto px-6 lg:px-10 py-12">

    <!-- Breadcrumb -->
    <div class="text-sm text-brand-muted flex items-center gap-2 mb-8">
        <a href="<?= e(url('/')) ?>" class="hover:text-brand-ink flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar
        </a>
        <?php if ($primaryCategory): ?>
            <span class="text-gray-300">/</span>
            <a href="<?= e(url('categoria/' . $primaryCategory['slug'])) ?>" class="hover:text-brand-ink"><?= e($primaryCategory['name']) ?></a>
        <?php endif; ?>
    </div>

    <!-- Descrição curta -->
    <?php if (!empty($product['short_description'])): ?>
        <p class="text-lg text-brand-ink leading-relaxed mb-12"><?= e($product['short_description']) ?></p>
    <?php endif; ?>

    <!-- Galeria de thumbnails (se houver mais imagens) -->
    <?php if (count($images) > 1): ?>
        <div x-data="{ main: '<?= e($heroUrl) ?>' }" class="mb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php foreach ($images as $img):
                    $u = upload_url('products/' . $img['file_path']);
                ?>
                    <button type="button" @click="main = '<?= e($u) ?>'"
                            class="aspect-square bg-gray-100 rounded-xl overflow-hidden hover:opacity-90 transition">
                        <img src="<?= e($u) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sugestões de Modulação (placeholder com ícones técnicos quando há specs) -->
    <?php if (!empty($specs)): ?>
        <div class="text-center mb-8">
            <div class="text-xs uppercase tracking-widest text-brand-muted font-medium">Sugestões de Modulação</div>
            <div class="flex items-center justify-center gap-4 mt-6 opacity-60">
                <?php for ($i = 0; $i < min(5, count($specs)); $i++): ?>
                    <svg class="w-16 h-12 text-brand-ink" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 64 48">
                        <g transform="translate(<?= 4 + $i ?>, <?= 8 + $i*2 ?>)">
                            <?php for ($j = 0; $j < 6; $j++): ?>
                                <line x1="<?= $j*8 ?>" y1="<?= 8 - $j*0.5 ?>" x2="<?= 50 - $j ?>" y2="<?= 12 - $j*0.3 ?>"/>
                            <?php endfor; ?>
                        </g>
                    </svg>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Tabela de specs -->
        <div class="overflow-x-auto mb-12">
            <table class="w-full text-sm">
                <thead class="border-b border-brand-line">
                    <tr class="text-xs uppercase tracking-widest text-brand-muted">
                        <?php
                        $columns = [
                            'code'           => 'Code',
                            'thickness'      => 'Thickness (mm)',
                            'a'              => '"A" (mm)',
                            'b'              => '"B" (mm)',
                            'pieces_per_box' => 'Pieces/box',
                            'coverage_area'  => 'Coverage area',
                            'pet_bottles'    => 'PET Bottles',
                        ];
                        // Detecta colunas presentes
                        $firstSpec = $specs[0] ?? [];
                        $presentColumns = array_intersect_key($columns, $firstSpec);
                        foreach ($presentColumns as $key => $label): ?>
                            <th class="px-4 py-3 text-left font-medium"><?= e($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-line">
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

    <!-- CTAs -->
    <form method="post" action="<?= e(url('carrinho/adicionar')) ?>" class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-8">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= e((string) $product['id']) ?>">
        <input type="hidden" name="quantity" value="1">

        <button type="submit"
            class="inline-flex items-center justify-center gap-2 bg-brand-blue text-white px-8 py-3.5 rounded-full font-medium hover:bg-brand-blue-dark transition min-w-[220px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            Adicionar ao Carrinho
        </button>

        <a href="<?= e(url('carrinho')) ?>"
           class="inline-flex items-center justify-center gap-2 bg-brand-ink text-white px-8 py-3.5 rounded-full font-medium hover:bg-black transition min-w-[220px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Solicitar Orçamento
        </a>
    </form>

    <!-- Notas técnicas -->
    <?php if (!empty($product['description'])): ?>
        <div class="text-sm text-brand-muted leading-relaxed space-y-2 max-w-3xl mx-auto">
            <?php
            $lines = explode("\n", trim($product['description']));
            foreach ($lines as $line):
                $line = trim($line);
                if ($line === '') continue;
            ?>
                <p>
                <?php if (preg_match('/^([\w \-/]+):\s*(.+)$/u', $line, $m)): ?>
                    <strong class="text-brand-ink"><?= e($m[1]) ?>:</strong> <?= e($m[2]) ?>
                <?php else: ?>
                    <?= e($line) ?>
                <?php endif; ?>
                </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- RELACIONADOS -->
<?php if (!empty($related)): ?>
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
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
