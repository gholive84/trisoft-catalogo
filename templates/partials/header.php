<?php
$current = $_SERVER['REQUEST_URI'] ?? '';
$baseUrl = \App\Core\Config::baseUrl();
$isHome  = rtrim($current, '/') === rtrim($baseUrl, '/');

// Badge do carrinho — apenas logado tem carrinho persistente
$cartBadge = 0;
if (auth()) {
    try { $cartBadge = (new \App\Services\CartService())->badge(); } catch (\Throwable) {}
}
?>
<header class="bg-white border-b border-brand-line">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 h-20 flex items-center">
        <!-- Logo -->
        <a href="<?= e(url('/')) ?>" class="flex items-center shrink-0">
            <img src="<?= e(asset('images/logo.png')) ?>" alt="Trisoft Revestimentos" class="h-11 w-auto" style="max-width: 180px;">
        </a>

        <!-- Nav central -->
        <nav class="flex-1 flex justify-center items-center gap-10 text-sm tracking-widest uppercase font-medium">
            <a href="<?= e(url('/')) ?>" class="<?= $isHome ? 'text-brand-ink' : 'text-brand-muted hover:text-brand-ink' ?> transition">
                Produtos
            </a>
        </nav>

        <!-- Ações -->
        <div class="flex items-center gap-4 sm:gap-5 text-sm">
            <?php if (auth()): ?>
                <?php if (has_role('admin', 'editor', 'seller')): ?>
                    <a href="<?= e(url('admin')) ?>" class="hidden md:inline text-brand-muted hover:text-brand-ink uppercase tracking-widest text-xs font-medium">Admin</a>
                <?php endif; ?>

                <!-- Minha conta (dropdown) -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2 text-brand-ink hover:text-brand-blue transition"
                            aria-label="Minha conta">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         x-transition.opacity.scale.95.origin.top.right
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-brand-line py-2 z-40">
                        <div class="px-4 py-3 border-b border-brand-line">
                            <div class="text-sm font-medium text-brand-ink truncate"><?= e(auth()['name']) ?></div>
                            <div class="text-xs text-brand-muted truncate"><?= e(auth()['email']) ?></div>
                        </div>
                        <?php if (has_role('admin', 'editor', 'seller')): ?>
                            <a href="<?= e(url('admin')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Painel Admin
                            </a>
                        <?php endif; ?>
                        <a href="<?= e(url('minha-conta')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h3m10-12l2 2m-2-2v10a2 2 0 01-2 2h-3m-6 0a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2m-6 0h6"/></svg>
                            Minha conta
                        </a>
                        <a href="<?= e(url('minha-conta/orcamentos')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                            Meus orçamentos
                        </a>
                        <a href="<?= e(url('minha-conta/enderecos')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Endereços
                        </a>
                        <div class="border-t border-brand-line mt-1 pt-1">
                            <form method="post" action="<?= e(url('logout')) ?>">
                                <?= csrf_field() ?>
                                <button class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-rose-50 text-rose-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>" class="text-brand-muted hover:text-brand-ink uppercase tracking-widest text-xs font-medium">Entrar</a>
            <?php endif; ?>

            <!-- Botão do carrinho (abre drawer) -->
            <button type="button" @click="cartOpen = true"
                    class="relative text-brand-ink hover:text-brand-blue transition" aria-label="Abrir orçamento">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 11V7a3 3 0 016 0v4m-9 8h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <span data-cart-badge
                      class="absolute -top-1.5 -right-1.5 bg-brand-blue text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center"
                      style="<?= $cartBadge > 0 ? '' : 'display: none;' ?>">
                    <?= e((string) max(0, $cartBadge)) ?>
                </span>
            </button>
        </div>
    </div>
</header>

<?php $this->partial('cart_drawer'); ?>
