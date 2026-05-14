<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$daysLabels = [7 => '7 dias', 14 => '14 dias', 30 => '30 dias', 90 => '90 dias'];
$funnel = $funnel ?? ['visitors' => 0, 'product_views' => 0, 'add_to_cart' => 0, 'quotes' => 0];

// Helpers de conversão
$pct = function (int $part, int $total): string {
    if ($total <= 0) return '—';
    return number_format(($part / $total) * 100, 1, ',', '.') . '%';
};
?>
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="font-display text-2xl font-semibold text-brand-ink">Analytics</h1>
        <p class="text-sm text-brand-muted mt-1">Visão dos últimos <?= e((string) $days) ?> dias</p>
    </div>
    <div class="flex gap-1.5">
        <?php foreach ($daysLabels as $d => $label): ?>
            <a href="<?= e(url('admin/analytics?days=' . $d)) ?>"
               class="<?= $days === $d ? 'bg-brand-ink text-white' : 'bg-white border border-brand-line text-brand-ink hover:border-brand-blue hover:text-brand-blue' ?> px-3 py-1.5 rounded-full text-xs font-medium transition">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-brand-line p-5">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <div class="text-xs text-brand-muted uppercase tracking-widest">Online agora</div>
        </div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-2"><?= e(number_format($activeNowCount, 0, ',', '.')) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">últimos 15 min</div>
    </div>
    <div class="bg-white rounded-2xl border border-brand-line p-5">
        <div class="text-xs text-brand-muted uppercase tracking-widest">Visitantes únicos</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-2"><?= e(number_format($visitors, 0, ',', '.')) ?></div>
    </div>
    <div class="bg-white rounded-2xl border border-brand-line p-5">
        <div class="text-xs text-brand-muted uppercase tracking-widest">Page views</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-2"><?= e(number_format($pageviews, 0, ',', '.')) ?></div>
        <?php if ($visitors > 0): ?>
            <div class="text-[11px] text-brand-muted mt-1">Média: <?= e(number_format($pageviews / $visitors, 1, ',', '.')) ?> /visitante</div>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-2xl border border-brand-line p-5">
        <div class="text-xs text-brand-muted uppercase tracking-widest">Clientes ativos</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-2"><?= e(number_format($loggedUsers, 0, ',', '.')) ?></div>
    </div>
    <div class="bg-white rounded-2xl border border-brand-line p-5">
        <div class="text-xs text-brand-muted uppercase tracking-widest">Carrinhos abandonados</div>
        <div class="font-display text-3xl font-semibold text-brand-ink mt-2"><?= e(number_format($abandonedCarts, 0, ',', '.')) ?></div>
        <div class="text-[11px] text-brand-muted mt-1">≥ 3 dias sem atividade</div>
    </div>
</div>

