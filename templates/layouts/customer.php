<?php /** @var App\Core\View $this */ ?>
<?php $openCart = (bool) \App\Core\Session::getFlash('open_cart_drawer', false); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? 'Minha conta']); ?>
</head>
<body class="bg-white text-brand-ink min-h-screen flex flex-col"
      x-data="{ cartOpen: <?= $openCart ? 'true' : 'false' ?> }">

    <?php $this->partial('header'); ?>
    <?php $this->partial('flash'); ?>

    <div class="flex-1 max-w-7xl mx-auto w-full px-6 lg:px-10 py-10 grid grid-cols-1 md:grid-cols-[240px_1fr] gap-10">
        <aside class="h-fit md:sticky md:top-8">
            <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-3 px-3">Minha conta</div>
            <nav class="flex flex-col gap-0.5 text-sm">
                <?php $current = $_SERVER['REQUEST_URI'] ?? '';
                $links = [
                    ['url' => url('minha-conta'),               'label' => 'Painel'],
                    ['url' => url('minha-conta/orcamentos'),    'label' => 'Meus orçamentos'],
                    ['url' => url('minha-conta/enderecos'),     'label' => 'Endereços'],
                    ['url' => url('minha-conta/perfil'),        'label' => 'Meus dados'],
                ];
                foreach ($links as $l):
                    $active = rtrim($current, '/') === rtrim($l['url'], '/');
                ?>
                    <a href="<?= e($l['url']) ?>" class="px-3 py-2.5 rounded-lg <?= $active ? 'bg-brand-ink text-white' : 'text-brand-ink hover:bg-gray-50' ?>">
                        <?= e($l['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main>
            <?= $this->yield('content') ?>
        </main>
    </div>

    <?php $this->partial('footer'); ?>
</body>
</html>
