<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Categorias</h1>
        <p class="text-sm text-brand-muted mt-1">Organize a árvore de produtos</p>
    </div>
    <a href="<?= e(url('admin/categorias/nova')) ?>" class="bg-brand-ink text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-black transition inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nova categoria
    </a>
</div>

<div class="bg-white border border-brand-line rounded-2xl overflow-hidden">
    <?php
    $renderNode = function (array $node, int $depth = 0) use (&$renderNode, $counts) {
        $id = (int) $node['id'];
        $count = $counts[$id] ?? 0;
        $hasChildren = !empty($node['children']);
    ?>
        <div class="flex items-center px-4 py-3 border-b border-brand-line last:border-0 hover:bg-gray-50">
            <div class="flex-1 flex items-center gap-2" style="padding-left: <?= $depth * 24 ?>px">
                <?php if ($hasChildren): ?>
                    <svg class="w-4 h-4 text-brand-muted shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <?php else: ?>
                    <span class="w-4 inline-block"></span>
                <?php endif; ?>
                <div>
                    <a href="<?= e(url('admin/categorias/' . $id . '/editar')) ?>" class="font-medium text-brand-ink hover:text-brand-blue">
                        <?= e($node['name']) ?>
                    </a>
                    <span class="text-xs text-brand-muted ml-2 font-mono"><?= e($node['slug']) ?></span>
                    <?php if (!$node['is_active']): ?>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded ml-2">inativa</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-xs text-brand-muted shrink-0">
                <?= $count ?> produto<?= $count === 1 ? '' : 's' ?>
            </div>
            <div class="ml-4 flex items-center gap-3 shrink-0">
                <a href="<?= e(url('categoria/' . $node['slug'])) ?>" target="_blank" class="text-xs text-brand-muted hover:text-brand-ink">ver</a>
                <a href="<?= e(url('admin/categorias/' . $id . '/editar')) ?>" class="text-xs text-brand-blue hover:underline font-medium">editar</a>
            </div>
        </div>
        <?php
        foreach ($node['children'] ?? [] as $child) $renderNode($child, $depth + 1);
    };
    foreach ($tree as $n) $renderNode($n, 0);
    if ($tree === []): ?>
        <div class="p-12 text-center text-brand-muted">Nenhuma categoria cadastrada.</div>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
