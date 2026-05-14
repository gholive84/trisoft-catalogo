<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? 'Admin']); ?>
</head>
<body class="bg-gray-50 text-brand-ink min-h-screen flex">

    <aside class="w-64 bg-brand-blue-900 text-gray-100 flex flex-col shrink-0">
        <div class="px-5 py-5 border-b border-white/10">
            <a href="<?= e(url('admin')) ?>" class="flex items-center gap-2">
                <img src="<?= e(asset('images/logo-mark.png')) ?>" alt="Trisoft" class="h-9 w-auto bg-white rounded-md p-1">
                <span class="font-display font-bold text-base">Admin</span>
            </a>
        </div>
        <nav class="flex flex-col p-3 gap-1 text-sm flex-1">
            <?php
            $current = $_SERVER['REQUEST_URI'] ?? '';
            $adminLinks = [
                ['url' => url('admin'),               'label' => 'Dashboard',     'icon' => 'M4 6h6V4H4v2zm10 14h6v-2h-6v2zM4 20h6V10H4v10zm10 0V4l6 0v6h-6'],
                ['url' => url('admin/produtos'),      'label' => 'Produtos',      'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4'],
                ['url' => url('admin/categorias'),   'label' => 'Categorias',    'icon' => 'M4 6h16M4 12h16M4 18h16'],
                ['url' => url('admin/orcamentos'),   'label' => 'Orçamentos',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ];
            if (has_role('admin')) {
                $adminLinks[] = ['url' => url('admin/usuarios'),       'label' => 'Usuários',       'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-2a4 4 0 11-8 0 4 4 0 018 0z'];
                $adminLinks[] = ['url' => url('admin/analytics'),      'label' => 'Analytics',      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'];
                $adminLinks[] = ['url' => url('admin/configuracoes'),  'label' => 'Configurações',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'];
            }
            foreach ($adminLinks as $l):
                $active = rtrim($current, '/') === rtrim($l['url'], '/');
            ?>
                <a href="<?= e($l['url']) ?>" class="px-3 py-2.5 rounded-lg flex items-center gap-2.5 transition <?= $active ? 'bg-white/10 text-white font-semibold' : 'text-gray-300 hover:bg-white/5 hover:text-white' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $l['icon'] ?>"/></svg>
                    <?= e($l['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3 border-t border-white/10 text-xs">
            <div class="text-gray-400">Logado como</div>
            <div class="font-medium text-white truncate"><?= e(auth()['name'] ?? '') ?></div>
            <div class="flex items-center justify-between mt-2 gap-2">
                <a href="<?= e(url('/')) ?>" class="text-gray-400 hover:text-white">Ver site</a>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="text-rose-300 hover:text-rose-200">Sair</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <?php $this->partial('flash'); ?>
        <main class="flex-1 p-6 lg:p-8 overflow-x-auto">
            <?= $this->yield('content') ?>
        </main>
    </div>

</body>
</html>
