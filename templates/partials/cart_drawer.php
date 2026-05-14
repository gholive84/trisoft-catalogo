<?php
/**
 * Drawer lateral do carrinho. Renderiza o estado atual do carrinho (SSR).
 * Controlado por Alpine via x-data="{ cartOpen: false }" no <body>.
 *
 * Para abrir programaticamente:
 *   <button @click="$dispatch('open-cart')">...</button>
 * Para fechar:
 *   <button @click="$dispatch('close-cart')">...</button>
 */

// Carrega o resumo do carrinho (apenas para usuários logados;
// guests não têm carrinho persistente — ver feedback_visibilidade_precos).
$cartSummary = ['items' => [], 'count' => 0, 'subtotal' => 0.0];
if (\App\Core\Auth::check()) {
    try {
        $cartSummary = (new \App\Services\CartService())->summary();
    } catch (\Throwable) {}
}
$cartItems    = $cartSummary['items'];
$cartCount    = (int) $cartSummary['count'];
$cartSubtotal = (float) ($cartSummary['subtotal'] ?? 0);
?>
<div x-cloak
     x-show="cartOpen"
     @keydown.escape.window="cartOpen = false"
     @open-cart.window="cartOpen = true"
     @close-cart.window="cartOpen = false"
     class="fixed inset-0 z-50 overflow-hidden">

    <!-- Overlay -->
    <div x-show="cartOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cartOpen = false"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Painel lateral -->
    <div x-show="cartOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">

        <!-- Header do drawer -->
        <div class="px-6 py-5 border-b border-brand-line flex items-center justify-between">
            <div>
                <h2 class="font-medium text-lg uppercase tracking-widest text-brand-ink">Orçamento</h2>
                <p class="text-xs text-brand-muted mt-0.5">
                    <?= $cartCount > 0
                        ? e((string) $cartCount) . ' item' . ($cartCount > 1 ? 's' : '') . ' selecionado' . ($cartCount > 1 ? 's' : '')
                        : 'Nenhum item ainda' ?>
                </p>
            </div>
            <button @click="cartOpen = false" class="w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center text-brand-muted hover:text-brand-ink transition" aria-label="Fechar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Conteúdo: lista de itens -->
        <div class="flex-1 overflow-y-auto">
            <?php if (!\App\Core\Auth::check()): ?>
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
                                    <form method="post" action="<?= e(url('carrinho/atualizar')) ?>" class="inline-flex items-center bg-gray-100 rounded-full">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                        <input type="hidden" name="_drawer" value="1">
                                        <button type="submit" name="quantity" value="<?= e((string) max(0, (int) $i['quantity'] - 1)) ?>"
                                                class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">−</button>
                                        <span class="w-8 text-center text-sm font-medium"><?= e((string) $i['quantity']) ?></span>
                                        <button type="submit" name="quantity" value="<?= e((string) ((int) $i['quantity'] + 1)) ?>"
                                                class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">+</button>
                                    </form>

                                    <form method="post" action="<?= e(url('carrinho/remover')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                        <input type="hidden" name="_drawer" value="1">
                                        <button type="submit" class="text-xs text-brand-muted hover:text-rose-600 transition">Remover</button>
                                    </form>
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

        <!-- Footer do drawer -->
        <?php if (\App\Core\Auth::check() && $cartItems !== []): ?>
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
                    Solicitar Orçamento
                </a>
                <a href="<?= e(url('carrinho')) ?>"
                   class="block text-center text-sm text-brand-muted hover:text-brand-ink py-1">
                    Ver carrinho completo →
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
