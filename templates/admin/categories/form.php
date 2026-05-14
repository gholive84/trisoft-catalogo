<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$action = $isNew
    ? url('admin/categorias')
    : url('admin/categorias/' . $category['id']);
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= e(url('admin/categorias')) ?>" class="text-xs text-brand-muted hover:text-brand-ink">← Categorias</a>
        <h1 class="font-display text-2xl font-semibold text-brand-ink mt-1">
            <?= $isNew ? 'Nova categoria' : e($category['name']) ?>
        </h1>
    </div>
    <?php if (!$isNew): ?>
        <form method="post" action="<?= e(url('admin/categorias/' . $category['id'] . '/excluir')) ?>"
              onsubmit="return confirm('Confirma a exclusão desta categoria?');">
            <?= csrf_field() ?>
            <button class="text-sm text-rose-600 hover:underline">Excluir</button>
        </form>
    <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6">
    <?= csrf_field() ?>
    <?php if (!$isNew): ?><?= method_field('PUT') ?><?php endif; ?>

    <div class="space-y-6">
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">Informações</h2>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Nome *</label>
                <input type="text" name="name" required value="<?= e(old('name', $category['name'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Slug (URL)</label>
                <input type="text" name="slug" value="<?= e(old('slug', $category['slug'])) ?>" placeholder="auto-gerado"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 font-mono text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Descrição</label>
                <textarea name="description" rows="4"
                          class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e(old('description', $category['description'])) ?></textarea>
            </div>
        </div>

        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">SEO</h2>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Meta title</label>
                <input type="text" name="meta_title" maxlength="255"
                       value="<?= e($category['meta_title'] ?? '') ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Meta description</label>
                <textarea name="meta_description" rows="2" maxlength="500"
                          class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e($category['meta_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <aside class="space-y-6 h-fit lg:sticky lg:top-6">
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-3">
            <h2 class="font-display font-semibold text-brand-ink">Hierarquia</h2>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Categoria pai</label>
                <select name="parent_id" class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                    <option value="">— Sem pai (raiz) —</option>
                    <?php foreach ($allCategories as $c): ?>
                        <?php if ($category['id'] === $c['id']) continue; ?>
                        <option value="<?= e((string) $c['id']) ?>" <?= $category['parent_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Ordem</label>
                <input type="number" name="sort_order" value="<?= e((string) ($category['sort_order'] ?? 0)) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
            </div>
        </div>

        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-3">
            <h2 class="font-display font-semibold text-brand-ink">Status</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" <?= $category['is_active'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                Ativa (visível no catálogo)
            </label>
        </div>

        <button type="submit" class="w-full bg-brand-blue text-white py-3.5 rounded-full font-medium hover:bg-brand-blue-dark shadow-soft transition">
            <?= $isNew ? 'Criar categoria' : 'Salvar alterações' ?>
        </button>
    </aside>
</form>
<?php $this->endSection(); ?>
