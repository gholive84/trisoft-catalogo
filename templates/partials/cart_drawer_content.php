<?php
/**
 * Conteúdo do drawer (lista de itens + footer). Renderizado tanto via SSR
 * (dentro de cart_drawer.php) quanto via AJAX (CartApiController -> JSON.html).
 *
 * Espera variáveis: $cartItems (array), $cartCount (int), $cartSubtotal (float),
 *                   $isGuest (bool — true quando não está logado).
 */
$cartItems    = $cartItems    ?? [];
$cartCount    = $cartCount    ?? 0;
$cartSubtotal = $cartSubtotal ?? 0.0;
$isGuest      = $isGuest      ?? !\App\Core\Auth::check();
?>

<div class="px-6 py-5 border-b border-brand-line flex items-center justify-between">
    <div>
        <h2 class="font-medium text-lg uppercase tracking-widest text-brand-ink">Orçamento</h2>
        <p class="text-xs text-brand-muted mt-0.5">
            <?= $cartCount > 0
                ? e((string) $cartCount) . ' item' . ($cartCount > 1 ? 's' : '')
                : 'Nenhum item ainda' ?>
        </p>
    </div>
    <button @click="cartOpen = false" class="w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center text-brand-muted hover:text-brand-ink transition" aria-label="Fechar">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<div class="flex-1 overflow-y-auto">
    <?php if ($isGuest): ?>
        <div class="px-6 py-12 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11V7a3 3 0 016 0v4m-9 8h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-sm text-brand-ink font-medium">Entre para montar seu orçamento</p>
            <p class="text-xs text-brand-muted mt-1 mb-5">Acesse sua conta para adicionar produtos</p>
            <a href="<?= e(url('login')) ?>" class="inline-block bg-brand-ink text-white px-6 py-3 rounded-full text-sm font-medium hover:bg-black transition">
                Entrar / Criar conta
            </a>
        </div>
    <?php elseif ($cartItems === []): ?>
        <div class="px-6 py-12 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11V7a3 3 0 016 0v4m-9 8h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-sm text-brand-ink font-medium">Seu orçamento está vazio</p>
            <p class="text-xs text-brand-muted mt-1 mb-5">Adicione produtos para começar</p>
            <button @click="cartOpen = false" class="text-sm text-brand-blue hover:underline">
                Explorar catálogo
            </button>
        </div>
    <?php else: ?>
        <div class="divide-y divide-brand-line">
            <?php foreach ($cartItems as $i):
                $img = $i['main_image_path'] ? upload_url('products/' . $i['main_image_path']) : null;
            ?>
                <div class="px-6 py-4 flex gap-3">
                    <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="w-20 h-20 shrink-0 bg-gray-100 rounded-xl overflow-hidden">
                        <?php if ($img): ?>
                            <img src="<?= e($img) ?>" alt="" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="<?= e(url('produto/' . $i['slug'])) ?>"
                           class="text-sm font-medium text-brand-ink hover:text-brand-blue line-clamp-2 transition uppercase tracking-wide">
                            <?= e($i['name']) ?>
                        </a>
                        <div class="text-[10px] uppercase tracking-widest text-brand-muted mt-1">SKU <?= e($i['sku']) ?></div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="inline-flex items-center bg-gray-100 rounded-full overflow-hidden">
                                <button type="button" @click="updateCartItem(<?= (int) $i['item_id'] ?>, <?= max(0, (int) $i['quantity'] - 1) ?>)"
                                        class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">−</button>
                                <input type="number" min="0" value="<?= e((string) $i['quantity']) ?>"
                                       data-item-id="<?= (int) $i['item_id'] ?>"
                                       onchange="updateCartItem(parseInt(this.dataset.itemId, 10), Math.max(0, parseInt(this.value, 10) || 0))"
                                       onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.blur(); }"
                                       class="w-10 text-center text-sm font-medium bg-transparent border-0 focus:ring-0 focus:outline-none px-0 py-0
                                              [appearance:textfield] [-moz-appearance:textfield]">
                                <button type="button" @click="updateCartItem(<?= (int) $i['item_id'] ?>, <?= ((int) $i['quantity'] + 1) ?>)"
                                        class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">+</button>
                            </div>
                            <button type="button" @click="removeCartItem(<?= (int) $i['item_id'] ?>)"
                                    class="text-xs text-brand-muted hover:text-rose-600 transition">Remover</button>
                        </div>

                        <?php if (can_see_prices()): ?>
                            <div class="text-sm font-medium text-brand-ink mt-1">
                                <?= e(money_br((float) $i['price'] * (int) $i['quantity'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (!$isGuest && $cartItems !== []): ?>
    <div class="border-t border-brand-line p-6 space-y-3 bg-white">
        <?php if (can_see_prices()): ?>
            <div class="flex items-baseline justify-between">
                <span class="text-sm text-brand-muted uppercase tracking-widest">Subtotal</span>
                <span class="font-medium text-lg text-brand-ink"><?= e(money_br($cartSubtotal)) ?></span>
            </div>
            <p class="text-xs text-brand-muted">Frete e descontos calculados após resposta do vendedor.</p>
        <?php else: ?>
            <p class="text-xs text-brand-muted leading-relaxed">
                Orçamento sob consulta. Nossa equipe responde com preços personalizados em até 1 dia útil.
            </p>
        <?php endif; ?>

        <a href="<?= e(url('carrinho')) ?>"
           class="block text-center bg-brand-ink text-white py-3.5 rounded-full font-medium hover:bg-black transition">
            Finalizar e Solicitar
        </a>
        <button @click="cartOpen = false" class="block w-full text-center text-sm text-brand-muted hover:text-brand-ink py-1">
            Continuar comprando
        </button>
    </div>
<?php endif; ?>
