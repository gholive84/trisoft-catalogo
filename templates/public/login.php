<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-md mx-auto px-6 lg:px-10 py-16 lg:py-24">
    <div class="text-center mb-10">
        <h1 class="display text-4xl md:text-5xl text-brand-ink">Entrar</h1>
        <p class="text-brand-muted mt-3">Acesse sua conta para solicitar orçamentos</p>
    </div>

    <form method="post" action="<?= e(url('login')) ?>" class="space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">E-mail</label>
            <input
                type="email" name="email" required autofocus autocomplete="email"
                value="<?= e(old('email')) ?>"
                class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium">Senha</label>
                <a href="#" class="text-xs text-brand-muted hover:text-brand-ink">Esqueceu?</a>
            </div>
            <input
                type="password" name="password" required autocomplete="current-password"
                class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
        </div>

        <button type="submit"
            class="w-full bg-brand-ink text-white py-4 rounded-full font-medium hover:bg-black transition mt-8">
            Entrar
        </button>
    </form>

    <div class="text-center mt-10 text-sm text-brand-muted">
        Não tem conta?
        <a href="<?= e(url('cadastro')) ?>" class="font-medium text-brand-ink hover:underline ml-1">Criar conta</a>
    </div>
</div>
<?php $this->endSection(); ?>
