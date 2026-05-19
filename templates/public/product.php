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
        <?php if (has_role('admin', 'editor')): ?>
            <a href="<?= e(url('admin/produtos/' . (int) $product['id'] . '/editar')) ?>"
               class="absolute top-4 right-4 z-20 inline-flex items-center gap-1.5 bg-white/95 backdrop-blur text-brand-ink px-4 py-2 rounded-full text-xs font-medium uppercase tracking-wider shadow-lg hover:bg-white transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar produto
            </a>
        <?php endif; ?>
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
        <?php
        // Imagem técnica (Dimensões ou Módulos) acima da tabela.
        // Prioridade: dimensions_image_path (novo padrão); fallback para
        // modulation_image_path (legacy de produtos importados antigamente).
        $techImage = $product['dimensions_image_path']
            ?: ($product['modulation_image_path'] ?? null);
        ?>
        <?php if (!empty($techImage)): ?>
            <div class="text-center mb-8">
                <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-4">Dimensões / Módulos</div>
                <img src="<?= e(upload_url('products/' . $techImage)) ?>"
                     alt="Diagrama técnico"
                     class="max-w-full mx-auto"
                     style="max-height: 320px;"
                     loading="lazy">
            </div>
        <?php endif; ?>

        <?php
        $specLayoutVal = $product['spec_layout'] ?? 'simple';
        $isMultiPiece  = $specLayoutVal === 'multi_piece';
        $isWallCeiling = $specLayoutVal === 'wall_ceiling';
        $isFlexible    = $specLayoutVal === 'flexible';

        // Mapeamento de cor -> classes Tailwind (paleta fixa).
        $flexColorHeader = [
            'blue'    => 'bg-blue-100 text-blue-800 border-blue-200',
            'amber'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rose'    => 'bg-rose-100 text-rose-800 border-rose-200',
            'purple'  => 'bg-purple-100 text-purple-800 border-purple-200',
            'slate'   => 'bg-slate-100 text-slate-800 border-slate-200',
        ];
        $flexColorCell = [
            'blue'    => 'bg-blue-50/40 border-blue-200',
            'amber'   => 'bg-amber-50/40 border-amber-200',
            'emerald' => 'bg-emerald-50/40 border-emerald-200',
            'rose'    => 'bg-rose-50/40 border-rose-200',
            'purple'  => 'bg-purple-50/40 border-purple-200',
            'slate'   => 'bg-slate-50/40 border-slate-200',
        ];
        ?>

        <?php if ($isFlexible):
            // Carrega schema dinamico
            $flexSchema = $product['spec_schema'] ?? null;
            if (is_string($flexSchema)) $flexSchema = json_decode($flexSchema, true);
            $flexCols = (is_array($flexSchema) && !empty($flexSchema['columns'])) ? $flexSchema['columns'] : [];

            // Skip coluna onde TODAS as rows estao vazias (mesma logica do simple)
            $visibleCols = [];
            foreach ($flexCols as $col) {
                $k = $col['key'];
                if ($k === 'code') { $visibleCols[] = $col; continue; }
                foreach ($specs as $row) {
                    $v = $row[$k] ?? null;
                    if ($v !== null && $v !== '' && $v !== 0 && $v !== '0') {
                        $visibleCols[] = $col;
                        break;
                    }
                }
            }

            // Construir linhas de header: se ha grupos consecutivos, agrupa em header superior.
            $groupBlocks = []; // [[group, color, colspan, startIdx]]
            $curGroup = null; $curColor = null; $curSpan = 0; $curStart = 0;
            foreach ($visibleCols as $idx => $col) {
                $g = $col['group'] ?? null;
                $c = $col['color'] ?? null;
                if ($g === $curGroup && $c === $curColor && $g !== null) {
                    $curSpan++;
                } else {
                    if ($curSpan > 0) $groupBlocks[] = ['group'=>$curGroup,'color'=>$curColor,'colspan'=>$curSpan,'start'=>$curStart];
                    $curGroup = $g; $curColor = $c; $curSpan = 1; $curStart = $idx;
                }
            }
            if ($curSpan > 0) $groupBlocks[] = ['group'=>$curGroup,'color'=>$curColor,'colspan'=>$curSpan,'start'=>$curStart];
            $hasGroups = false;
            foreach ($groupBlocks as $b) { if (!empty($b['group'])) { $hasGroups = true; break; } }
        ?>
            <div class="overflow-x-auto mb-10 border border-brand-line rounded-2xl">
                <table class="w-full text-xs md:text-sm">
                    <thead class="border-b border-brand-line">
                        <?php if ($hasGroups): ?>
                            <tr class="text-xs uppercase tracking-widest bg-gray-50">
                                <?php foreach ($groupBlocks as $b):
                                    $bgClass = $b['color'] && isset($flexColorHeader[$b['color']]) ? $flexColorHeader[$b['color']] : 'text-brand-muted text-[10px]';
                                ?>
                                    <th colspan="<?= (int) $b['colspan'] ?>" class="px-2 py-3 text-center font-bold border-l border-r <?= e($bgClass) ?>">
                                        <?= !empty($b['group']) ? '▸ ' . e($b['group']) : '' ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                        <tr class="text-[10px] md:text-xs uppercase tracking-widest text-brand-muted bg-gray-50 border-t border-brand-line">
                            <?php foreach ($visibleCols as $col):
                                $hdr = $col['color'] && isset($flexColorHeader[$col['color']]) ? $flexColorHeader[$col['color']] : '';
                            ?>
                                <th class="px-3 py-2 text-left font-medium <?= e($hdr) ?>">
                                    <?= e($col['label']) ?><?php if (!empty($col['unit'])): ?><span class="ml-1 text-[10px] opacity-60 font-normal lowercase">(<?= e($col['unit']) ?>)</span><?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-line bg-white">
                        <?php foreach ($specs as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <?php foreach ($visibleCols as $col):
                                    $cellClass = $col['color'] && isset($flexColorCell[$col['color']]) ? $flexColorCell[$col['color']] : '';
                                    $isCode = $col['key'] === 'code';
                                    $val = $row[$col['key']] ?? '';
                                    if ($val === '' || $val === null) $val = '—';
                                ?>
                                    <td class="px-3 py-2 text-brand-ink <?= e($cellClass) ?><?= $isCode ? ' font-mono whitespace-nowrap' : '' ?>"><?= e((string) $val) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($isWallCeiling): ?>
            <!-- Legenda Parede / Teto -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-3 text-xs text-brand-muted">
                <span class="inline-flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded bg-blue-200 border border-blue-300"></span>
                    <span class="font-semibold text-blue-700 uppercase tracking-widest text-[11px]">Parede</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded bg-amber-200 border border-amber-300"></span>
                    <span class="font-semibold text-amber-700 uppercase tracking-widest text-[11px]">Teto</span>
                </span>
                <span class="text-[11px] italic">— produto composto por painéis de parede e teto, vendidos em conjunto.</span>
            </div>
            <div class="overflow-x-auto mb-10 border border-brand-line rounded-2xl">
                <table class="w-full text-xs md:text-sm min-w-[800px]">
                    <thead class="border-b border-brand-line">
                        <tr class="text-xs uppercase tracking-widest bg-gray-50">
                            <th class="px-3 py-3"></th>
                            <th class="px-3 py-3"></th>
                            <th colspan="3" class="px-2 py-3 text-center bg-blue-100 text-blue-800 border-l-2 border-r-2 border-blue-300 font-bold text-sm">▸ PAREDE</th>
                            <th colspan="3" class="px-2 py-3 text-center bg-amber-100 text-amber-800 border-r-2 border-amber-300 font-bold text-sm">▸ TETO</th>
                            <th colspan="4" class="px-2 py-3 text-center text-brand-muted text-[10px]">Compartilhado</th>
                        </tr>
                        <tr class="text-[10px] uppercase tracking-widest text-brand-muted bg-gray-50 border-t border-brand-line">
                            <th class="px-3 py-2 text-left font-medium">Code</th>
                            <th class="px-2 py-2 text-left font-medium">Thickness<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60 border-l-2 border-blue-200">Altura "A"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60">Largura "B"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60 border-r-2 border-blue-200">Compr.<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60">Altura "C"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60">Largura "D"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60 border-r-2 border-amber-200">Compr.<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium">Pç/cx</th>
                            <th class="px-2 py-2 text-left font-medium">Cob. Parede</th>
                            <th class="px-2 py-2 text-left font-medium">Cob. Teto</th>
                            <th class="px-2 py-2 text-left font-medium">PET</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-line bg-white">
                        <?php foreach ($specs as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-mono text-brand-ink whitespace-nowrap"><?= e((string) ($row['code'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['thickness'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40 border-l-2 border-blue-200"><?= e((string) ($row['wall_height'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40"><?= e((string) ($row['wall_width'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40 border-r-2 border-blue-200"><?= e((string) ($row['wall_length'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40"><?= e((string) ($row['ceiling_height'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40"><?= e((string) ($row['ceiling_width'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40 border-r-2 border-amber-200"><?= e((string) ($row['ceiling_length'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['pieces_per_box'] ?? '—')) ?></td>
                                <td class="px-2 py-2 whitespace-nowrap"><?= e((string) ($row['wall_coverage'] ?? '—')) ?></td>
                                <td class="px-2 py-2 whitespace-nowrap"><?= e((string) ($row['ceiling_coverage'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['pet_bottles'] ?? '—')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($isMultiPiece): ?>
            <!-- Legenda das peças -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-3 text-xs text-brand-muted">
                <span class="inline-flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded bg-blue-200 border border-blue-300"></span>
                    <span class="font-semibold text-blue-700 uppercase tracking-widest text-[11px]">Peça 1</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded bg-amber-200 border border-amber-300"></span>
                    <span class="font-semibold text-amber-700 uppercase tracking-widest text-[11px]">Peça 2</span>
                </span>
                <span class="text-[11px] italic">— este produto é composto por duas peças vendidas em conjunto. Veja na imagem acima.</span>
            </div>
            <!-- TABELA MULTI_PIECE — agrupada com header PIECE 1 | PIECE 2 | shared -->
            <div class="overflow-x-auto mb-10 border border-brand-line rounded-2xl">
                <table class="w-full text-xs md:text-sm min-w-[900px]">
                    <thead class="border-b border-brand-line">
                        <tr class="text-xs uppercase tracking-widest bg-gray-50">
                            <th class="px-3 py-3"></th>
                            <th class="px-3 py-3"></th>
                            <th colspan="5" class="px-2 py-3 text-center bg-blue-100 text-blue-800 border-l-2 border-r-2 border-blue-300 font-bold text-sm">▸ PEÇA 1</th>
                            <th colspan="4" class="px-2 py-3 text-center bg-amber-100 text-amber-800 border-r-2 border-amber-300 font-bold text-sm">▸ PEÇA 2</th>
                            <th colspan="3" class="px-2 py-3 text-center text-brand-muted text-[10px]">Compartilhado</th>
                        </tr>
                        <tr class="text-[10px] uppercase tracking-widest text-brand-muted bg-gray-50 border-t border-brand-line">
                            <th class="px-3 py-2 text-left font-medium">Code</th>
                            <th class="px-2 py-2 text-left font-medium">Thickness<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60 border-l-2 border-blue-200">"A"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60">"B"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60">"C"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60">Peças</th>
                            <th class="px-2 py-2 text-left font-medium bg-blue-50/60 border-r-2 border-blue-200">PET</th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60">"A"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60">"B"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60">"C"<span class="ml-1 text-[9px] opacity-60 font-normal lowercase">(mm)</span></th>
                            <th class="px-2 py-2 text-left font-medium bg-amber-50/60 border-r-2 border-amber-200">Peças</th>
                            <th class="px-2 py-2 text-left font-medium">Pç/cx</th>
                            <th class="px-2 py-2 text-left font-medium">Cobertura</th>
                            <th class="px-2 py-2 text-left font-medium">PET</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-line bg-white">
                        <?php foreach ($specs as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-mono text-brand-ink whitespace-nowrap"><?= e((string) ($row['code'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['thickness'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40 border-l-2 border-blue-200"><?= e((string) ($row['p1_a'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40"><?= e((string) ($row['p1_b'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40"><?= e((string) ($row['p1_c'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40"><?= e((string) ($row['p1_pieces'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-blue-50/40 border-r-2 border-blue-200"><?= e((string) ($row['p1_pet'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40"><?= e((string) ($row['p2_a'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40"><?= e((string) ($row['p2_b'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40"><?= e((string) ($row['p2_c'] ?? '—')) ?></td>
                                <td class="px-2 py-2 bg-amber-50/40 border-r-2 border-amber-200"><?= e((string) ($row['p2_pieces'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['pieces_per_box'] ?? '—')) ?></td>
                                <td class="px-2 py-2 whitespace-nowrap"><?= e((string) ($row['coverage_area'] ?? '—')) ?></td>
                                <td class="px-2 py-2"><?= e((string) ($row['pet_bottles'] ?? '—')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto mb-10 border border-brand-line rounded-2xl">
                <table class="w-full text-sm">
                    <thead class="border-b border-brand-line">
                        <tr class="text-xs uppercase tracking-widest text-brand-muted bg-gray-50">
                            <?php
                            // Labels custom (spec_column_labels) sobrescrevem "A"-"H".
                            // Colunas E-H sao "extras" — so aparecem quando label esta definido OU valor presente.
                            // Colunas A-D usam unit "mm" por padrao (geometria); E-H sem unit (texto livre).
                            $customLabels = $product['spec_column_labels'] ?? null;
                            if (is_string($customLabels)) $customLabels = json_decode($customLabels, true);
                            if (!is_array($customLabels)) $customLabels = [];
                            $labelA = !empty($customLabels['a']) ? $customLabels['a'] : '"A"';
                            $labelB = !empty($customLabels['b']) ? $customLabels['b'] : '"B"';
                            $labelC = !empty($customLabels['c']) ? $customLabels['c'] : '"C"';
                            $labelD = !empty($customLabels['d']) ? $customLabels['d'] : '"D"';
                            $labelE = !empty($customLabels['e']) ? $customLabels['e'] : '"E"';
                            $labelF = !empty($customLabels['f']) ? $customLabels['f'] : '"F"';
                            $labelG = !empty($customLabels['g']) ? $customLabels['g'] : '"G"';
                            $labelH = !empty($customLabels['h']) ? $customLabels['h'] : '"H"';
                            $columns = [
                                'code'           => ['Code',        null],
                                'thickness'      => ['Thickness',   'mm'],
                                'a'              => [$labelA,       'mm'],
                                'b'              => [$labelB,       'mm'],
                                'c'              => [$labelC,       'mm'],
                                'd'              => [$labelD,       'mm'],
                                'e'              => [$labelE,       null],
                                'f'              => [$labelF,       null],
                                'g'              => [$labelG,       null],
                                'h'              => [$labelH,       null],
                                'pieces_per_box' => ['Peças/cx',    null],
                                'coverage_area'  => ['Cobertura',   null],
                                'pet_bottles'    => ['PET Bottles', null],
                            ];
                            $presentColumns = [];
                            foreach ($columns as $key => $colDef) {
                                if ($key === 'code') { $presentColumns[$key] = $colDef; continue; }
                                foreach ($specs as $row) {
                                    $v = $row[$key] ?? null;
                                    if ($v !== null && $v !== '' && $v !== 0 && $v !== '0') {
                                        $presentColumns[$key] = $colDef;
                                        break;
                                    }
                                }
                            }
                            foreach ($presentColumns as $key => [$label, $unit]): ?>
                                <th class="px-4 py-3 text-left font-medium">
                                    <?= e($label) ?><?php if ($unit): ?><span class="ml-1 text-[10px] opacity-60 font-normal lowercase">(<?= e($unit) ?>)</span><?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-line bg-white">
                        <?php foreach ($specs as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <?php foreach ($presentColumns as $key => $_): ?>
                                    <td class="px-4 py-3 text-brand-ink<?= $key === 'code' ? ' font-mono whitespace-nowrap' : '' ?>"><?= e((string) ($row[$key] ?? '—')) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