<!-- Online agora -->
<?php if (!empty($activeNow)): ?>
<div class="bg-white rounded-2xl border border-brand-line overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-brand-line flex items-center justify-between">
        <h2 class="font-display font-semibold text-brand-ink flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Online agora
        </h2>
        <span class="text-xs text-brand-muted"><?= count($activeNow) ?> sessão(ões) ativa(s) · últimos 15 min</span>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-widest text-brand-muted">
            <tr>
                <th class="px-6 py-3 text-left">Usuário</th>
                <th class="px-6 py-3 text-left">Última página</th>
                <th class="px-6 py-3 text-right w-20">Páginas</th>
                <th class="px-6 py-3 text-right w-32">Última atividade</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-brand-line">
            <?php foreach ($activeNow as $s):
                $isLogged = !empty($s['user_id']);
                $name = $isLogged ? $s['name'] : 'Visitante ' . substr($s['session_id'], 0, 6);
                $diff = time() - strtotime($s['last_seen']);
                $ago = $diff < 60 ? 'agora' : ($diff < 3600 ? floor($diff / 60) . ' min' : floor($diff / 3600) . 'h');
            ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= $isLogged ? 'bg-brand-blue/10 text-brand-blue' : 'bg-gray-100 text-gray-500' ?>">
                                <?= e(strtoupper(mb_substr($name, 0, 2))) ?>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-brand-ink truncate"><?= e($name) ?></div>
                                <?php if ($isLogged): ?>
                                    <div class="text-xs text-brand-muted truncate"><?= e($s['email']) ?> · <?= e($s['role']) ?></div>
                                <?php else: ?>
                                    <div class="text-xs text-brand-muted">anônimo</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-brand-muted text-xs font-mono truncate max-w-md">
                        <?= e($s['last_url'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-right text-brand-muted"><?= e((string) $s['pages']) ?></td>
                    <td class="px-6 py-3 text-right text-brand-muted text-xs"><?= e($ago) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Últimos visitantes -->
<?php if (!empty($recentVisitors)): ?>
<div class="bg-white rounded-2xl border border-brand-line overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-brand-line flex items-center justify-between">
        <h2 class="font-display font-semibold text-brand-ink">Últimos visitantes</h2>
        <span class="text-xs text-brand-muted">Mais recentes nos últimos <?= e((string) $days) ?> dias</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-widest text-brand-muted">
                <tr>
                    <th class="px-6 py-3 text-left">Usuário</th>
                    <th class="px-6 py-3 text-left">Última página</th>
                    <th class="px-6 py-3 text-left">IP</th>
                    <th class="px-6 py-3 text-right w-20">Páginas</th>
                    <th class="px-6 py-3 text-right w-32">Última visita</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-line">
                <?php foreach ($recentVisitors as $s):
                    $isLogged = !empty($s['user_id']);
                    $name = $isLogged ? $s['name'] : 'Visitante ' . substr($s['session_id'], 0, 6);
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 <?= $isLogged ? 'bg-brand-blue/10 text-brand-blue' : 'bg-gray-100 text-gray-500' ?>">
                                    <?= e(strtoupper(mb_substr($name, 0, 2))) ?>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-brand-ink truncate"><?= e($name) ?></div>
                                    <?php if ($isLogged): ?>
                                        <div class="text-[11px] text-brand-muted truncate"><?= e($s['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-brand-muted text-xs font-mono truncate max-w-xs">
                            <?= e($s['last_url'] ?? '—') ?>
                        </td>
                        <td class="px-6 py-3 text-brand-muted text-xs font-mono">
                            <?= e($s['ip_address'] ?? '—') ?>
                        </td>
                        <td class="px-6 py-3 text-right text-brand-muted"><?= e((string) $s['pages']) ?></td>
                        <td class="px-6 py-3 text-right text-brand-muted text-xs"><?= e(date_br($s['last_seen'], 'd/m H:i')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Gráfico de tráfego -->
<div class="bg-white rounded-2xl border border-brand-line p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display font-semibold text-brand-ink">Tráfego diário</h2>
        <div class="flex items-center gap-4 text-xs text-brand-muted">
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-brand-blue"></span>Page views</div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-brand-green"></span>Sessões</div>
        </div>
    </div>
    <canvas id="trafficChart" height="80"></canvas>
</div>

<!-- Funil -->
<div class="bg-white rounded-2xl border border-brand-line p-6 mb-8">
    <h2 class="font-display font-semibold text-brand-ink mb-5">Funil de conversão</h2>
    <?php
    $steps = [
        ['Visitantes',           $funnel['visitors'],      'bg-brand-blue/10 text-brand-blue'],
        ['Viu produtos',         $funnel['product_views'], 'bg-brand-teal/10 text-brand-teal'],
        ['Adicionou ao carrinho',$funnel['add_to_cart'],   'bg-brand-green/10 text-brand-green-dark'],
        ['Solicitou orçamento',  $funnel['quotes'],        'bg-amber-50 text-amber-700'],
    ];
    $stepValues = array_map(fn ($s) => $s[1], $steps);
    $stepValues[] = 1;
    $maxValue = max($stepValues);
    ?>
    <div class="space-y-3">
        <?php foreach ($steps as $i => [$label, $value, $color]):
            $width = $maxValue > 0 ? max(2, round(($value / $maxValue) * 100)) : 0;
            $prevValue = $i > 0 ? $steps[$i - 1][1] : 0;
            $conv = $i > 0 && $prevValue > 0 ? round(($value / $prevValue) * 100, 1) : null;
        ?>
            <div class="flex items-center gap-3">
                <div class="w-40 text-xs uppercase tracking-widest text-brand-muted shrink-0"><?= e($label) ?></div>
                <div class="flex-1 relative h-9 bg-gray-50 rounded-lg overflow-hidden">
                    <div class="absolute inset-y-0 left-0 <?= e($color) ?> rounded-lg flex items-center justify-end px-3 transition-all"
                         style="width: <?= $width ?>%">
                        <span class="text-sm font-semibold"><?= e(number_format($value, 0, ',', '.')) ?></span>
                    </div>
                </div>
                <div class="w-20 text-right text-xs text-brand-muted shrink-0">
                    <?php if ($conv !== null): ?>
                        <?= e(number_format($conv, 1, ',', '.')) ?>%
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabelas: produtos, categorias, buscas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-brand-line overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-line">
            <h2 class="font-display font-semibold text-brand-ink">Top produtos visualizados</h2>
        </div>
        <?php if (empty($topProducts)): ?>
            <div class="p-6 text-sm text-brand-muted text-center">Sem dados ainda.</div>
        <?php else: ?>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-brand-line">
                    <?php foreach ($topProducts as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <a href="<?= e(url('produto/' . $p['slug'])) ?>" target="_blank" class="text-brand-ink hover:text-brand-blue truncate block">
                                    <?= e($p['name']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-3 text-right font-medium text-brand-ink w-20"><?= e(number_format($p['views'], 0, ',', '.')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl border border-brand-line overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-line">
            <h2 class="font-display font-semibold text-brand-ink">Top categorias</h2>
        </div>
        <?php if (empty($topCategories)): ?>
            <div class="p-6 text-sm text-brand-muted text-center">Sem dados ainda.</div>
        <?php else: ?>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-brand-line">
                    <?php foreach ($topCategories as $c): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <a href="<?= e(url('categoria/' . $c['slug'])) ?>" target="_blank" class="text-brand-ink hover:text-brand-blue truncate block">
                                    <?= e($c['name']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-3 text-right font-medium text-brand-ink w-20"><?= e(number_format($c['views'], 0, ',', '.')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl border border-brand-line overflow-hidden lg:col-span-2">
        <div class="px-6 py-4 border-b border-brand-line flex items-center justify-between">
            <h2 class="font-display font-semibold text-brand-ink">Top buscas</h2>
            <span class="text-xs text-brand-muted">Buscas sem resultado merecem atenção</span>
        </div>
        <?php if (empty($topSearches)): ?>
            <div class="p-6 text-sm text-brand-muted text-center">Sem buscas ainda.</div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-widest text-brand-muted">
                    <tr>
                        <th class="px-6 py-3 text-left">Termo</th>
                        <th class="px-6 py-3 text-right w-20">Buscas</th>
                        <th class="px-6 py-3 text-center w-32">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-line">
                    <?php foreach ($topSearches as $s):
                        $zero = !empty($s['zero_results']);
                    ?>
                        <tr class="hover:bg-gray-50 <?= $zero ? 'bg-rose-50/30' : '' ?>">
                            <td class="px-6 py-3 font-mono text-xs"><?= e((string) $s['query']) ?></td>
                            <td class="px-6 py-3 text-right font-medium"><?= e(number_format($s['count'], 0, ',', '.')) ?></td>
                            <td class="px-6 py-3 text-center">
                                <?php if ($zero): ?>
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700">Sem resultado</span>
                                <?php else: ?>
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">Encontrou</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const data = <?= json_encode(array_values($dailyTraffic), JSON_NUMERIC_CHECK) ?>;
    const ctx = document.getElementById('trafficChart');
    if (!ctx || !data.length) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => new Date(d.date + 'T00:00:00').toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })),
            datasets: [
                {
                    label: 'Page views',
                    data: data.map(d => d.pageviews),
                    borderColor: '#2962FF',
                    backgroundColor: 'rgba(41, 98, 255, 0.08)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                },
                {
                    label: 'Sessões',
                    data: data.map(d => d.sessions),
                    borderColor: '#8BC750',
                    backgroundColor: 'transparent',
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    borderDash: [4, 4],
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6b6b6b', font: { size: 11 } } },
                y: { beginAtZero: true, grid: { color: '#E5E5E5' }, ticks: { color: '#6b6b6b', font: { size: 11 }, precision: 0 } },
            },
        },
    });
})();
</script>
<?php $this->endSection(); ?>
