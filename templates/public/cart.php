<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$items     = $summary['items'];
$subtotal  = $summary['subtotal'];
$count     = $summary['count'];
$showPrice = can_see_prices();
?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl md:text-4xl font-bold text-brand-ink">Carrinho de orçamento</h1>
            <p class="text-sm text-gray-500 mt-1"><?= e((string) $count) ?> item(ns) selecionado(s)</p>
        </div>
        <a href="<?= e(url('/')) ?>" class="text-sm text-brand-blue hover:underline hidden sm:inline">← Continuar comprando</a>
    </div>

    <?php if ($items === []): ?>
        <div class="bg-white border border-dashed border-gray-200 rounded-2xl p-16 text-center">
            <svg class="w-20 h-20 text-gray-200 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-600 mt-4 font-display text-lg">Seu carrinho está vazio</p>
            <p class="text-sm text-gray-500 mt-1">Adicione produtos para solicitar um orçamento.</p>
            <a href="<?= e(url('/')) ?>" class="inline-flex items-center mt-6 px-6 py-3 rounded-xl bg-brand-blue text-white font-semibold hover:bg-brand-blue-dark shadow-brand transition">
                Explorar catálogo
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">

            <div class="bg-white border border-gray-100 rounded-2xl shadow-soft divide-y divide-gray-100">
                <?php foreach ($items as $i):
                    $img = $i['main_image_path']
                        ? upload_url('products/' . $i['main_image_path'])
                        : null;
                ?>
                    <div class="p-5 flex gap-4">
                        <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="w-24 h-24 shrink-0 bg-brand-cream rounded-xl overflow-hidden">
                            <?php if ($img): ?>
                                <img src="<?= e($img) ?>" alt="" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="font-medium text-brand-ink hover:text-brand-blue line-clamp-2 transition">
                                <?= e($i['name']) ?>
                            </a>
                            <div class="text-xs text-gray-400 uppercase tracking-wider mt-0.5">SKU <?= e($i['sku']) ?></div>

                            <div class="mt-3 flex items-center gap-4">
                                <form method="post" action="<?= e(url('carrinho/atualizar')) ?>" class="flex items-center gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                    <label class="text-xs text-gray-500">Qtd</label>
                                    <input type="number" name="quantity" min="1" value="<?= e((string) $i['quantity']) ?>"
                                           class="w-16 bg-brand-cream border border-transparent rounded-lg px-2 py-1.5 text-sm focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                                    <button class="text-xs text-brand-blue hover:underline font-medium">atualizar</button>
                                </form>

                                <form method="post" action="<?= e(url('carrinho/remover')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                    <button class="text-xs text-rose-600 hover:underline font-medium">Remover</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <?php if ($showPrice): ?>
                                <div class="font-display font-bold text-brand-ink">
                                    <?= e(money_br((float) $i['price'] * (int) $i['quantity'])) ?>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    <?= e(money_br((float) $i['price'])) ?> × <?= e((string) $i['quantity']) ?>
                                </div>
                            <?php else: ?>
                                <div class="text-xs text-brand-blue font-semibold uppercase tracking-wider">Sob consulta</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="bg-white border border-gray-100 rounded-2xl shadow-soft p-6 h-fit space-y-5 lg:sticky lg:top-24">
                <h2 class="font-display font-bold text-brand-ink text-lg">Resumo</h2>

                <?php if ($showPrice): ?>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-brand-ink"><?= e(money_br($subtotal)) ?></span>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 pt-4 flex justify-between items-baseline">
                        <span class="font-display font-bold text-brand-ink">Total estimado</span>
                        <span class="font-display text-2xl font-bold text-brand-ink"><?= e(money_br($subtotal)) ?></span>
                    </div>
                    <p class="text-xs text-gray-500">Frete e impostos calculados após resposta do vendedor.</p>
                <?php else: ?>
                    <div class="bg-brand-blue/5 border border-brand-blue/20 rounded-xl p-4">
                        <p class="text-sm text-brand-blue-dark font-medium">Orçamento sob consulta</p>
                        <p class="text-xs text-gray-600 mt-1">
                            Solicite e nossa equipe responderá com preços personalizados em até 1 dia útil.
                        </p>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('carrinho/solicitar-orcamento')) ?>" class="space-y-3">
                    <?= csrf_field() ?>
                    <textarea name="notes" rows="3" placeholder="Observações para a equipe (opcional)"
                              class="w-full text-sm bg-brand-cream border border-transparent rounded-xl px-3 py-2.5 focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition placeholder:text-gray-400"></textarea>
                    <button class="w-full inline-flex items-center justify-center gap-2 bg-brand-blue text-white py-3.5 rounded-xl font-semibold hover:bg-brand-blue-dark shadow-brand transition">
                        Solicitar orçamento
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </form>

                <form method="post" action="<?= e(url('carrinho/limpar')) ?>">
                    <?= csrf_field() ?>
                    <button class="w-full text-sm text-gray-400 hover:text-rose-600 transition">Esvaziar carrinho</button>
                </form>
            </aside>
        </div>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
