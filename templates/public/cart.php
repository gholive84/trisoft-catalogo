<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$items     = $summary['items'];
$subtotal  = $summary['subtotal'];
$count     = $summary['count'];
$showPrice = can_see_prices();
?>
<section class="max-w-6xl mx-auto px-6 lg:px-10 py-12 lg:py-16">
    <div class="text-center mb-12">
        <h1 class="display text-4xl md:text-5xl text-brand-ink">Solicitar Orçamento</h1>
        <p class="text-brand-muted mt-3 text-sm">
            <?= e((string) $count) ?> item<?= $count === 1 ? '' : 's' ?> · Resposta em até 1 dia útil
        </p>
    </div>

    <?php if ($items === []): ?>
        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-3xl p-16 text-center">
            <svg class="w-14 h-14 text-gray-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11V7a3 3 0 016 0v4m-9 8h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            <p class="text-brand-muted mt-4">Seu orçamento está vazio</p>
            <a href="<?= e(url('/')) ?>" class="inline-flex items-center mt-6 bg-brand-ink text-white px-6 py-3 rounded-full text-sm font-medium hover:bg-black transition">
                Explorar catálogo
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-10">

            <div class="space-y-4">
                <?php foreach ($items as $i):
                    $img = $i['main_image_path'] ? upload_url('products/' . $i['main_image_path']) : null;
                ?>
                    <div class="bg-white border border-brand-line rounded-2xl p-5 flex gap-4">
                        <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="w-24 h-24 shrink-0 bg-gray-100 rounded-xl overflow-hidden">
                            <?php if ($img): ?>
                                <img src="<?= e($img) ?>" alt="" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="font-medium text-brand-ink hover:text-brand-blue uppercase tracking-wide text-sm line-clamp-2 transition">
                                <?= e($i['name']) ?>
                            </a>
                            <div class="text-[10px] uppercase tracking-widest text-brand-muted mt-1">SKU <?= e($i['sku']) ?></div>

                            <div class="mt-3 flex items-center gap-4">
                                <div class="inline-flex items-center bg-gray-100 rounded-full overflow-hidden">
                                    <button type="button" @click="updateCartItem(<?= (int) $i['item_id'] ?>, <?= max(0, (int) $i['quantity'] - 1) ?>)"
                                            class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">−</button>
                                    <input type="number" min="0" value="<?= e((string) $i['quantity']) ?>"
                                           data-item-id="<?= (int) $i['item_id'] ?>"
                                           onchange="updateCartItem(parseInt(this.dataset.itemId, 10), Math.max(0, parseInt(this.value, 10) || 0)); setTimeout(() => window.location.reload(), 400)"
                                           onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.blur(); }"
                                           class="w-12 text-center text-sm font-medium bg-transparent border-0 focus:ring-0 focus:outline-none [appearance:textfield] [-moz-appearance:textfield]">
                                    <button type="button" @click="updateCartItem(<?= (int) $i['item_id'] ?>, <?= ((int) $i['quantity'] + 1) ?>); setTimeout(() => window.location.reload(), 400)"
                                            class="w-7 h-7 flex items-center justify-center text-brand-muted hover:text-brand-ink">+</button>
                                </div>

                                <button type="button" @click="removeCartItem(<?= (int) $i['item_id'] ?>); setTimeout(() => window.location.reload(), 400)"
                                        class="text-xs text-brand-muted hover:text-rose-600 transition">Remover</button>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <?php if ($showPrice): ?>
                                <div class="font-medium text-brand-ink"><?= e(money_br((float) $i['price'] * (int) $i['quantity'])) ?></div>
                                <div class="text-xs text-brand-muted mt-0.5"><?= e(money_br((float) $i['price'])) ?> × <?= e((string) $i['quantity']) ?></div>
                            <?php else: ?>
                                <div class="text-xs text-brand-muted uppercase tracking-widest">A consultar</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="bg-white border border-brand-line rounded-2xl p-6 h-fit space-y-4 lg:sticky lg:top-8">
                <h2 class="font-medium text-brand-ink uppercase tracking-widest text-sm">Resumo</h2>

                <?php if ($showPrice): ?>
                    <div class="flex items-baseline justify-between">
                        <span class="text-sm text-brand-muted">Subtotal</span>
                        <span class="font-medium text-lg text-brand-ink"><?= e(money_br($subtotal)) ?></span>
                    </div>
                    <p class="text-xs text-brand-muted">Frete e descontos calculados após resposta do vendedor.</p>
                <?php else: ?>
                    <p class="text-xs text-brand-muted leading-relaxed">
                        Orçamento sob consulta. Nossa equipe responde com preços personalizados em até 1 dia útil.
                    </p>
                <?php endif; ?>

                <form method="post" action="<?= e(url('carrinho/solicitar-orcamento')) ?>" class="space-y-3 pt-2">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Observações</label>
                        <textarea name="notes" rows="3" placeholder="Detalhes do projeto, prazo desejado, dúvidas..."
                                  class="w-full text-sm bg-gray-50 border border-transparent rounded-xl px-3 py-2.5 focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition placeholder:text-gray-400"></textarea>
                    </div>
                    <button class="w-full bg-brand-ink text-white py-3.5 rounded-full font-medium hover:bg-black transition">
                        Enviar Pedido de Orçamento
                    </button>
                </form>

                <form method="post" action="<?= e(url('carrinho/limpar')) ?>" class="text-center">
                    <?= csrf_field() ?>
                    <button class="text-xs text-brand-muted hover:text-rose-600 transition">Esvaziar orçamento</button>
                </form>
            </aside>
        </div>
    <?php endif; ?>
</section>
<?php $this->endSection(); ?>
