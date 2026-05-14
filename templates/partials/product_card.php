<?php
/**
 * Card de produto. Espera $product e opcionalmente $context = 'grid'.
 * Mostra preço somente para staff (helper can_see_prices()).
 */
$img = $product['main_image_path'] ?? null;
$imgUrl = $img ? upload_url('products/' . $img) : null;
$href = url('produto/' . $product['slug']);
?>
<a href="<?= e($href) ?>"
   class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-brand-blue/30 hover:shadow-brand transition-all duration-300 hover:-translate-y-1 flex flex-col">
    <div class="aspect-square bg-brand-cream relative overflow-hidden">
        <?php if ($imgUrl): ?>
            <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                 loading="lazy">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                </svg>
            </div>
        <?php endif; ?>

        <?php if (!empty($product['is_featured'])): ?>
            <span class="absolute top-3 left-3 inline-flex items-center px-2 py-1 rounded-md bg-brand-green text-white text-[10px] font-bold uppercase tracking-wider shadow-soft">
                Destaque
            </span>
        <?php endif; ?>
    </div>

    <div class="p-4 flex-1 flex flex-col">
        <h3 class="font-medium text-brand-ink line-clamp-2 group-hover:text-brand-blue transition"><?= e($product['name']) ?></h3>
        <?php if (!empty($product['short_description'])): ?>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= e($product['short_description']) ?></p>
        <?php endif; ?>

        <div class="mt-auto pt-4 flex items-end justify-between">
            <div>
                <?php if (can_see_prices()): ?>
                    <div class="font-display text-lg font-bold text-brand-ink"><?= e(money_br((float) $product['price'])) ?></div>
                <?php else: ?>
                    <div class="text-xs text-brand-blue font-semibold uppercase tracking-wide">Sob consulta</div>
                <?php endif; ?>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">SKU <?= e($product['sku']) ?></div>
            </div>
            <span class="inline-flex items-center text-xs text-gray-500 group-hover:text-brand-blue font-medium">
                Ver
                <svg class="w-3.5 h-3.5 ml-0.5 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        </div>
    </div>
</a>
