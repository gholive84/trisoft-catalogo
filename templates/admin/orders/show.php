<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => 'Solicitado',
    'quoted'          => 'Respondido',
    'approved'        => 'Aprovado',
    'rejected'        => 'Rejeitado',
    'expired'         => 'Expirado',
    'pending_payment' => 'Aguardando pagamento',
    'paid'            => 'Pago',
    'processing'      => 'Em produção',
    'shipped'         => 'Enviado',
    'delivered'       => 'Entregue',
    'canceled'        => 'Cancelado',
];
$canRespond = in_array($order['status'], ['quote_requested', 'quoted'], true);
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= e(url('admin/orcamentos')) ?>" class="text-xs text-brand-muted hover:text-brand-ink">← Orçamentos</a>
        <h1 class="font-display text-2xl font-semibold text-brand-ink mt-1 font-mono"><?= e($order['order_number']) ?></h1>
        <div class="text-sm text-brand-muted mt-1">
            Criado em <?= e(date_br($order['created_at'], 'd/m/Y H:i')) ?>
            <?php if ($order['quoted_at']): ?>
                · Respondido em <?= e(date_br($order['quoted_at'], 'd/m/Y H:i')) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-right">
        <div class="text-xs uppercase tracking-widest text-brand-muted">Status</div>
        <div class="font-medium text-brand-ink text-lg"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></div>
    </div>
</div>

