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

        <!-- Tabela de variações (form dinâmico) -->
        <?php
        // Prepara $specsArray pra o Alpine. Se vier do banco, normaliza para array de objetos.
        $specsArray = !empty($product['specifications'])
            ? (is_array($product['specifications']) ? $product['specifications'] : json_decode((string) $product['specifications'], true))
            : [];
        if (!is_array($specsArray)) $specsArray = [];
        $specsJsonInline = htmlspecialchars(json_encode(array_values($specsArray), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-3"
             x-data='{
                rows: <?= $specsJsonInline ?: "[]" ?>,
                addRow() { this.rows.push({code:"", thickness:"", a:"", b:"", c:"", d:"", pieces_per_box:"", coverage_area:"", pet_bottles:""}); },
                removeRow(i) { this.rows.splice(i, 1); },
                duplicateRow(i) {
                    const c = JSON.parse(JSON.stringify(this.rows[i]));
                    this.rows.splice(i + 1, 0, c);
                }
             }'>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-display font-semibold text-brand-ink">Tabela de variações</h2>
                    <p class="text-xs text-brand-muted mt-1">Variações de tamanho/modulação. Cada linha é um SKU (código) com suas dimensões e quantidades.</p>
                </div>
                <button type="button" @click="addRow()"
                        class="bg-brand-ink text-white px-4 py-2 rounded-full text-xs font-medium hover:bg-black transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nova variação
                </button>
            </div>

            <div class="space-y-2">
                <!-- Header (visual) -->
                <div class="hidden md:grid grid-cols-[1.6fr_0.6fr_0.55fr_0.55fr_0.55fr_0.55fr_0.6fr_0.85fr_0.6fr_0.3fr] gap-1.5 px-2 text-[10px] uppercase tracking-widest text-brand-muted font-medium">
                    <div>Code (SKU)</div>
                    <div>Thick</div>
                    <div>"A"</div>
                    <div>"B"</div>
                    <div>"C"</div>
                    <div>"D"</div>
                    <div>Pç/cx</div>
                    <div>Cobertura</div>
                    <div>PET</div>
                    <div></div>
                </div>

                <template x-for="(row, i) in rows" :key="i">
                    <div class="grid grid-cols-1 md:grid-cols-[1.6fr_0.6fr_0.55fr_0.55fr_0.55fr_0.55fr_0.6fr_0.85fr_0.6fr_0.3fr] gap-1.5 items-center bg-gray-50 rounded-xl p-2">
                        <input type="text" :name="`specifications[${i}][code]`" x-model="row.code"
                               placeholder="RF-FRA-25-0001"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 font-mono text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][thickness]`" x-model="row.thickness"
                               placeholder="50"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][a]`" x-model="row.a"
                               placeholder="500"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][b]`" x-model="row.b"
                               placeholder="500"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][c]`" x-model="row.c"
                               placeholder="2700"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][d]`" x-model="row.d"
                               placeholder="1200"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][pieces_per_box]`" x-model="row.pieces_per_box"
                               placeholder="14"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][coverage_area]`" x-model="row.coverage_area"
                               placeholder="3,96 m²"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <input type="text" :name="`specifications[${i}][pet_bottles]`" x-model="row.pet_bottles"
                               placeholder="27"
                               class="border border-brand-line rounded-lg px-2.5 py-1.5 text-xs focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition">
                        <div class="flex items-center gap-1 justify-end">
                            <button type="button" @click="duplicateRow(i)" title="Duplicar"
                                    class="w-8 h-8 rounded-lg text-brand-muted hover:text-brand-blue hover:bg-white transition flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <button type="button" @click="removeRow(i)" title="Remover"
                                    class="w-8 h-8 rounded-lg text-brand-muted hover:text-rose-600 hover:bg-rose-50 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a2 2 0 012-2h2a2 2 0 012 2v3"/></svg>
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="rows.length === 0">
                    <div class="text-center py-8 text-brand-muted text-sm">
                        Nenhuma variação. <button type="button" @click="addRow()" class="text-brand-blue hover:underline font-medium">+ Adicionar primeira</button>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-brand-line">
                <div class="text-xs text-brand-muted" x-text="`${rows.length} variação(ões)`"></div>
                <button type="button" @click="addRow()" class="text-xs text-brand-blue hover:underline font-medium">+ Adicionar variação</button>
            </div>
        </div>

        <!-- Imagens técnicas (dimensões e modulação) -->
        <div class="bg-white border border-brand-line rounded-2xl p-6 space-y-5">
            <div>
                <h2 class="font-display font-semibold text-brand-ink">Imagens técnicas</h2>
                <p class="text-xs text-brand-muted mt-1">Diagramas extraídos do PDF. Faça upload, arraste, ou cole da área de trabalho para substituir.</p>
            </div>

            <?php foreach ([
                ['key' => 'dimensions_image_path', 'label' => 'Diagrama de dimensões',  'hint' => 'PNG/JPG com cotas "A" e "B" — aparece na aba "Dimensões" da página do produto.'],
                ['key' => 'modulation_image_path', 'label' => 'Sugestões de modulação', 'hint' => 'PNG/JPG com mini-diagramas das modulações — aparece na seção "Modulação".'],
            ] as $tech): ?>
                <?php $path = $product[$tech['key']] ?? null; ?>
                <?php $originalUrl = $path ? upload_url('products/' . $path) : ''; ?>
                <div x-data="techImagePicker('<?= e($originalUrl) ?>')"
                     class="border border-brand-line rounded-xl p-4 grid grid-cols-1 md:grid-cols-[160px_1fr] gap-4 items-start">
                    <!-- Preview: imagem atual OU preview do arquivo escolhido OU placeholder -->
                    <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden border border-brand-line flex items-center justify-center">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" :alt="'<?= e($tech['label']) ?>'" class="w-full h-full object-contain">
                        </template>
                        <template x-if="!previewUrl">
                            <span class="text-[10px] text-brand-muted uppercase tracking-widest">Sem imagem</span>
                        </template>
                    </div>
                    <div class="space-y-2.5">
                        <div>
                            <div class="text-sm font-medium text-brand-ink"><?= e($tech['label']) ?></div>
                            <p class="text-xs text-brand-muted mt-0.5"><?= e($tech['hint']) ?></p>
                            <?php if ($path): ?>
                                <div class="text-[10px] text-brand-muted font-mono mt-1.5">Atual: <?= e($path) ?></div>
                            <?php endif; ?>
                            <div x-show="pickedName" x-cloak class="text-[10px] text-brand-blue font-mono mt-1.5">
                                Novo: <span x-text="pickedName"></span>
                            </div>
                        </div>

                        <input x-ref="fileInput" type="file" name="<?= e($tech['key']) ?>"
                               accept="image/jpeg,image/png,image/webp"
                               @change="onPick($event)" class="hidden">

                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="$refs.fileInput.click()"
                                    class="inline-flex items-center gap-1.5 bg-brand-ink text-white text-xs px-3 py-1.5 rounded-full hover:bg-black transition font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                Escolher arquivo
                            </button>
                            <button type="button" @click="pasteFromClipboard()"
                                    :disabled="pasting"
                                    class="inline-flex items-center gap-1.5 bg-white border border-brand-line text-brand-ink text-xs px-3 py-1.5 rounded-full hover:bg-gray-50 hover:border-brand-blue transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span x-text="pasting ? 'Aguardando…' : 'Colar da área de trabalho'"></span>
                            </button>
                            <button type="button" x-show="pickedName" x-cloak @click="clearPick()"
                                    class="inline-flex items-center text-xs text-rose-600 hover:underline px-2">
                                ✕ desfazer
                            </button>
                        </div>

                        <div x-show="pasteError" x-cloak class="text-[11px] text-rose-600" x-text="pasteError"></div>

                        <?php if ($path): ?>
                            <label class="flex items-center gap-2 text-xs text-rose-600">
                                <input type="checkbox" name="<?= e($tech['key']) ?>_remove" value="1"
                                       class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                                Remover imagem atual (não substituir)
                            </label>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
            function techImagePicker(originalUrl) {
                return {
                    originalUrl: originalUrl,
                    previewUrl: originalUrl,
                    pickedName: '',
                    pasting: false,
                    pasteError: '',
                    onPick(ev) {
                        const f = ev.target.files?.[0];
                        if (!f) return;
                        this.setFile(f);
                    },
                    setFile(f) {
                        this.previewUrl = URL.createObjectURL(f);
                        this.pickedName = f.name;
                        this.pasteError = '';
                    },
                    clearPick() {
                        this.$refs.fileInput.value = '';
                        this.pickedName = '';
                        this.previewUrl = this.originalUrl;
                    },
                    async pasteFromClipboard() {
                        this.pasteError = '';
                        if (!navigator.clipboard?.read) {
                            this.pasteError = 'Seu navegador não suporta colar imagens. Use Ctrl+V em um campo de texto, ou use "Escolher arquivo".';
                            return;
                        }
                        this.pasting = true;
                        try {
                            const items = await navigator.clipboard.read();
                            for (const item of items) {
                                for (const type of item.types) {
                                    if (type.startsWith('image/')) {
                                        const blob = await item.getType(type);
                                        const ext = type.split('/')[1] || 'png';
                                        const ts = new Date().toISOString().replace(/[:.]/g, '-');
                                        const file = new File([blob], `paste-${ts}.${ext}`, { type });
                                        // Sincroniza com o input file via DataTransfer
                                        const dt = new DataTransfer();
                                        dt.items.add(file);
                                        this.$refs.fileInput.files = dt.files;
                                        this.setFile(file);
                                        return;
                                    }
                                }
                            }
                            this.pasteError = 'Nenhuma imagem encontrada na área de transferência. Copie uma imagem primeiro (Print Screen, captura, etc).';
                        } catch (err) {
                            if (err.name === 'NotAllowedError') {
                                this.pasteError = 'Permissão de acesso à área de transferência negada. Habilite no navegador.';
                            } else {
                                this.pasteError = 'Erro ao colar: ' + err.message;
                            }
                        } finally {
                            this.pasting = false;
                        }
                    },
                };
            }
        </script>

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
