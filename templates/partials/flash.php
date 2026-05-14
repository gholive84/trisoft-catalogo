<?php
$success = flash('success');
$error   = flash('error');
$info    = flash('info');
if (!$success && !$error && !$info) return;
?>
<div class="max-w-7xl mx-auto px-6 lg:px-10 pt-4 space-y-2">
    <?php if ($success): ?>
        <div x-data="{show: true}" x-show="show" x-transition.opacity
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="flex-1"><?= e($success) ?></span>
            <button @click="show=false" class="text-current/60 hover:text-current">×</button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div x-data="{show: true}" x-show="show" x-transition.opacity
             class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="flex-1"><?= e($error) ?></span>
            <button @click="show=false" class="text-current/60 hover:text-current">×</button>
        </div>
    <?php endif; ?>
    <?php if ($info): ?>
        <div x-data="{show: true}" x-show="show" x-transition.opacity
             class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-2xl text-sm flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span class="flex-1"><?= e($info) ?></span>
            <button @click="show=false" class="text-current/60 hover:text-current">×</button>
        </div>
    <?php endif; ?>
</div>