<form method="post" action="<?= e(url('admin/orcamentos/' . $order['id'] . '/responder')) ?>"
      class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
    <?= csrf_field() ?>

    <div class="space-y-6">
        <!-- Itens (editáveis se can respond) -->
        <div class="bg-white border border-brand-line rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-brand-line">
                <h2 class="font-display font-semibold text-brand-ink">Itens do orçamento</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-brand-muted text-xs uppercase tracking-widest">
                    <tr>
                        <th class="px-4 py-3 text-left">Produto</th>
                        <th class="px-4 py-3 text-right w-20">Qtd</th>
                        <th class="px-4 py-3 text-right w-32">Preço unit.</th>
                        <th class="px-4 py-3 text-right w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-line">
                    <?php foreach ($items as $i):
                        $snap = is_array($i['product_snapshot'])
                            ? $i['product_snapshot']
                            : json_decode((string) $i['product_snapshot'], true) ?: [];
                    ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-brand-ink"><?= e($snap['name'] ?? '—') ?></div>
                                <div class="text-xs text-brand-muted font-mono">SKU <?= e($snap['sku'] ?? '') ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($canRespond): ?>
                                    <input type="number" name="quantity[<?= e((string) $i['id']) ?>]"
                                           value="<?= e((string) $i['quantity']) ?>" min="1"
                                           class="w-full text-right border border-brand-line rounded-lg px-2 py-1 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                <?php else: ?>
                                    <div class="text-right"><?= e((string) $i['quantity']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($canRespond): ?>
                                    <input type="text" name="unit_price[<?= e((string) $i['id']) ?>]"
                                           value="<?= e(number_format((float) $i['unit_price'], 2, ',', '')) ?>"
                                           placeholder="0,00"
                                           class="w-full text-right border border-brand-line rounded-lg px-2 py-1 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                <?php else: ?>
                                    <div class="text-right"><?= e(money_br((float) $i['unit_price'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                <?= e(money_br((float) $i['total'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50 text-sm">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-brand-muted">Subtotal</td>
                        <td class="px-4 py-3 text-right font-medium"><?= e(money_br((float) $order['subtotal'])) ?></td>
                    </tr>
                    <?php if ((float) $order['discount'] > 0): ?>
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-emerald-700">Desconto</td>
                        <td class="px-4 py-3 text-right text-emerald-700">− <?= e(money_br((float) $order['discount'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ((float) $order['shipping_cost'] > 0): ?>
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Frete</td>
                        <td class="px-4 py-3 text-right"><?= e(money_br((float) $order['shipping_cost'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="font-bold">
                        <td colspan="3" class="px-4 py-3 text-right">Total</td>
                        <td class="px-4 py-3 text-right text-base"><?= e(money_br((float) $order['total'])) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Observações do cliente -->
        <?php if (!empty($order['customer_notes'])): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <h3 class="font-display font-semibold text-amber-900 text-sm mb-2">Observações do cliente</h3>
                <p class="text-sm text-amber-900 whitespace-pre-line"><?= e($order['customer_notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Resposta (apenas para quote_requested ou quoted) -->
        <?php if ($canRespond): ?>
            <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
                <h2 class="font-display font-semibold text-brand-ink">Resposta ao cliente</h2>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Mensagem ao cliente</label>
                    <textarea name="customer_notes" rows="4" placeholder="Detalhes da proposta, prazo de produção, condições..."
                              class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e($order['customer_notes'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Anotações internas (não enviadas ao cliente)</label>
                    <textarea name="internal_notes" rows="2" placeholder="Notas para a equipe interna..."
                              class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e($order['internal_notes'] ?? '') ?></textarea>
                </div>
            </div>
        <?php else: ?>
            <?php if (!empty($order['internal_notes'])): ?>
                <div class="bg-gray-50 border border-brand-line rounded-2xl p-5">
                    <h3 class="font-display font-semibold text-brand-ink text-sm mb-2">Anotações internas</h3>
                    <p class="text-sm text-brand-muted whitespace-pre-line"><?= e($order['internal_notes']) ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-4 h-fit lg:sticky lg:top-6">
        <div class="bg-white border border-brand-line rounded-2xl p-5">
            <h3 class="font-display font-semibold text-brand-ink mb-3">Cliente</h3>
            <div class="text-sm space-y-1.5">
                <div class="font-medium text-brand-ink"><?= e($customer['name']) ?></div>
                <a href="mailto:<?= e($customer['email']) ?>" class="block text-brand-blue hover:underline"><?= e($customer['email']) ?></a>
                <?php if (!empty($customer['phone'])): ?>
                    <div class="text-brand-muted">📱 <?= e($customer['phone']) ?></div>
                <?php endif; ?>
                <?php if (!empty($customer['company_name'])): ?>
                    <div class="text-brand-muted">🏢 <?= e($customer['company_name']) ?></div>
                <?php endif; ?>
                <?php if (!empty($customer['document'])): ?>
                    <div class="text-xs text-brand-muted">CPF/CNPJ: <?= e($customer['document']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($canRespond): ?>
            <div class="bg-white border border-brand-line rounded-2xl p-5 space-y-3">
                <h3 class="font-display font-semibold text-brand-ink">Ajustes</h3>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-1.5">Desconto (R$)</label>
                    <input type="text" name="discount" value="<?= e(number_format((float) $order['discount'], 2, ',', '')) ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-1.5">Frete (R$)</label>
                    <input type="text" name="shipping_cost" value="<?= e(number_format((float) $order['shipping_cost'], 2, ',', '')) ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-1.5">Validade (dias)</label>
                    <input type="number" name="expires_in_days" value="15" min="1"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                    <?php if ($order['expires_at']): ?>
                        <div class="text-xs text-brand-muted mt-1">Atual: <?= e(date_br($order['expires_at'], 'd/m/Y')) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" name="action" value="send"
                    class="w-full bg-brand-blue text-white py-3.5 rounded-full font-medium hover:bg-brand-blue-dark shadow-soft transition">
                Enviar resposta ao cliente
            </button>
            <button type="submit" name="action" value="save_draft"
                    class="w-full text-sm text-brand-muted hover:text-brand-ink py-2">
                Salvar como rascunho
            </button>
        <?php endif; ?>

        <?php if (in_array($order['status'], ['quote_requested', 'quoted'], true)): ?>
            <form method="post" action="<?= e(url('admin/orcamentos/' . $order['id'] . '/cancelar')) ?>"
                  onsubmit="return confirm('Cancelar este orçamento?');" class="text-center">
                <?= csrf_field() ?>
                <button class="text-xs text-rose-600 hover:underline">Cancelar orçamento</button>
            </form>
        <?php endif; ?>
    </aside>
</form>
<?php $this->endSection(); ?>
