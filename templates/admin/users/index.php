<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$roleLabels = [
    'admin'    => ['Admin',    'bg-rose-50 text-rose-700'],
    'editor'   => ['Editor',   'bg-indigo-50 text-indigo-700'],
    'seller'   => ['Vendedor', 'bg-amber-50 text-amber-700'],
    'customer' => ['Cliente',  'bg-gray-100 text-gray-700'],
];
$statusLabels = [
    'active'   => ['Ativo',     'bg-emerald-50 text-emerald-700'],
    'inactive' => ['Inativo',   'bg-gray-100 text-gray-600'],
    'pending'  => ['Pendente',  'bg-amber-50 text-amber-700'],
];
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Usuários</h1>
        <p class="text-sm text-brand-muted mt-1"><?= e((string) $total) ?> usuário(s)</p>
    </div>
    <a href="<?= e(url('admin/usuarios/novo')) ?>" class="bg-brand-ink text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-black transition">
        + Novo usuário
    </a>
</div>

<div class="flex flex-wrap gap-2 mb-5">
    <a href="<?= e(url('admin/usuarios')) ?>"
       class="<?= $role === '' ? 'bg-brand-ink text-white' : 'bg-gray-100 text-brand-ink hover:bg-gray-200' ?> px-4 py-1.5 rounded-full text-sm font-medium transition">
        Todos
    </a>
    <?php foreach ($roleLabels as $key => [$label, $color]):
        $n = $kpis[$key] ?? 0; if ($n === 0 && $role !== $key) continue; ?>
        <a href="<?= e(url('admin/usuarios?role=' . $key)) ?>"
           class="<?= $role === $key ? 'bg-brand-ink text-white' : 'bg-gray-100 text-brand-ink hover:bg-gray-200' ?> px-4 py-1.5 rounded-full text-sm font-medium transition">
            <?= e($label) ?>s <span class="text-xs opacity-70 ml-1">(<?= e((string) $n) ?>)</span>
        </a>
    <?php endforeach; ?>
</div>

<form method="get" class="mb-5">
    <input type="hidden" name="role" value="<?= e($role) ?>">
    <div class="relative max-w-md">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Nome, e-mail, empresa..."
               class="w-full bg-gray-100 border border-transparent rounded-full pl-10 pr-4 py-2 text-sm focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
    </div>
</form>

<div class="bg-white border border-brand-line rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-brand-muted text-xs uppercase tracking-widest">
            <tr>
                <th class="px-4 py-3 text-left">Nome / Empresa</th>
                <th class="px-4 py-3 text-left">E-mail</th>
                <th class="px-4 py-3">Função</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-left">Cadastro</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-brand-line">
            <?php foreach ($items as $u):
                [$rLabel, $rColor] = $roleLabels[$u['role']] ?? [$u['role'], 'bg-gray-100 text-gray-700'];
                [$sLabel, $sColor] = $statusLabels[$u['status']] ?? [$u['status'], 'bg-gray-100 text-gray-700'];
            ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-brand-ink"><?= e($u['name']) ?></div>
                        <?php if (!empty($u['company_name'])): ?>
                            <div class="text-xs text-brand-muted"><?= e($u['company_name']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-brand-muted"><?= e($u['email']) ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold <?= e($rColor) ?>">
                            <?= e($rLabel) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold <?= e($sColor) ?>">
                            <?= e($sLabel) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-brand-muted text-xs"><?= e(date_br($u['created_at'])) ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="<?= e(url('admin/usuarios/' . $u['id'] . '/editar')) ?>" class="text-xs text-brand-blue hover:underline font-medium">editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($items === []): ?>
                <tr><td colspan="6" class="px-4 py-12 text-center text-brand-muted">Nenhum usuário encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($lastPage > 1): ?>
    <?php $this->partial('pagination', [
        'pagination' => ['page' => $page, 'lastPage' => $lastPage, 'perPage' => $perPage, 'total' => $total],
        'baseUrl'    => url('admin/usuarios'),
        'query'      => ['role' => $role, 'q' => $q],
    ]); ?>
<?php endif; ?>
<?php $this->endSection(); ?>
