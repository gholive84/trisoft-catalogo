<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? null, 'metaDescription' => $metaDescription ?? null]); ?>
</head>
<body class="bg-white text-brand-ink min-h-screen flex flex-col">

    <?php $this->partial('header'); ?>
    <?php $this->partial('flash'); ?>

    <main class="flex-1">
        <?= $this->yield('content') ?>
    </main>

    <?php $this->partial('footer'); ?>
</body>
</html>
