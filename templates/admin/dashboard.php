<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$statusLabels = [
    'quote_requested' => 'Solicitado',
    'quoted'          => 'Respondido',
    'approved'        => 'Aprovado',
    'rejected'        => 'Rejeitado',
];
?>
<div class="flex items-end justify-between mb-8">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Dashboard</h1>
        <p class="text-sm text-brand-muted mt-1">Visão geral · Olá, <strong class="text-brand-ink"><?= e(auth()['name']) ?></strong></p>
    </div>
</div>

<!-- Cards de KPI (clicáveis) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

    <!-- Online agora -->
    <a href="<?= e(url('admin/analytics')) ?>"
       class="group bg-white rounded-2xl border border-brand-line p-5 hover:border-brand-blue/40 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-blue group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-brand-muted uppercase tracking-widest mt-4">Online agora</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-1"><?= e((string) ($kpis['active_now'] ?? 0)) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">Ver analytics →</div>
    </a>

    <!-- Orçamentos pendentes -->
    <a href="<?= e(url('admin/orcamentos?status=quote_requested')) ?>"
       class="group bg-white rounded-2xl border border-brand-line p-5 hover:border-amber-300 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-amber-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-brand-muted uppercase tracking-widest mt-4">Aguardando resposta</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-1"><?= e((string) ($kpis['pending_quotes'] ?? 0)) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">Responder agora →</div>
    </a>

    <!-- Novos clientes -->
    <a href="<?= e(url('admin/usuarios?role=customer')) ?>"
       class="group bg-white rounded-2xl border border-brand-line p-5 hover:border-brand-blue/40 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-2a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-blue group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-brand-muted uppercase tracking-widest mt-4">Novos clientes (30d)</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-1"><?= e((string) ($kpis['new_customers_30d'] ?? 0)) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">Ver clientes →</div>
    </a>

    <!-- Produtos ativos -->
    <a href="<?= e(url('admin/produtos?status=active')) ?>"
       class="group bg-white rounded-2xl border border-brand-line p-5 hover:border-brand-green/40 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-brand-green/10 text-brand-green-dark flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-green-dark group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-brand-muted uppercase tracking-widest mt-4">Produtos ativos</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-1"><?= e((string) ($kpis['active_products'] ?? 0)) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">Ver catálogo →</div>
    </a>

    <!-- Carrinhos abandonados -->
    <a href="<?= e(url('admin/analytics?days=30')) ?>"
       class="group bg-white rounded-2xl border border-brand-line p-5 hover:border-rose-300 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-rose-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="text-xs text-brand-muted uppercase tracking-widest mt-4">Carrinhos abandonados</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-1"><?= e((string) ($kpis['abandoned_carts'] ?? 0)) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">≥ 3 dias sem atividade</div>
    </a>
</div>

<!-- Layout em 2 colunas: orçamentos pendentes + novos clientes -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Orçamentos pendentes (2 colunas) -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-brand-line overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-line flex items-center justify-between">
            <h2 class="font-display font-semibold text-brand-ink">Aguardando resposta</h2>
            <a href="<?= e(url('admin/orcamentos?status=quote_requested')) ?>" class="text-xs text-brand-blue hover:underline">
                Ver todos →
            </a>
        </div>
        <?php if (empty($pendingOrders)): ?>
            <div class="px-6 py-12 text-center text-brand-muted text-sm">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Nenhum orçamento aguardando resposta.
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-brand-line">
                    <?php foreach ($pendingOrders as $o): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 w-32">
                                <a href="<?= e(url('admin/orcamentos/' . $o['id'])) ?>" class="font-mono text-sm font-medium text-brand-blue hover:underline">
                                    <?= e($o['order_number']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-brand-ink"><?= e($o['customer_name']) ?></div>
                                <div class="text-xs text-brand-muted"><?= e(date_br($o['created_at'], 'd/m/Y H:i')) ?></div>
                            </td>
                            <td class="px-6 py-3 text-right w-32">
                                <a href="<?= e(url('admin/orcamentos/' . $o['id'])) ?>" class="inline-flex items-center text-xs bg-amber-100 hover:bg-amber-200 text-amber-800 px-3 py-1.5 rounded-full font-medium transition">
                                    Responder
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Novos clientes -->
    <div class="bg-white rounded-2xl border border-brand-line overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-line flex items-center justify-between">
            <h2 class="font-display font-semibold text-brand-ink">Novos clientes</h2>
            <a href="<?= e(url('admin/usuarios?role=customer')) ?>" class="text-xs text-brand-blue hover:underline">
                Ver todos →
            </a>
        </div>
        <?php if (empty($newCustomers)): ?>
            <div class="px-6 py-12 text-center text-brand-muted text-sm">Sem cadastros ainda.</div>
        <?php else: ?>
            <ul class="divide-y divide-brand-line">
                <?php foreach ($newCustomers as $u): ?>
                    <li class="px-6 py-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-brand-blue/10 text-brand-blue flex items-center justify-center text-xs font-bold shrink-0">
                            <?= e(strtoupper(mb_substr($u['name'], 0, 2))) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?= e(url('admin/usuarios/' . $u['id'] . '/editar')) ?>" class="text-sm font-medium text-brand-ink hover:text-brand-blue truncate block">
                                <?= e($u['name']) ?>
                            </a>
                            <div class="text-xs text-brand-muted truncate"><?= e($u['email']) ?></div>
                            <div class="text-[10px] text-brand-muted"><?= e(date_br($u['created_at'])) ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Atalhos -->
<div class="bg-white rounded-2xl border border-brand-line p-6">
    <h2 class="font-display font-semibold text-brand-ink mb-3">Atalhos rápidos</h2>
    <div class="flex flex-wrap gap-2">
        <a href="<?= e(url('admin/produtos/novo')) ?>" class="px-4 py-2.5 text-sm bg-brand-ink text-white rounded-full hover:bg-black transition inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo produto
        </a>
        <a href="<?= e(url('admin/categorias/nova')) ?>" class="px-4 py-2.5 text-sm bg-gray-100 text-brand-ink rounded-full hover:bg-gray-200 transition inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nova categoria
        </a>
        <a href="<?= e(url('admin/orcamentos')) ?>" class="px-4 py-2.5 text-sm bg-gray-100 text-brand-ink rounded-full hover:bg-gray-200 transition">
            Todos os orçamentos
        </a>
        <a href="<?= e(url('admin/analytics')) ?>" class="px-4 py-2.5 text-sm bg-gray-100 text-brand-ink rounded-full hover:bg-gray-200 transition">
            Analytics
        </a>
    </div>
</div>
<?php $this->endSection(); ?>
