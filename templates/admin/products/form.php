<?php /** @var App\Core\View $this */ $this->extend('layouts/admin'); ?>

<?php $this->section('content'); ?>
<?php
$action = $isNew
    ? url('admin/produtos')
    : url('admin/produtos/' . $product['id']);

$specsJson = '';
if (!empty($product['specifications'])) {
    $specsJson = is_string($product['specifications'])
        ? $product['specifications']
        : json_encode($product['specifications'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= e(url('admin/produtos')) ?>" class="text-xs text-brand-muted hover:text-brand-ink">← Produtos</a>
        <h1 class="font-display text-2xl font-semibold text-brand-ink mt-1">
            <?= $isNew ? 'Novo produto' : e($product['name']) ?>
        </h1>
        <?php if (!$isNew && !empty($product['sku'])): ?>
            <div class="text-xs text-brand-muted mt-1 font-mono uppercase tracking-wider">SKU <?= e($product['sku']) ?></div>
        <?php endif; ?>
    </div>
    <?php if (!$isNew): ?>
        <form method="post" action="<?= e(url('admin/produtos/' . $product['id'] . '/excluir')) ?>"
              onsubmit="return confirm('Confirma a exclusão deste produto?');">
            <?= csrf_field() ?>
            <button class="text-sm text-rose-600 hover:underline">Excluir produto</button>
        </form>
    <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
    <?= csrf_field() ?>
    <?php if (!$isNew): ?><?= method_field('PUT') ?><?php endif; ?>

    <!-- COLUNA ESQUERDA: dados -->
    <div class="space-y-6">
        <!-- Básico -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">Informações básicas</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Nome *</label>
                    <input type="text" name="name" required value="<?= e(old('name', $product['name'])) ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Subtítulo</label>
                    <input type="text" name="subtitle" value="<?= e(old('subtitle', $product['subtitle'])) ?>"
                           placeholder="Ex.: SOLID, HIGH RELIEF"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">SKU *</label>
                    <input type="text" name="sku" required value="<?= e(old('sku', $product['sku'])) ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 font-mono text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Slug (URL)</label>
                    <input type="text" name="slug" value="<?= e(old('slug', $product['slug'])) ?>" placeholder="auto-gerado"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 font-mono text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Descrição curta</label>
                <input type="text" name="short_description" maxlength="500"
                       value="<?= e(old('short_description', $product['short_description'])) ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Descrição completa</label>
                <textarea name="description" rows="6"
                          class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e(old('description', $product['description'])) ?></textarea>
            </div>
        </div>

        <!-- Preço e dimensões -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">Preço e dimensões</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Preço (R$)</label>
                    <input type="text" name="price" value="<?= e(old('price', number_format((float) $product['price'], 2, ',', ''))) ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Custo (R$) <span class="text-gray-400 normal-case tracking-normal">— interno</span></label>
                    <input type="text" name="cost" value="<?= $product['cost'] !== null ? e(number_format((float) $product['cost'], 2, ',', '')) : '' ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-brand-muted font-medium mb-1.5">Estoque</label>
                    <input type="number" name="stock_quantity" value="<?= e($product['stock_quantity'] ?? '') ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-brand-muted font-medium mb-1.5">Peso (kg)</label>
                    <input type="text" name="weight_kg" value="<?= e($product['weight_kg'] ?? '') ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-brand-muted font-medium mb-1.5">Largura (cm)</label>
                    <input type="text" name="width_cm" value="<?= e($product['width_cm'] ?? '') ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-brand-muted font-medium mb-1.5">Altura (cm)</label>
                    <input type="text" name="height_cm" value="<?= e($product['height_cm'] ?? '') ?>"
                           class="w-full border border-brand-line rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                </div>
            </div>
        </div>

        <!-- Tabela de variações (specifications JSON) -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-3">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-display font-semibold text-brand-ink">Tabela de variações</h2>
                    <p class="text-xs text-brand-muted mt-1">JSON array de objetos com code/thickness/A/B/etc. Renderizado como tabela na página do produto.</p>
                </div>
            </div>
            <textarea name="specifications_json" rows="10"
                      placeholder='[{"code":"BC-STR-50-0001","thickness":50,"a":200,"b":1200,"pieces_per_box":14,"coverage_area":"3,96 m²","pet_bottles":27}, ...]'
                      class="w-full border border-brand-line rounded-lg px-3 py-2 font-mono text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e($specsJson) ?></textarea>
        </div>

        <!-- Imagens -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">Imagens</h2>

            <?php if ($images !== []): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <?php foreach ($images as $img):
                        $u = upload_url('products/' . $img['file_path']);
                    ?>
                        <div class="relative group">
                            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden border <?= $img['is_main'] ? 'border-brand-blue ring-2 ring-brand-blue/20' : 'border-brand-line' ?>">
                                <img src="<?= e($u) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
                            </div>
                            <?php if ($img['is_main']): ?>
                                <span class="absolute top-2 left-2 bg-brand-blue text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Principal</span>
                            <?php endif; ?>
                            <div class="absolute inset-x-0 bottom-0 p-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                <?php if (!$img['is_main']): ?>
                                    <form method="post" action="<?= e(url('admin/produtos/' . $product['id'] . '/imagens/principal')) ?>" class="flex-1">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="image_id" value="<?= e((string) $img['id']) ?>">
                                        <button class="w-full bg-white/95 text-xs py-1 rounded hover:bg-white">★ principal</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="<?= e(url('admin/produtos/' . $product['id'] . '/imagens/excluir')) ?>"
                                      class="flex-1" onsubmit="return confirm('Remover esta imagem?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="image_id" value="<?= e((string) $img['id']) ?>">
                                    <button class="w-full bg-rose-600 text-white text-xs py-1 rounded hover:bg-rose-700">excluir</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Adicionar imagens (múltiplas)</label>
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                       class="w-full text-sm file:bg-brand-ink file:text-white file:border-0 file:px-4 file:py-2 file:rounded-full file:cursor-pointer file:mr-3 file:font-medium hover:file:bg-black transition">
                <p class="text-xs text-brand-muted mt-2">JPG, PNG, WebP ou GIF — máx <?= round(((int) (\App\Core\Config::get('UPLOAD_MAX_BYTES', 8388608))) / 1048576, 1) ?> MB por arquivo.</p>
            </div>
        </div>

        <!-- SEO -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-4">
            <h2 class="font-display font-semibold text-brand-ink">SEO</h2>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Meta title</label>
                <input type="text" name="meta_title" maxlength="255"
                       value="<?= e($product['meta_title'] ?? '') ?>"
                       class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2">Meta description</label>
                <textarea name="meta_description" rows="2" maxlength="500"
                          class="w-full border border-brand-line rounded-lg px-3 py-2 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"><?= e($product['meta_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- COLUNA DIREITA: status, categorias -->
    <aside class="space-y-6 h-fit lg:sticky lg:top-6">
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-3">
            <h2 class="font-display font-semibold text-brand-ink">Status</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                Ativo (visível no catálogo)
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                Em destaque
            </label>
        </div>

        <div class="bg-white border border-brand-line rounded-2xl p-6">
            <h2 class="font-display font-semibold text-brand-ink mb-3">Categorias</h2>
            <div class="max-h-64 overflow-y-auto space-y-1.5 text-sm">
                <?php
                // Organiza categorias hierarquicamente para exibição
                $byParent = [];
                foreach ($allCategories as $c) {
                    $pid = $c['parent_id'] === null ? 0 : (int) $c['parent_id'];
                    $byParent[$pid][] = $c;
                }
                $renderCat = function (int $parentId, int $depth = 0) use (&$renderCat, $byParent, $productCats) {
                    foreach ($byParent[$parentId] ?? [] as $c) {
                        $id = (int) $c['id'];
                        $checked = in_array($id, $productCats, true) ? 'checked' : '';
                        ?>
                        <label class="flex items-center gap-2" style="padding-left: <?= $depth * 16 ?>px">
                            <input type="checkbox" name="categories[]" value="<?= e((string) $id) ?>" <?= $checked ?>
                                   class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                            <?= e($c['name']) ?>
                            <?php if (!$c['is_active']): ?><span class="text-[10px] text-gray-400 ml-1">(inativa)</span><?php endif; ?>
                        </label>
                        <?php
                        $renderCat($id, $depth + 1);
                    }
                };
                $renderCat(0);
                ?>
            </div>
            <a href="<?= e(url('admin/categorias')) ?>" class="text-xs text-brand-blue hover:underline mt-3 inline-block">+ Nova categoria</a>
        </div>

        <button type="submit" class="w-full bg-brand-blue text-white py-3.5 rounded-full font-medium hover:bg-brand-blue-dark shadow-soft transition">
            <?= $isNew ? 'Criar produto' : 'Salvar alterações' ?>
        </button>
    </aside>
</form>
<?php $this->endSection(); ?>
