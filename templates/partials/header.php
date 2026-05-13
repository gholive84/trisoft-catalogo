<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center gap-6">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-2 shrink-0">
            <span class="text-xl font-bold tracking-tight text-gray-900">Trisoft</span>
            <span class="text-xs text-gray-500 uppercase tracking-wider hidden sm:inline">Catálogo</span>
        </a>

        <form method="get" action="<?= e(url('busca')) ?>" class="flex-1 max-w-xl hidden md:block">
            <div class="relative">
                <input
                    type="search"
                    name="q"
                    placeholder="Buscar produtos, SKU…"
                    value="<?= e($_GET['q'] ?? '') ?>"
                    class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                >
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
            </div>
        </form>

        <nav class="flex items-center gap-4 ml-auto text-sm">
            <?php $cartBadge = auth() ? (new \App\Services\CartService())->badge() : 0; ?>
            <a href="<?= e(url('carrinho')) ?>" class="relative text-gray-700 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="hidden sm:inline">Carrinho</span>
                <?php if ($cartBadge > 0): ?>
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center">
                        <?= e((string) $cartBadge) ?>
                    </span>
                <?php endif; ?>
            </a>

            <?php if (auth()): ?>
                <?php if (has_role('admin', 'editor', 'seller')): ?>
                    <a href="<?= e(url('admin')) ?>" class="text-gray-700 hover:text-gray-900">Admin</a>
                <?php endif; ?>
                <div x-data="{open: false}" class="relative">
                    <button @click="open = !open" class="text-gray-700 hover:text-gray-900 flex items-center gap-1">
                        <?= e(auth()['name']) ?>
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5 5 5-5H5z"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                        <a href="<?= e(url('minha-conta')) ?>" class="block px-4 py-2 hover:bg-gray-50">Minha conta</a>
                        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="block px-4 py-2 hover:bg-gray-50">Meus orçamentos</a>
                        <form method="post" action="<?= e(url('logout')) ?>">
                            <?= csrf_field() ?>
                            <button class="block w-full text-left px-4 py-2 hover:bg-gray-50 text-red-600">Sair</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>" class="text-gray-700 hover:text-gray-900">Entrar</a>
                <a href="<?= e(url('cadastro')) ?>" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-gray-800">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
