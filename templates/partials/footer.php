<footer class="bg-brand-blue-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
            <img src="<?= e(asset('images/logo.png')) ?>" alt="Trisoft" class="h-12 w-auto brightness-0 invert opacity-90 mb-4" style="max-width: 200px;">
            <p class="text-sm text-gray-400 max-w-md">
                Trisoft Revestimentos Funcionais. Soluções acústicas de alta performance para arquitetura, áudio profissional e ambientes corporativos.
            </p>
        </div>

        <div>
            <h4 class="text-white font-display font-semibold mb-3 text-sm uppercase tracking-wide">Catálogo</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="<?= e(url('/')) ?>" class="hover:text-white transition">Página inicial</a></li>
                <li><a href="<?= e(url('busca')) ?>" class="hover:text-white transition">Buscar produtos</a></li>
                <li><a href="<?= e(url('carrinho')) ?>" class="hover:text-white transition">Meu orçamento</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-display font-semibold mb-3 text-sm uppercase tracking-wide">Conta</h4>
            <ul class="space-y-2 text-sm">
                <?php if (auth()): ?>
                    <li><a href="<?= e(url('minha-conta')) ?>" class="hover:text-white transition">Minha conta</a></li>
                    <li><a href="<?= e(url('minha-conta/orcamentos')) ?>" class="hover:text-white transition">Meus orçamentos</a></li>
                <?php else: ?>
                    <li><a href="<?= e(url('login')) ?>" class="hover:text-white transition">Entrar</a></li>
                    <li><a href="<?= e(url('cadastro')) ?>" class="hover:text-white transition">Criar conta</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400">
            <div>© <?= date('Y') ?> Trisoft. Todos os direitos reservados.</div>
            <div>Catálogo de produtos · Sistema de orçamentos online</div>
        </div>
    </div>
</footer>
