<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-10 lg:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <!-- Form -->
        <div class="max-w-md mx-auto w-full lg:mx-0">
            <div class="mb-8">
                <h1 class="font-display text-3xl md:text-4xl font-bold text-brand-ink">Bem-vindo de volta</h1>
                <p class="text-gray-600 mt-2">Entre para solicitar orçamentos e acompanhar pedidos.</p>
            </div>

            <form method="post" action="<?= e(url('login')) ?>" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
                    <input
                        type="email" name="email" required autofocus autocomplete="email"
                        value="<?= e(old('email')) ?>"
                        placeholder="seu@email.com"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition placeholder:text-gray-400">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Senha</label>
                        <a href="#" class="text-xs text-brand-blue hover:underline">Esqueceu a senha?</a>
                    </div>
                    <input
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition placeholder:text-gray-400">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center bg-brand-blue text-white font-semibold py-3.5 rounded-xl hover:bg-brand-blue-dark shadow-brand transition group">
                    Entrar
                    <svg class="w-4 h-4 ml-2 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-600">
                Não tem conta?
                <a href="<?= e(url('cadastro')) ?>" class="font-semibold text-brand-blue hover:underline">Criar minha conta</a>
            </div>
        </div>

        <!-- Hero / Branding side -->
        <div class="hidden lg:block relative">
            <div class="relative bg-brand-radial rounded-3xl p-10 overflow-hidden text-white aspect-[4/5] flex flex-col justify-between shadow-brand">
                <!-- decoração -->
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-brand-green/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-brand-teal/30 rounded-full blur-3xl"></div>

                <div class="relative">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 backdrop-blur text-xs font-medium uppercase tracking-wider">
                        Catálogo Trisoft
                    </div>
                    <h2 class="font-display text-4xl font-bold mt-6 leading-tight">
                        Revestimentos<br>funcionais<br>
                        <span class="text-brand-green">de alta performance</span>
                    </h2>
                </div>

                <div class="relative">
                    <p class="text-white/80 max-w-sm">
                        Tratamento acústico, painéis decorativos e soluções para estúdios profissionais. Solicite seu orçamento personalizado.
                    </p>
                    <div class="flex items-center gap-3 mt-6">
                        <div class="flex -space-x-2">
                            <div class="w-9 h-9 rounded-full bg-brand-green border-2 border-brand-blue-900"></div>
                            <div class="w-9 h-9 rounded-full bg-brand-teal border-2 border-brand-blue-900"></div>
                            <div class="w-9 h-9 rounded-full bg-brand-blue border-2 border-brand-blue-900"></div>
                        </div>
                        <div class="text-sm text-white/80">+30 anos de mercado</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
