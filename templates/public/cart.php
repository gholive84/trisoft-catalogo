<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$items     = $summary['items'];
$subtotal  = $summary['subtotal'];
$count     = $summary['count'];
$showPrice = can_see_prices();
?>
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Carrinho de orçamento</h1>
        <div class="text-sm text-gray-500"><?= e((string) $count) ?> item(ns)</div>
    </div>

    <?php if ($items === []): ?>
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-12 text-center">
            <p class="text-gray-600">Seu carrinho está vazio.</p>
            <a href="<?= e(url('/')) ?>" class="text-gray-900 underline mt-2 inline-block">Voltar ao catálogo</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">

            <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100">
                <?php foreach ($items as $i):
                    $img = $i['main_image_path']
                        ? upload_url('products/' . $i['main_image_path'])
                        : null;
                ?>
                    <div class="p-4 flex gap-4">
                        <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="w-20 h-20 shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                            <?php if ($img): ?>
                                <img src="<?= e($img) ?>" alt="" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="<?= e(url('produto/' . $i['slug'])) ?>" class="font-medium text-gray-900 hover:underline line-clamp-2">
                                <?= e($i['name']) ?>
                            </a>
                            <div class="text-xs text-gray-500 mt-0.5">SKU <?= e($i['sku']) ?></div>

                            <div class="mt-3 flex items-center gap-3">
                                <form method="post" action="<?= e(url('carrinho/atualizar')) ?>" class="flex items-center gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                    <label class="text-sm text-gray-600">Qtd:</label>
                                    <input type="number" name="quantity" min="1" value="<?= e((string) $i['quantity']) ?>"
                                           class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                                    <button class="text-sm text-gray-700 hover:text-gray-900 underline">atualizar</button>
                                </form>

                                <form method="post" action="<?= e(url('carrinho/remover')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e((string) $i['item_id']) ?>">
                                    <button class="text-sm text-red-600 hover:text-red-700 underline">remover</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-right">
                            <?php if ($showPrice): ?>
                                <div class="font-bold text-gray-900">
                                    <?= e(money_br((float) $i['price'] * (int) $i['quantity'])) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= e(money_br((float) $i['price'])) ?> × <?= e((string) $i['quantity']) ?>
                                </div>
                            <?php else: ?>
                                <div class="text-xs text-gray-500">A consultar</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="bg-white border border-gray-200 rounded-xl p-5 h-fit space-y-4 lg:sticky lg:top-20">
                <h2 class="font-semibold text-gray-900">Resumo</h2>

                <?php if ($showPrice): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900"><?= e(money_br($subtotal)) ?></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                        <span>Total estimado</span>
                        <span><?= e(money_br($subtotal)) ?></span>
                    </div>
                    <p class="text-xs text-gray-500">Frete e impostos serão calculados após a resposta do vendedor.</p>
                <?php else: ?>
                    <p class="text-sm text-gray-700">
                        Solicite o orçamento e nossa equipe responderá com preços personalizados.
                    </p>
                <?php endif; ?>

                <form method="post" action="<?= e(url('carrinho/solicitar-orcamento')) ?>" class="space-y-3">
                    <?= csrf_field() ?>
                    <textarea name="notes" rows="3" placeholder="Observações (opcional)"
                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900"></textarea>
                    <button class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800">
                        Solicitar orçamento
                    </button>
                </form>

                <form method="post" action="<?= e(url('carrinho/limpar')) ?>">
                    <?= csrf_field() ?>
                    <button class="w-full text-sm text-gray-500 hover:text-gray-700">Esvaziar carrinho</button>
                </form>
            </aside>
        </div>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
