<?php /** @var App\Core\View $this */ ?>
<?php $openCart = (bool) \App\Core\Session::getFlash('open_cart_drawer', false); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => $title ?? 'Admin']); ?>
</head>
<body class="bg-brand-cream text-brand-ink min-h-screen flex"
      x-data="{ cartOpen: <?= $openCart ? 'true' : 'false' ?>, sidebarOpen: false }">

    <aside class="w-64 bg-brand-ink text-white flex flex-col shrink-0 sticky top-0 h-screen">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="<?= e(url('admin')) ?>" class="flex items-center gap-3">
                <img src="<?= e(asset('images/logo-mark.png')) ?>" alt="Trisoft" class="h-9 w-auto bg-white rounded-lg p-1">
                <div>
                    <div class="font-display font-bold text-white leading-tight">Trisoft</div>
                    <div class="text-[10px] uppercase tracking-widest text-white/50">Painel admin</div>
                </div>
            </a>
            <a href="<?= e(url('/')) ?>" target="_blank"
               class="mt-4 flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white text-xs px-3 py-2 rounded-full transition font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ver site
            </a>
        </div>

        <nav class="flex flex-col p-3 gap-1 text-sm flex-1 overflow-y-auto">
            <?php
            $current = $_SERVER['REQUEST_URI'] ?? '';
            $adminLinks = [
                ['url' => url('admin'),               'label' => 'Dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7m-9 2v8a2 2 0 002 2h2a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2h2a2 2 0 002-2v-8M5 10v10', 'match' => 'exact'],
                ['url' => url('admin/produtos'),      'label' => 'Produtos',   'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4', 'match' => 'prefix'],
                ['url' => url('admin/categorias'),    'label' => 'Categorias', 'icon' => 'M7 7h10M7 12h10M7 17h6', 'match' => 'prefix'],
                ['url' => url('admin/orcamentos'),    'label' => 'Orçamentos', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'prefix'],
            ];
            if (has_role('admin')) {
                $adminLinks[] = ['url' => url('admin/usuarios'),       'label' => 'Usuários',       'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-2a4 4 0 11-8 0 4 4 0 018 0z', 'match' => 'prefix'];
                $adminLinks[] = ['url' => url('admin/analytics'),      'label' => 'Analytics',      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'prefix'];
                $adminLinks[] = ['url' => url('admin/configuracoes'),  'label' => 'Configurações',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'prefix'];
            }
            foreach ($adminLinks as $l):
                $active = $l['match'] === 'exact'
                    ? rtrim($current, '/') === rtrim($l['url'], '/')
                    : str_starts_with(rtrim($current, '/'), rtrim($l['url'], '/'));
            ?>
                <a href="<?= e($l['url']) ?>"
                   class="px-3 py-2.5 rounded-xl flex items-center gap-3 text-sm transition
                          <?= $active
                                ? 'bg-brand-blue text-white font-medium shadow-lg shadow-brand-blue/30'
                                : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="<?= $active ? '2' : '1.75' ?>" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="<?= $l['icon'] ?>"/>
                    </svg>
                    <?= e($l['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-3 border-t border-white/10 text-xs">
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/5">
                <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center text-xs font-bold">
                    <?= e(strtoupper(mb_substr(auth()['name'] ?? '?', 0, 2))) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white font-medium truncate text-[13px]"><?= e(auth()['name'] ?? '') ?></div>
                    <div class="text-white/40 truncate text-[11px]"><?= e(auth_role() ?? '') ?></div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 px-3 gap-3">
                <a href="<?= e(url('/')) ?>" class="text-white/50 hover:text-white text-[11px] uppercase tracking-wider">Ver site</a>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="text-rose-400 hover:text-rose-300 text-[11px] uppercase tracking-wider">Sair</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-brand-cream">
        <?php $this->partial('flash'); ?>
        <main class="flex-1 p-6 lg:p-10 min-w-0">
            <?= $this->yield('content') ?>
        </main>
    </div>

</body>
</html>
