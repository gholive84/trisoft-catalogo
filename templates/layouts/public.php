<?php /** @var App\Core\View $this */ ?>
<?php
// Lê (e consome) a flag set por CartController quando algo foi adicionado.
// Quando true, inicializa o Alpine com cartOpen = true (abre o drawer no load).
$openCart = (bool) \App\Core\Session::getFlash('open_cart_drawer', false);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? null, 'metaDescription' => $metaDescription ?? null]); ?>
</head>
<body class="bg-white text-brand-ink min-h-screen flex flex-col"
      x-data="{ cartOpen: <?= $openCart ? 'true' : 'false' ?> }">

    <?php $this->partial('header'); ?>
    <?php $this->partial('flash'); ?>

    <main class="flex-1">
        <?= $this->yield('content') ?>
    </main>

    <?php $this->partial('footer'); ?>
</body>
</html>
