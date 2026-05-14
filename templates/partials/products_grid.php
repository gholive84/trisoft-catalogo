<?php
/**
 * Grid de produtos + paginação. Renderizável tanto SSR quanto via AJAX
 * (CatalogController::listJson).
 *
 * Espera: $pagination = ['items','total','page','perPage','lastPage']
 */
$pagination = $pagination ?? ['items' => [], 'total' => 0, 'page' => 1, 'perPage' => 24, 'lastPage' => 1];
?>

<?php if ($pagination['items'] === []): ?>
    <div class="bg-gray-50 border border-dashed border-brand-line rounded-2xl p-16 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-brand-muted mt-4">Nenhum produto encontrado.</p>
        <p class="text-xs text-gray-400 mt-1">Tente remover filtros ou ajustar a busca.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
        <?php foreach ($pagination['items'] as $product): ?>
            <?php $this->partial('product_card', ['product' => $product]); ?>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['lastPage'] > 1): ?>
        <div class="mt-10 flex items-center justify-center gap-1.5 text-sm" data-pagination>
            <?php
            $window = 2;
            $page = (int) $pagination['page'];
            $last = (int) $pagination['lastPage'];
            $pages = [];
            for ($i = max(1, $page - $window); $i <= min($last, $page + $window); $i++) $pages[] = $i;
            if ($pages !== [] && $pages[0] !== 1) array_unshift($pages, 1, 'gap');
            if ($pages !== [] && end($pages) !== $last) array_push($pages, 'gap', $last);
            $pages = array_values(array_unique($pages, SORT_REGULAR));
            ?>
            <?php if ($page > 1): ?>
                <button data-page="<?= $page - 1 ?>" class="px-3 py-2 rounded-lg border border-brand-line hover:border-brand-blue hover:text-brand-blue transition">‹</button>
            <?php endif; ?>
            <?php foreach ($pages as $p): ?>
                <?php if ($p === 'gap'): ?>
                    <span class="px-2 text-gray-400">…</span>
                <?php elseif ((int) $p === $page): ?>
                    <span class="px-3 py-2 rounded-lg bg-brand-ink text-white font-medium"><?= (int) $p ?></span>
                <?php else: ?>
                    <button data-page="<?= (int) $p ?>" class="px-3 py-2 rounded-lg border border-brand-line hover:border-brand-blue hover:text-brand-blue transition"><?= (int) $p ?></button>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($page < $last): ?>
                <button data-page="<?= $page + 1 ?>" class="px-3 py-2 rounded-lg border border-brand-line hover:border-brand-blue hover:text-brand-blue transition">›</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="text-center text-xs text-brand-muted mt-6">
        Mostrando <?= count($pagination['items']) ?> de <?= e((string) $pagination['total']) ?> produto(s)
    </div>
<?php endif; ?>
