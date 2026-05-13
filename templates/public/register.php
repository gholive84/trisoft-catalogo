<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-lg mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Criar conta</h1>
        <p class="text-sm text-gray-500 mb-6">Cadastre-se para solicitar orçamentos.</p>

        <form method="post" action="<?= e(url('cadastro')) ?>" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo *</label>
                <input type="text" name="name" required value="<?= e(old('name')) ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                <input type="email" name="email" required value="<?= e(old('email')) ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="tel" name="phone" value="<?= e(old('phone')) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ</label>
                    <input type="text" name="document" value="<?= e(old('document')) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Razão social / Empresa</label>
                <input type="text" name="company_name" value="<?= e(old('company_name')) ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha *</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha *</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                </div>
            </div>

            <label class="flex items-start gap-2 text-sm text-gray-600">
                <input type="checkbox" name="terms" value="1" required class="mt-0.5">
                <span>Concordo com os termos de uso.</span>
            </label>

            <button type="submit" class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800">
                Criar conta
            </button>
        </form>

        <div class="text-center mt-6 text-sm text-gray-600">
            Já tem conta?
            <a href="<?= e(url('login')) ?>" class="text-gray-900 font-medium hover:underline">Entrar</a>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
