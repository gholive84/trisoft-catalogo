<?php
$success = flash('success');
$error   = flash('error');
$info    = flash('info');
?>
<?php if ($success || $error || $info): ?>
<div class="max-w-7xl mx-auto px-4 pt-4">
    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-2 text-sm">
            <?= e($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-2 text-sm">
            <?= e($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($info): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-2 text-sm">
            <?= e($info) ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
