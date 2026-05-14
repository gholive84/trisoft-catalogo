<?php
/**
 * Breadcrumb. Espera $items = [['url' => '...', 'name' => '...']].
 * Suporta $darkMode = true para usar em headers escuros.
 */
$items    = $items    ?? [];
$darkMode = $darkMode ?? false;
if ($items === []) return;
$linkColor = $darkMode ? 'text-white/70 hover:text-white' : 'text-gray-500 hover:text-brand-blue';
$activeColor = $darkMode ? 'text-white' : 'text-brand-ink';
$sepColor = $darkMode ? 'text-white/30' : 'text-gray-300';
?>
<nav aria-label="Breadcrumb" class="text-sm">
    <ol class="flex flex-wrap items-center gap-1.5">
        <li>
            <a href="<?= e(url('/')) ?>" class="<?= e($linkColor) ?> transition">Início</a>
        </li>
        <?php foreach ($items as $i => $item):
            $last = $i === array_key_last($items);
        ?>
            <li class="flex items-center gap-1.5">
                <svg class="w-3 h-3 <?= e($sepColor) ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M7 5l6 5-6 5V5z"/></svg>
                <?php if ($last): ?>
                    <span class="<?= e($activeColor) ?> font-medium"><?= e($item['name']) ?></span>
                <?php else: ?>
                    <a href="<?= e($item['url']) ?>" class="<?= e($linkColor) ?> transition"><?= e($item['name']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
