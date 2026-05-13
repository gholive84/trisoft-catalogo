<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Minha conta') ?> — Catálogo Trisoft</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <?php $this->partial('header'); ?>
    <?php $this->partial('flash'); ?>

    <div class="flex-1 max-w-7xl mx-auto w-full px-4 py-8 grid grid-cols-1 md:grid-cols-[220px_1fr] gap-8">
        <aside class="bg-white rounded-lg border border-gray-200 p-4 h-fit">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Minha conta</h3>
            <nav class="flex flex-col gap-1 text-sm">
                <a href="<?= e(url('minha-conta')) ?>" class="px-3 py-2 rounded hover:bg-gray-100">Painel</a>
                <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="px-3 py-2 rounded hover:bg-gray-100">Meus orçamentos</a>
                <a href="<?= e(url('minha-conta/enderecos')) ?>" class="px-3 py-2 rounded hover:bg-gray-100">Endereços</a>
                <a href="<?= e(url('minha-conta/perfil')) ?>" class="px-3 py-2 rounded hover:bg-gray-100">Meus dados</a>
            </nav>
        </aside>

        <main>
            <?= $this->yield('content') ?>
        </main>
    </div>

    <?php $this->partial('footer'); ?>
</body>
</html>
