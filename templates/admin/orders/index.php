<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => ['Solicitado',  'bg-amber-50 text-amber-800',         '⏱'],
    'quoted'          => ['Respondido',  'bg-brand-blue/10 text-brand-blue',   '✓'],
    'approved'        => ['Aprovado',    'bg-emerald-50 text-emerald-700',     '✓'],
    'rejected'        => ['Rejeitado',   'bg-rose-50 text-rose-800',           '×'],
    'expired'         => ['Expirado',    'bg-gray-100 text-gray-700',          '·'],
    'pending_payment' => ['Aguardando pagto', 'bg-orange-50 text-orange-800',  '$'],
    'paid'            => ['Pago',        'bg-emerald-50 text-emerald-700',     '$'],
    'processing'      => ['Em produção', 'bg-brand-blue/10 text-brand-blue',   '↻'],
    'shipped'         => ['Enviado',     'bg-indigo-50 text-indigo-800',       '→'],
    'delivered'       => ['Entregue',    'bg-emerald-50 text-emerald-700',     '✓'],
    'canceled'        => ['Cancelado',   'bg-gray-100 text-gray-700',          '×'],
];
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Orçamentos</h1>
        <p class="text-sm text-brand-muted mt-1"><?= e((string) $total) ?> registro(s)</p>
    </div>
</div>

<!-- Tabs por status -->
<div class="flex flex-wrap gap-2 mb-5">
    <a href="<?= e(url('admin/orcamentos')) ?>"
       class="<?= $status === '' ? 'bg-brand-ink text-white' : 'bg-gray-100 text-brand-ink hover:bg-gray-200' ?> px-4 py-1.5 rounded-full text-sm font-medium transition">
        Todos <span class="text-xs opacity-70 ml-1">(<?= e((string) array_sum($kpis)) ?>)</span>
    </a>
    <?php foreach ($statusLabels as $key => [$label, $color, $icon]):
        $n = $kpis[$key] ?? 0;
        if ($n === 0 && $key !== $status) continue;
    ?>
        <a href="<?= e(url('admin/orcamentos?status=' . $key)) ?>"
           class="<?= $status === $key ? 'bg-brand-ink text-white' : 'bg-gray-100 text-brand-ink hover:bg-gray-200' ?> px-4 py-1.5 rounded-full text-sm font-medium transition">
            <?= e($label) ?> <span class="text-xs opacity-70 ml-1">(<?= e((string) $n) ?>)</span>
        </a>
    <?php endforeach; ?>
</div>

<form method="get" class="mb-5">
    <input type="hidden" name="status" value="<?= e($status) ?>">
    <div class="relative max-w-md">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar nº, cliente, e-mail..."
               class="w-full bg-gray-100 border border-transparent rounded-full pl-10 pr-4 py-2 text-sm focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
    </div>
</form>

<div class="bg-white border border-brand-line rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-brand-muted text-xs uppercase tracking-widest">
            <tr>
                <th class="px-4 py-3 text-left">Número</th>
                <th class="px-4 py-3 text-left">Cliente</th>
                <th class="px-4 py-3 text-left">Data</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-brand-line">
            <?php foreach ($items as $o):
                [$label, $color, $icon] = $statusLabels[$o['status']] ?? [$o['status'], 'bg-gray-100 text-gray-700', '·'];
            ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium font-mono text-brand-ink"><?= e($o['order_number']) ?></td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-brand-ink"><?= e($o['customer_name']) ?></div>
                        <div class="text-xs text-brand-muted"><?= e($o['customer_email']) ?></div>
                    </td>
                    <td class="px-4 py-3 text-brand-muted"><?= e(date_br($o['created_at'], 'd/m/Y H:i')) ?></td>
                    <td class="px-4 py-3 text-right font-medium">
                        <?= (float) $o['total'] > 0 ? e(money_br((float) $o['total'])) : '—' ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold <?= e($color) ?>">
                            <?= e($label) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="<?= e(url('admin/orcamentos/' . $o['id'])) ?>" class="text-xs text-brand-blue hover:underline font-medium">abrir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($items === []): ?>
                <tr><td colspan="6" class="px-4 py-12 text-center text-brand-muted">Nenhum orçamento.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($lastPage > 1): ?>
    <?php $this->partial('pagination', [
        'pagination' => ['page' => $page, 'lastPage' => $lastPage, 'perPage' => $perPage, 'total' => $total],
        'baseUrl'    => url('admin/orcamentos'),
        'query'      => ['status' => $status, 'q' => $q],
    ]); ?>
<?php endif; ?>
<?php $this->endSection(); ?>
