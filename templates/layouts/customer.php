<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? 'Minha conta']); ?>
</head>
<body class="bg-brand-cream text-brand-ink min-h-screen flex flex-col">

    <?php $this->partial('header'); ?>
    <?php $this->partial('flash'); ?>

    <div class="flex-1 max-w-7xl mx-auto w-full px-4 py-8 grid grid-cols-1 md:grid-cols-[240px_1fr] gap-8">
        <aside class="bg-white rounded-2xl shadow-soft border border-gray-200 p-4 h-fit">
            <h3 class="text-xs font-display font-semibold text-gray-500 uppercase tracking-widest mb-3 px-2">Minha conta</h3>
            <nav class="flex flex-col gap-0.5 text-sm">
                <?php $current = $_SERVER['REQUEST_URI'] ?? ''; ?>
                <?php
                $links = [
                    ['url' => url('minha-conta'),               'label' => 'Painel',         'icon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
                    ['url' => url('minha-conta/orcamentos'),    'label' => 'Meus orçamentos','icon' => 'M9 12h6m-6 4h6M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z'],
                    ['url' => url('minha-conta/enderecos'),     'label' => 'Endereços',      'icon' => 'M12 2C8 2 5 5 5 9c0 7 7 13 7 13s7-6 7-13c0-4-3-7-7-7zm0 9a2 2 0 110-4 2 2 0 010 4z'],
                    ['url' => url('minha-conta/perfil'),        'label' => 'Meus dados',     'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ];
                foreach ($links as $l):
                    $active = rtrim($current, '/') === rtrim($l['url'], '/');
                ?>
                    <a href="<?= e($l['url']) ?>" class="px-3 py-2.5 rounded-lg flex items-center gap-2.5 text-sm <?= $active ? 'bg-brand-blue/10 text-brand-blue font-semibold' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $l['icon'] ?>"/></svg>
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
