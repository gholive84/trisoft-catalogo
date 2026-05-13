<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Entrar</h1>
        <p class="text-sm text-gray-500 mb-6">Acesse para solicitar orçamentos.</p>

        <form method="post" action="<?= e(url('login')) ?>" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input
                    type="email" name="email" required autofocus
                    value="<?= e(old('email')) ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input
                    type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
            </div>

            <button type="submit"
                class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800">
                Entrar
            </button>
        </form>

        <div class="text-center mt-6 text-sm text-gray-600">
            Ainda não tem conta?
            <a href="<?= e(url('cadastro')) ?>" class="text-gray-900 font-medium hover:underline">Criar conta</a>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
