<?php
/**
 * Card de produto premium (estilo arquitetura).
 * Imagem retangular grande, nome em CAPS, subtítulo cinza.
 */
$img = $product['main_image_path'] ?? null;
$imgUrl = $img ? upload_url('products/' . $img) : null;
$href = url('produto/' . $product['slug']);
$subtitle = $product['short_description'] ?? '';
?>
<a href="<?= e($href) ?>" class="group block">
    <div class="aspect-[5/4] bg-gray-100 rounded-2xl overflow-hidden">
        <?php if ($imgUrl): ?>
            <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>"
                 class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-500"
                 loading="lazy">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4m0 0l4-4m-4 4l4 4m4-12h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>
    <div class="mt-4">
        <h3 class="text-base uppercase tracking-wider font-medium text-brand-ink group-hover:text-brand-blue transition">
            <?= e($product['name']) ?>
        </h3>
        <?php if (!empty($subtitle)): ?>
            <p class="text-sm text-brand-muted mt-1 line-clamp-1"><?= e($subtitle) ?></p>
        <?php endif; ?>
        <?php if (can_see_prices()): ?>
            <p class="text-sm font-medium text-brand-ink mt-2"><?= e(money_br((float) $product['price'])) ?></p>
        <?php endif; ?>
    </div>
</a>
