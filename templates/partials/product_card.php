<?php
/**
 * Card de produto premium minimalista.
 * Apenas título em CAPS + ícone "+" para adicionar ao orçamento rapidamente.
 */
$img = $product['main_image_path'] ?? null;
$imgUrl = $img ? upload_url('products/' . $img) : null;
$href = url('produto/' . $product['slug']);
?>
<div class="group relative">
    <a href="<?= e($href) ?>" class="block">
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
        <div class="mt-4 flex items-start justify-between gap-3">
            <h3 class="text-sm md:text-[15px] uppercase tracking-wider font-medium text-brand-ink group-hover:text-brand-blue transition flex-1">
                <?= e($product['name']) ?>
            </h3>
        </div>
    </a>

    <!-- Quick add: botão flutuante no canto superior direito da imagem -->
    <button type="button"
            onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?= (int) $product['id'] ?>, 1, this)"
            title="Adicionar ao orçamento"
            class="absolute top-3 right-3 w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center text-brand-ink hover:bg-brand-blue hover:text-white transition opacity-0 group-hover:opacity-100 focus:opacity-100 disabled:opacity-60">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
    </button>
</div>
