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
        <div class="flex items-center gap-5 text-sm">
            <?php if (auth()): ?>
                <?php if (has_role('admin', 'editor', 'seller')): ?>
                    <a href="<?= e(url('admin')) ?>" class="hidden md:inline text-brand-muted hover:text-brand-ink uppercase tracking-widest text-xs font-medium">Admin</a>
                <?php endif; ?>
                <form method="post" action="<?= e(url('logout')) ?>" class="hidden md:block">
                    <?= csrf_field() ?>
                    <button class="text-brand-muted hover:text-brand-ink uppercase tracking-widest text-xs font-medium">Sair</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>" class="text-brand-muted hover:text-brand-ink uppercase tracking-widest text-xs font-medium">Entrar</a>
            <?php endif; ?>

            <!-- Botão do carrinho (abre drawer) -->
            <button type="button" @click="cartOpen = true"
                    class="relative text-brand-ink hover:text-brand-blue transition" aria-label="Abrir orçamento">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 11V7a3 3 0 016 0v4m-9 8h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <?php if ($cartBadge > 0): ?>
                    <span class="absolute -top-1.5 -right-1.5 bg-brand-blue text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center">
                        <?= e((string) $cartBadge) ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</header>

<?php $this->partial('cart_drawer'); ?>
