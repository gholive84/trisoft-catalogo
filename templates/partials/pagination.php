<?php
/**
 * Paginação simples. Espera:
 *   $pagination = ['page','perPage','lastPage','total'], $baseUrl, $query (array para preservar)
 */
$page    = (int) ($pagination['page'] ?? 1);
$last    = (int) ($pagination['lastPage'] ?? 1);
$baseUrl = $baseUrl ?? current_url();
$query   = $query   ?? [];

if ($last <= 1) return;

$buildUrl = function (int $p) use ($baseUrl, $query): string {
    $q = $query;
    $q['page'] = $p;
    $qs = http_build_query(array_filter($q, fn ($v) => $v !== null && $v !== ''));
    $clean = preg_replace('/\?.*$/', '', $baseUrl);
    return $clean . '?' . $qs;
};

$window = 2;
$pages = [];
for ($i = max(1, $page - $window); $i <= min($last, $page + $window); $i++) {
    $pages[] = $i;
}
if ($pages !== [] && $pages[0] !== 1)        array_unshift($pages, 1, 'gap');
if ($pages !== [] && end($pages) !== $last)  array_push($pages, 'gap', $last);
$pages = array_values(array_unique($pages, SORT_REGULAR));
?>
<nav class="flex items-center justify-center gap-1.5 mt-10 text-sm">
    <?php if ($page > 1): ?>
        <a href="<?= e($buildUrl($page - 1)) ?>" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:border-brand-blue hover:text-brand-blue transition">‹ Anterior</a>
    <?php endif; ?>

    <?php foreach ($pages as $p): ?>
        <?php if ($p === 'gap'): ?>
            <span class="px-2 text-gray-400">…</span>
        <?php elseif ((int) $p === $page): ?>
            <span class="px-4 py-2 rounded-lg bg-brand-blue text-white font-semibold shadow-soft"><?= (int) $p ?></span>
        <?php else: ?>
            <a href="<?= e($buildUrl((int) $p)) ?>" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:border-brand-blue hover:text-brand-blue transition"><?= (int) $p ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($page < $last): ?>
        <a href="<?= e($buildUrl($page + 1)) ?>" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:border-brand-blue hover:text-brand-blue transition">Próxima ›</a>
    <?php endif; ?>
</nav>
