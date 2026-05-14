<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Produtos</h1>
        <p class="text-sm text-brand-muted mt-1"><?= e((string) $total) ?> produto(s)</p>
    </div>
    <a href="<?= e(url('admin/produtos/novo')) ?>" class="bg-brand-ink text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-black transition inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Novo produto
    </a>
</div>

<form method="get" class="flex gap-2 mb-5">
    <div class="relative flex-1 max-w-md">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar nome, SKU, subtítulo..."
               class="w-full bg-gray-100 border border-transparent rounded-full pl-10 pr-4 py-2 text-sm focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
    </div>
    <select name="status" class="bg-gray-100 border border-transparent rounded-full px-4 py-2 text-sm focus:bg-white focus:border-brand-line">
        <option value="">Todos</option>
        <option value="active"   <?= $status==='active'?'selected':'' ?>>Ativos</option>
        <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inativos</option>
        <option value="featured" <?= $status==='featured'?'selected':'' ?>>Destaques</option>
    </select>
    <button class="bg-brand-ink text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-black transition">Filtrar</button>
</form>

<div class="bg-white border border-brand-line rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-brand-muted text-xs uppercase tracking-widest">
            <tr>
                <th class="px-4 py-3 text-left">Produto</th>
                <th class="px-4 py-3 text-left">SKU</th>
                <th class="px-4 py-3 text-right">Preço</th>
                <th class="px-4 py-3">Cats</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-brand-line">
            <?php foreach ($items as $p): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                                <?php if (!empty($p['thumb'])): ?>
                                    <img src="<?= e(upload_url('products/' . $p['thumb'])) ?>" class="w-full h-full object-cover" alt="">
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <a href="<?= e(url('admin/produtos/' . $p['id'] . '/editar')) ?>" class="font-medium text-brand-ink hover:text-brand-blue line-clamp-1"><?= e($p['name']) ?></a>
                                <?php if (!empty($p['subtitle'])): ?>
                                    <div class="text-xs text-brand-muted uppercase tracking-wider"><?= e($p['subtitle']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-brand-muted font-mono"><?= e($p['sku']) ?></td>
                    <td class="px-4 py-3 text-right">
                        <?php if ((float) $p['price'] > 0): ?>
                            <span class="font-medium"><?= e(money_br((float) $p['price'])) ?></span>
                        <?php else: ?>
                            <span class="text-brand-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-brand-muted"><?= e((string) $p['cat_count']) ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($p['is_active']): ?>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700">Ativo</span>
                        <?php else: ?>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600">Inativo</span>
                        <?php endif; ?>
                        <?php if ($p['is_featured']): ?>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-brand-green/10 text-brand-green-dark ml-1">★</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= e(url('produto/' . $p['sku'])) ?>" target="_blank" class="text-xs text-brand-muted hover:text-brand-ink">ver</a>
                            <a href="<?= e(url('admin/produtos/' . $p['id'] . '/editar')) ?>" class="text-xs text-brand-blue hover:underline font-medium">editar</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($items === []): ?>
                <tr><td colspan="6" class="px-4 py-12 text-center text-brand-muted">Nenhum produto encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($lastPage > 1): ?>
    <?php $this->partial('pagination', [
        'pagination' => ['page' => $page, 'lastPage' => $lastPage, 'perPage' => $perPage, 'total' => $total],
        'baseUrl'    => url('admin/produtos'),
        'query'      => ['q' => $q, 'status' => $status],
    ]); ?>
<?php endif; ?>
<?php $this->endSection(); ?>
