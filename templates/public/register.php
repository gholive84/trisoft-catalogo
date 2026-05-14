<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <?php $this->partial('head', ['title' => 'Criar conta']); ?>
</head>
<body class="bg-brand-ink text-brand-ink min-h-screen relative overflow-hidden"
      x-data="{ cartOpen: false }">

<div class="absolute inset-0 z-0">
    <img src="<?= e(upload_url('products/baffle-classic-wave-solid-high-relief.jpg')) ?>"
         alt=""
         class="w-full h-full object-cover"
         onerror="this.style.display='none'">
    <div class="absolute inset-0 bg-gradient-to-br from-black/75 via-black/55 to-black/85"></div>
</div>

<header class="relative z-10">
    <div class="max-w-content mx-auto px-6 lg:px-10 h-20 flex items-center">
        <a href="<?= e(url('/')) ?>" class="flex items-center">
            <img src="<?= e(asset('images/logo.png')) ?>" alt="Trisoft" class="h-10 w-auto brightness-0 invert" style="max-width: 170px;">
        </a>
        <div class="ml-auto">
            <a href="<?= e(url('/')) ?>" class="text-white/80 hover:text-white text-xs uppercase tracking-widest font-medium">
                ← Voltar ao catálogo
            </a>
        </div>
    </div>
</header>

<?php $this->partial('flash'); ?>

<main class="relative z-10 flex items-center justify-center min-h-[calc(100vh-80px)] px-6 py-10">
    <div class="w-full max-w-lg">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 border border-white/40">
            <div class="text-center mb-7">
                <h1 class="display text-3xl md:text-4xl text-brand-ink">Criar conta</h1>
                <p class="text-brand-muted mt-2 text-sm">Acesso gratuito ao sistema de orçamentos</p>
            </div>

            <form method="post" action="<?= e(url('cadastro')) ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Nome completo *</label>
                    <input type="text" name="name" required value="<?= e(old('name')) ?>" autocomplete="name"
                           class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">E-mail *</label>
                    <input type="email" name="email" required value="<?= e(old('email')) ?>" autocomplete="email"
                           class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Telefone</label>
                        <input type="tel" name="phone" value="<?= e(old('phone')) ?>" placeholder="(11) 99999-9999"
                               class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">CPF / CNPJ</label>
                        <input type="text" name="document" value="<?= e(old('document')) ?>"
                               class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Empresa</label>
                    <input type="text" name="company_name" value="<?= e(old('company_name')) ?>"
                           class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Senha *</label>
                        <input type="password" name="password" required minlength="8" autocomplete="new-password"
                               class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Confirmar *</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full bg-white border border-brand-line rounded-xl px-4 py-3 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                    </div>
                </div>

                <label class="flex items-start gap-2.5 text-sm text-brand-muted cursor-pointer pt-1">
                    <input type="checkbox" name="terms" value="1" required
                           class="mt-0.5 rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                    <span>Concordo com os <a href="#" class="text-brand-ink hover:underline">termos de uso</a></span>
                </label>

                <button type="submit" class="w-full bg-brand-blue text-white font-semibold py-3.5 rounded-full hover:bg-brand-blue-dark transition mt-3">
                    Criar minha conta
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-brand-muted">
                Já tem conta?
                <a href="<?= e(url('login')) ?>" class="font-semibold text-brand-ink hover:underline ml-1">Entrar</a>
            </div>
        </div>
    </div>
</main>

</body>
</html>
