<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?> — Catálogo Trisoft</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex">

    <aside class="w-60 bg-gray-900 text-gray-100 flex flex-col">
        <div class="px-5 py-4 border-b border-gray-800">
            <a href="<?= e(url('admin')) ?>" class="font-bold text-lg">Trisoft Admin</a>
        </div>
        <nav class="flex flex-col p-3 gap-1 text-sm flex-1">
            <a href="<?= e(url('admin')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Dashboard</a>
            <a href="<?= e(url('admin/produtos')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Produtos</a>
            <a href="<?= e(url('admin/categorias')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Categorias</a>
            <a href="<?= e(url('admin/orcamentos')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Orçamentos</a>
            <?php if (has_role('admin')): ?>
                <a href="<?= e(url('admin/usuarios')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Usuários</a>
                <a href="<?= e(url('admin/analytics')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Analytics</a>
                <a href="<?= e(url('admin/configuracoes')) ?>" class="px-3 py-2 rounded hover:bg-gray-800">Configurações</a>
            <?php endif; ?>
        </nav>
        <div class="p-3 border-t border-gray-800 text-xs">
            <div class="text-gray-400">Logado como</div>
            <div class="font-medium"><?= e(auth()['name'] ?? '') ?></div>
            <form method="post" action="<?= e(url('logout')) ?>" class="mt-2">
                <?= csrf_field() ?>
                <button class="text-red-300 hover:text-red-200">Sair</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <?php $this->partial('flash'); ?>
        <main class="flex-1 p-6 overflow-x-auto">
            <?= $this->yield('content') ?>
        </main>
    </div>

</body>
</html>
