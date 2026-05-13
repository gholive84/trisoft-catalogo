<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Catálogo Trisoft') ?> — Catálogo Trisoft</title>
    <meta name="description" content="<?= e($metaDescription ?? 'Catálogo de produtos Trisoft. Solicite seu orçamento online.') ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col">

    <?php $this->partial('header'); ?>

    <?php $this->partial('flash'); ?>

    <main class="flex-1">
        <?= $this->yield('content') ?>
    </main>

    <?php $this->partial('footer'); ?>
</body>
</html>
