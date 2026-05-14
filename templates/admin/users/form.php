<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$action = $isNew
    ? url('admin/usuarios')
    : url('admin/usuarios/' . $user['id']);
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= e(url('admin/usuarios')) ?>" class="text-xs text-brand-muted hover:text-brand-ink">← Usuários</a>
        <h1 class="font-display text-2xl font-semibold text-brand-ink mt-1">
            <?= $isNew ? 'Novo usuário' : e($user['name']) ?>
        </h1>
    </div>
    <?php if (!$isNew && (int) $user['id'] !== (int) auth_id()): ?>
        <form method="post" action="<?= e(url('admin/usuarios/' . $user['id'] . '/excluir')) ?>"
              onsubmit="return confirm('Confirma a exclusão deste usuário?');">
            <?= csrf_field() ?>
            <button class="text-sm text-rose-600 hover:underline">Excluir</button>
        </form>
    <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" class="max-w-2xl">
    <?= csrf_field() ?>
    <?php if (!$isNew): ?><?= method_field('PUT') ?><?php endif; ?>

    <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Nome *</label>
                <input type="text" name="name" required value="<?= e(old('name', $user['name'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">E-mail *</label>
                <input type="email" name="email" required value="<?= e(old('email', $user['email'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Função *</label>
                <select name="role" required class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    <option value="customer" <?= $user['role']==='customer'?'selected':'' ?>>Cliente</option>
                    <option value="seller"   <?= $user['role']==='seller'?'selected':'' ?>>Vendedor</option>
                    <option value="editor"   <?= $user['role']==='editor'?'selected':'' ?>>Editor</option>
                    <option value="admin"    <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Status *</label>
                <select name="status" required class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    <option value="active"   <?= $user['status']==='active'?'selected':'' ?>>Ativo</option>
                    <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>>Inativo</option>
                    <option value="pending"  <?= $user['status']==='pending'?'selected':'' ?>>Pendente</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Telefone</label>
                <input type="tel" name="phone" value="<?= e(old('phone', $user['phone'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">CPF / CNPJ</label>
                <input type="text" name="document" value="<?= e(old('document', $user['document'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Empresa</label>
            <input type="text" name="company_name" value="<?= e(old('company_name', $user['company_name'])) ?>"
                   class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
        </div>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">
                Senha <?= $isNew ? '*' : '(deixe em branco para manter)' ?>
            </label>
            <input type="password" name="password" minlength="8"
                   <?= $isNew ? 'required' : '' ?>
                   class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            <p class="text-xs text-brand-muted mt-1">Mínimo 8 caracteres.</p>
        </div>
    </div>

    <button type="submit" class="mt-5 bg-brand-blue text-white px-8 py-3 rounded-full font-medium hover:bg-brand-blue-dark shadow-soft transition">
        <?= $isNew ? 'Criar usuário' : 'Salvar alterações' ?>
    </button>
</form>
<?php $this->endSection(); ?>
