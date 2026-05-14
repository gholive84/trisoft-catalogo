<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => 'Entrar']); ?>
</head>
<body class="bg-brand-ink text-brand-ink min-h-screen relative overflow-hidden"
      x-data="{ cartOpen: false }">

<!-- BG image (foto clean do catálogo) -->
<div class="absolute inset-0 z-0">
    <img src="<?= e(upload_url('products/baffle-classic-wave-solid.jpg')) ?>"
         alt=""
         class="w-full h-full object-cover"
         onerror="this.style.display='none'">
    <div class="absolute inset-0 bg-gradient-to-br from-white/85 via-white/55 to-white/75"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-brand-cream/40 via-transparent to-transparent"></div>
</div>

<!-- Header minimal -->
<header class="relative z-10">
    <div class="max-w-content mx-auto px-6 lg:px-10 h-20 flex items-center">
        <a href="<?= e(url('/')) ?>" class="flex items-center">
            <img src="<?= e(asset('images/logo.png')) ?>" alt="Trisoft" class="h-10 w-auto" style="max-width: 170px;">
        </a>
        <div class="ml-auto">
            <a href="<?= e(url('/')) ?>" class="text-brand-ink hover:text-brand-blue text-xs uppercase tracking-widest font-medium">
                ← Voltar ao catálogo
            </a>
        </div>
    </div>
</header>

<?php $this->partial('flash'); ?>

<main class="relative z-10 flex items-center justify-center min-h-[calc(100vh-80px)] px-6 py-10">
    <div class="w-full max-w-md">

        <!-- Caixa de login glassmorphism -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 border border-white/40">
            <div class="text-center mb-8">
                <h1 class="display text-3xl md:text-4xl text-brand-ink">Bem-vindo</h1>
                <p class="text-brand-muted mt-2 text-sm">Acesse sua conta para solicitar orçamentos</p>
            </div>

            <form method="post" action="<?= e(url('login')) ?>" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">E-mail</label>
                    <input
                        type="email" name="email" required autofocus autocomplete="email"
                        value="<?= e(old('email')) ?>"
                        placeholder="seu@email.com"
                        class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition placeholder:text-gray-400">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium">Senha</label>
                        <a href="#" class="text-xs text-brand-muted hover:text-brand-ink">Esqueceu?</a>
                    </div>
                    <input
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition placeholder:text-gray-400">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 bg-brand-blue text-white font-semibold py-3.5 rounded-full hover:bg-brand-blue-dark transition group">
                    Entrar
                    <svg class="w-4 h-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <div class="relative flex items-center my-6">
                <div class="flex-1 h-px bg-brand-line"></div>
                <span class="px-3 text-xs text-brand-muted uppercase tracking-widest">ou</span>
                <div class="flex-1 h-px bg-brand-line"></div>
            </div>

            <a href="<?= e(url('cadastro')) ?>"
               class="block text-center bg-white border-2 border-brand-ink text-brand-ink font-semibold py-3 rounded-full hover:bg-brand-ink hover:text-white transition">
                Criar nova conta
            </a>
        </div>

        <div class="text-center mt-8 text-brand-muted text-xs">
            <p class="uppercase tracking-widest">Trisoft Revestimentos Funcionais</p>
            <p class="mt-1">Soluções acústicas sustentáveis · 60+ anos de mercado</p>
        </div>
    </div>
</main>

</body>
</html>
