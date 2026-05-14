<header class="bg-white border-b border-gray-100 sticky top-0 z-30 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 h-20 flex items-center gap-6">
        <a href="<?= e(url('/')) ?>" class="flex items-center shrink-0">
            <img src="<?= e(asset('images/logo.png')) ?>" alt="Trisoft Revestimentos Funcionais"
                 class="h-12 w-auto" style="max-width: 200px;">
        </a>

        <form method="get" action="<?= e(url('busca')) ?>" class="flex-1 max-w-xl hidden md:block">
            <div class="relative">
                <input
                    type="search"
                    name="q"
                    placeholder="Buscar produtos, SKU…"
                    value="<?= e($_GET['q'] ?? '') ?>"
                    class="w-full bg-brand-cream/60 border border-transparent rounded-full pl-11 pr-4 py-2.5 text-sm placeholder:text-gray-400 focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"
                >
                <svg class="w-4 h-4 text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
            </div>
        </form>

        <nav class="flex items-center gap-3 sm:gap-5 ml-auto text-sm">
            <?php $cartBadge = auth() ? (new \App\Services\CartService())->badge() : 0; ?>
            <a href="<?= e(url('carrinho')) ?>" class="relative text-gray-700 hover:text-brand-blue flex items-center gap-1.5 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="hidden sm:inline font-medium">Orçamento</span>
                <?php if ($cartBadge > 0): ?>
                    <span class="absolute -top-1.5 -right-1.5 bg-brand-green text-white text-[10px] font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center ring-2 ring-white">
                        <?= e((string) $cartBadge) ?>
                    </span>
                <?php endif; ?>
            </a>

            <?php if (auth()): ?>
                <?php if (has_role('admin', 'editor', 'seller')): ?>
                    <a href="<?= e(url('admin')) ?>" class="hidden sm:inline-flex text-gray-600 hover:text-brand-blue font-medium">Admin</a>
                <?php endif; ?>
                <div x-data="{open: false}" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-brand-blue font-medium transition">
                        <span class="hidden sm:inline-flex w-9 h-9 bg-brand-blue/10 text-brand-blue rounded-full items-center justify-center text-xs font-bold">
                            <?= e(strtoupper(mb_substr(auth()['name'], 0, 2))) ?>
                        </span>
                        <span class="hidden md:inline"><?= e(explode(' ', auth()['name'])[0]) ?></span>
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5 5 5-5H5z"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         x-transition.opacity.scale.95.origin.top.right
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-brand border border-gray-100 py-1.5">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="text-sm font-medium text-gray-900 truncate"><?= e(auth()['name']) ?></div>
                            <div class="text-xs text-gray-500 truncate"><?= e(auth()['email']) ?></div>
                        </div>
                        <a href="<?= e(url('minha-conta')) ?>" class="block px-4 py-2 text-sm hover:bg-gray-50">Minha conta</a>
                        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="block px-4 py-2 text-sm hover:bg-gray-50">Meus orçamentos</a>
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="post" action="<?= e(url('logout')) ?>">
                                <?= csrf_field() ?>
                                <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-rose-600">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>" class="hidden sm:inline text-gray-700 hover:text-brand-blue font-medium">Entrar</a>
                <a href="<?= e(url('cadastro')) ?>" class="inline-flex items-center px-4 py-2 rounded-full bg-brand-blue text-white text-sm font-semibold hover:bg-brand-blue-dark shadow-soft transition">
                    Criar conta
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>
