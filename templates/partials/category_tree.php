<?php
/**
 * Renderiza uma árvore de categorias recursivamente.
 * Espera $nodes (array com 'children') e opcionalmente $activeId.
 */
$nodes    = $nodes    ?? [];
$activeId = $activeId ?? null;

if ($nodes === []) return;

$renderNode = function (array $node, int $depth = 0) use (&$renderNode, $activeId): void {
    $isActive = $activeId === (int) $node['id'];
    $href = url('categoria/' . $node['slug']);
    $padding = $depth * 12;
?>
    <li>
        <a href="<?= e($href) ?>"
           style="padding-left: <?= 8 + $padding ?>px"
           class="block py-1.5 pr-2 text-sm rounded hover:bg-gray-100 <?= $isActive ? 'text-gray-900 font-semibold bg-gray-100' : 'text-gray-700' ?>">
            <?= e($node['name']) ?>
        </a>
        <?php if (!empty($node['children'])): ?>
            <ul>
                <?php foreach ($node['children'] as $child) $renderNode($child, $depth + 1); ?>
            </ul>
        <?php endif; ?>
    </li>
<?php
};
?>
<ul class="text-sm">
    <?php foreach ($nodes as $node) $renderNode($node, 0); ?>
</ul>
