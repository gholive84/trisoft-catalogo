<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-10 lg:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <!-- Hero / Branding side (esquerda) -->
        <div class="hidden lg:block relative order-2 lg:order-1">
            <div class="relative bg-brand-radial rounded-3xl p-10 overflow-hidden text-white aspect-[4/5] flex flex-col justify-between shadow-brand">
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-brand-green/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-brand-blue/30 rounded-full blur-3xl"></div>

                <div class="relative">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 backdrop-blur text-xs font-medium uppercase tracking-wider">
                        Cadastro gratuito
                    </div>
                    <h2 class="font-display text-4xl font-bold mt-6 leading-tight">
                        Crie sua conta e<br>
                        <span class="text-brand-green">economize tempo</span><br>
                        em cada projeto.
                    </h2>
                </div>

                <ul class="relative space-y-3 text-white/90">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand-green/30 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-brand-green" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                        Orçamentos com preços personalizados
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand-green/30 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-brand-green" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                        Histórico de pedidos no painel
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand-green/30 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-brand-green" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                        Atendimento direto com nossa equipe
                    </li>
                </ul>
            </div>
        </div>

        <!-- Form (direita) -->
        <div class="max-w-md mx-auto w-full lg:mx-0 order-1 lg:order-2">
            <div class="mb-8">
                <h1 class="font-display text-3xl md:text-4xl font-bold text-brand-ink">Criar conta</h1>
                <p class="text-gray-600 mt-2">Leva menos de 1 minuto e é gratuito.</p>
            </div>

            <form method="post" action="<?= e(url('cadastro')) ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo *</label>
                    <input type="text" name="name" required value="<?= e(old('name')) ?>" autocomplete="name"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail *</label>
                    <input type="email" name="email" required value="<?= e(old('email')) ?>" autocomplete="email"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefone</label>
                        <input type="tel" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" placeholder="(11) 99999-9999"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">CPF / CNPJ</label>
                        <input type="text" name="document" value="<?= e(old('document')) ?>"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Empresa</label>
                    <input type="text" name="company_name" value="<?= e(old('company_name')) ?>" autocomplete="organization"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Senha *</label>
                        <input type="password" name="password" required minlength="8" autocomplete="new-password"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar *</label>
                        <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition">
                    </div>
                </div>

                <label class="flex items-start gap-2.5 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="terms" value="1" required
                           class="mt-0.5 rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                    <span>Concordo com os <a href="#" class="text-brand-blue hover:underline">termos de uso</a>.</span>
                </label>

                <button type="submit" class="w-full bg-brand-blue text-white font-semibold py-3.5 rounded-xl hover:bg-brand-blue-dark shadow-brand transition">
                    Criar minha conta
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Já tem conta?
                <a href="<?= e(url('login')) ?>" class="font-semibold text-brand-blue hover:underline">Entrar</a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
