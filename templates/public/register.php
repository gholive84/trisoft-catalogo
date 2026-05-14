<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-md mx-auto px-6 lg:px-10 py-16 lg:py-20">
    <div class="text-center mb-10">
        <h1 class="display text-4xl md:text-5xl text-brand-ink">Criar conta</h1>
        <p class="text-brand-muted mt-3">Leva menos de 1 minuto</p>
    </div>

    <form method="post" action="<?= e(url('cadastro')) ?>" class="space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Nome completo *</label>
            <input type="text" name="name" required value="<?= e(old('name')) ?>" autocomplete="name"
                class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
        </div>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">E-mail *</label>
            <input type="email" name="email" required value="<?= e(old('email')) ?>" autocomplete="email"
                class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Telefone</label>
                <input type="tel" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" placeholder="(11) 99999-9999"
                    class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base placeholder:text-gray-300">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">CPF / CNPJ</label>
                <input type="text" name="document" value="<?= e(old('document')) ?>"
                    class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
            </div>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Empresa</label>
            <input type="text" name="company_name" value="<?= e(old('company_name')) ?>" autocomplete="organization"
                class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Senha *</label>
                <input type="password" name="password" required minlength="8" autocomplete="new-password"
                    class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Confirmar *</label>
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                    class="w-full bg-white border-b border-brand-line px-0 py-3 focus:border-brand-ink focus:ring-0 focus:outline-none transition text-base">
            </div>
        </div>

        <label class="flex items-start gap-2.5 text-sm text-brand-muted cursor-pointer pt-2">
            <input type="checkbox" name="terms" value="1" required
                   class="mt-0.5 rounded border-gray-300 text-brand-ink focus:ring-brand-ink">
            <span>Concordo com os <a href="#" class="text-brand-ink hover:underline">termos de uso</a></span>
        </label>

        <button type="submit" class="w-full bg-brand-ink text-white py-4 rounded-full font-medium hover:bg-black transition mt-4">
            Criar minha conta
        </button>
    </form>

    <div class="text-center mt-8 text-sm text-brand-muted">
        Já tem conta?
        <a href="<?= e(url('login')) ?>" class="font-medium text-brand-ink hover:underline ml-1">Entrar</a>
    </div>
</div>
<?php $this->endSection(); ?>
