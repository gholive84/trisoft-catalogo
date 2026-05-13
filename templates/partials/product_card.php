<?php
/**
 * Card de produto. Espera $product e (opcional) $context = 'grid'|'horizontal'.
 * Mostra preço para visitantes; CTA de adicionar pede login se !auth().
 */
$context = $context ?? 'grid';
$img = $product['main_image_path'] ?? null;
$imgUrl = $img ? upload_url('products/' . $img) : null;
$href = url('produto/' . $product['slug']);
?>
<a href="<?= e($href) ?>" class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-gray-400 hover:shadow-sm transition flex flex-col">
    <div class="aspect-square bg-gray-100 overflow-hidden">
        <?php if ($imgUrl): ?>
            <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 loading="lazy">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>
    <div class="p-4 flex-1 flex flex-col">
        <h3 class="font-medium text-gray-900 line-clamp-2 group-hover:text-gray-700"><?= e($product['name']) ?></h3>
        <?php if (!empty($product['short_description'])): ?>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= e($product['short_description']) ?></p>
        <?php endif; ?>
        <div class="mt-auto pt-3 flex items-center justify-between">
            <div>
                <div class="text-lg font-bold text-gray-900"><?= e(money_br((float) $product['price'])) ?></div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wide">SKU <?= e($product['sku']) ?></div>
            </div>
            <span class="text-xs text-gray-600 group-hover:text-gray-900">Ver →</span>
        </div>
    </div>
</a>
