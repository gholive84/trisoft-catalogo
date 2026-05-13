<?php /** @var App\Core\View $this */ $this->extend('layouts/customer'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => ['Solicitado', 'bg-amber-100 text-amber-800'],
    'quoted'          => ['Respondido', 'bg-blue-100 text-blue-800'],
    'approved'        => ['Aprovado',   'bg-green-100 text-green-800'],
    'rejected'        => ['Rejeitado',  'bg-red-100 text-red-800'],
    'expired'         => ['Expirado',   'bg-gray-100 text-gray-700'],
    'pending_payment' => ['Aguardando pagamento', 'bg-orange-100 text-orange-800'],
    'paid'            => ['Pago',       'bg-green-100 text-green-800'],
    'processing'      => ['Em produção', 'bg-blue-100 text-blue-800'],
    'shipped'         => ['Enviado',    'bg-indigo-100 text-indigo-800'],
    'delivered'       => ['Entregue',   'bg-green-100 text-green-800'],
    'canceled'        => ['Cancelado',  'bg-gray-100 text-gray-700'],
];
?>

<h1 class="text-2xl font-bold text-gray-900">Meus orçamentos</h1>
<p class="text-sm text-gray-500 mt-1"><?= e((string) $total) ?> orçamento(s)</p>

<?php if ($orders === []): ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center mt-6">
        <p class="text-gray-600">Você ainda não tem orçamentos.</p>
        <a href="<?= e(url('/')) ?>" class="text-gray-900 underline mt-2 inline-block">Explorar o catálogo</a>
    </div>
<?php else: ?>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mt-6">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Nº</th>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($orders as $o):
                    [$label, $color] = $statusLabels[$o['status']] ?? [$o['status'], 'bg-gray-100 text-gray-700'];
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900"><?= e($o['order_number']) ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= e(date_br($o['created_at'])) ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium <?= e($color) ?>">
                                <?= e($label) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900">
                            <?= can_see_prices() ? e(money_br((float) $o['total'])) : '—' ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?= e(url('minha-conta/orcamentos/' . $o['order_number'])) ?>"
                               class="text-gray-700 hover:text-gray-900 hover:underline">ver detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $this->endSection(); ?>
