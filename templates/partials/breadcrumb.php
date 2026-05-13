<?php
/**
 * Breadcrumb. Espera $items = [['url' => '...', 'name' => '...']].
 * O último item não recebe link.
 */
$items = $items ?? [];
if ($items === []) return;
?>
<nav aria-label="Breadcrumb" class="text-sm text-gray-500">
    <ol class="flex flex-wrap items-center gap-1">
        <li>
            <a href="<?= e(url('/')) ?>" class="hover:text-gray-900">Início</a>
        </li>
        <?php foreach ($items as $i => $item):
            $last = $i === array_key_last($items);
        ?>
            <li class="flex items-center gap-1">
                <span class="text-gray-300">/</span>
                <?php if ($last): ?>
                    <span class="text-gray-900 font-medium"><?= e($item['name']) ?></span>
                <?php else: ?>
                    <a href="<?= e($item['url']) ?>" class="hover:text-gray-900"><?= e($item['name']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
