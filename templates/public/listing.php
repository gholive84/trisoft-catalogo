<?php /** @var App\Core\View $this */ $this->extend('layouts/public'); ?>

<?php $this->section('content'); ?>
<?php
$initialCategory = $initialCategory ?? null;
$pageTitle = $initialCategory ? $initialCategory['name'] : 'Nossos Produtos';
$pageSubtitle = $initialCategory && !empty($initialCategory['description'])
    ? $initialCategory['description']
    : 'Soluções acústicas sustentáveis com design premium';

// Garante que checkbox state inclui pre-selected categoria da URL
$initialSelected = $selectedCats ?? [];
?>

<!-- Hero full-width neutro (cinza claro com leve gradient) -->
<section class="relative overflow-hidden border-b border-brand-line bg-gray-50">
    <div class="relative max-w-content mx-auto px-6 lg:px-10 py-10 lg:py-14 text-center">
        <?php if ($initialCategory): ?>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 backdrop-blur-sm border border-brand-line text-[11px] font-semibold uppercase tracking-widest text-brand-blue mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>
                Categoria
            </div>
        <?php endif; ?>
        <h1 class="display text-4xl md:text-5xl lg:text-6xl text-brand-ink"><?= e($pageTitle) ?></h1>
        <p class="text-brand-muted mt-3 text-sm md:text-base max-w-2xl mx-auto"><?= e($pageSubtitle) ?></p>

        <?php if (isset($pagination['total']) && $pagination['total'] > 0): ?>
            <div class="text-[11px] text-brand-muted/80 mt-4 uppercase tracking-widest font-medium">
                <?= e((string) $pagination['total']) ?> produto<?= $pagination['total'] === 1 ? '' : 's' ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="max-w-content mx-auto px-6 lg:px-10 py-8 grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-10"
         x-data='catalogListing(<?= e(json_encode([
             "selectedCats" => array_values(array_map("intval", $initialSelected)),
             "query"        => $query ?? "",
             "tags"         => $activeTags ?? [],
             "baseUrl"      => \App\Core\Config::baseUrl(),
         ], JSON_UNESCAPED_UNICODE)) ?>)'
         x-init="init()">

    <!-- Sidebar de filtros -->
    <aside class="lg:sticky lg:top-8 h-fit space-y-6">
        <!-- Campo de busca -->
        <div>
            <label class="block text-xs uppercase tracking-widest text-brand-muted font-medium mb-2 px-1">Buscar</label>
            <div class="relative">
                <input type="search"
                       x-model="query"
                       @input.debounce.350ms="refresh()"
                       @keydown.enter.prevent="refresh()"
                       placeholder="Nome, SKU..."
                       class="w-full bg-gray-100 border border-transparent rounded-full pl-10 pr-9 py-2.5 text-sm placeholder:text-gray-400 focus:bg-white focus:border-brand-line focus:ring-2 focus:ring-gray-200 transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.817-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <button type="button"
                        x-show="query.length > 0" x-cloak
                        @click="query=''; refresh()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-ink"
                        aria-label="Limpar busca">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Categorias -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="text-xs uppercase tracking-widest text-brand-muted font-medium">Categorias</div>
                <button type="button"
                        x-show="selectedCats.length > 0 || query.length > 0" x-cloak
                        @click="clearAll()"
                        class="text-[11px] text-brand-blue hover:underline font-medium">
                    Limpar
                </button>
            </div>

            <nav class="flex flex-col gap-px text-sm">
                <?php
                $renderCategory = function (array $node, int $depth = 0) use (&$renderCategory) {
                    $id = (int) $node['id'];
                    $hasChildren = !empty($node['children']);
                ?>
                    <div>
                        <label class="relative block cursor-pointer rounded-xl transition-all duration-200 select-none"
                               :class="selectedCats.includes(<?= $id ?>)
                                        ? 'bg-brand-ink/[0.06] text-brand-ink'
                                        : 'text-brand-muted hover:bg-gray-50 hover:text-brand-ink'">
                            <input type="checkbox" value="<?= $id ?>"
                                   @change="toggleCat(<?= $id ?>)"
                                   :checked="selectedCats.includes(<?= $id ?>)"
                                   class="sr-only peer">

                            <!-- Indicador vertical à esquerda quando selecionado -->
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-[3px] rounded-r bg-brand-ink transition-all duration-200"
                                  :class="selectedCats.includes(<?= $id ?>) ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50'"></span>

                            <span class="flex items-center justify-between py-2.5 pr-3"
                                  :style="'padding-left: <?= 14 + ($depth * 14) ?>px'">
                                <span :class="selectedCats.includes(<?= $id ?>) ? 'font-medium tracking-tight' : ''">
                                    <?= e($node['name']) ?>
                                </span>
                                <!-- check sutil quando selecionado -->
                                <svg x-show="selectedCats.includes(<?= $id ?>)" x-cloak
                                     class="w-3.5 h-3.5 text-brand-ink ml-2 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        </label>
                        <?php if ($hasChildren): ?>
                            <div class="flex flex-col gap-px">
                                <?php foreach ($node['children'] as $child) $renderCategory($child, $depth + 1); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php };
                foreach ($tree as $n) $renderCategory($n, 0);
                ?>
            </nav>
        </div>
    </aside>

    <!-- Grid -->
    <div>
        <!-- Tags ativas -->
        <div x-show="tags.length > 0" x-cloak class="flex flex-wrap items-center gap-2 mb-6 pb-5 border-b border-brand-line">
            <span class="text-xs uppercase tracking-widest text-brand-muted font-medium mr-1">Filtros:</span>
            <template x-for="t in tags" :key="t.type + '-' + t.id">
                <button type="button" @click="removeTag(t)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-ink text-white rounded-full text-xs font-medium hover:bg-black transition group">
                    <span x-text="t.label"></span>
                    <svg class="w-3 h-3 opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </template>
        </div>

        <!-- Loading indicator -->
        <div x-show="loading" x-cloak class="text-center text-brand-muted text-sm py-8">
            <div class="inline-block w-5 h-5 border-2 border-brand-line border-t-brand-ink rounded-full animate-spin"></div>
            <span class="ml-2">Carregando...</span>
        </div>

        <!-- Grid (atualizado via AJAX) -->
        <div id="catalog-grid" :class="loading ? 'opacity-50 pointer-events-none' : ''">
            <?php $this->partial('products_grid', ['pagination' => $pagination]); ?>
        </div>
    </div>
</section>

<script>
function catalogListing(initial) {
    return {
        selectedCats: initial.selectedCats || [],
        query:        initial.query || '',
        tags:         initial.tags || [],
        loading:      false,
        baseUrl:      initial.baseUrl || '',

        init() {
            // Sync ao clicar nas paginações
            document.getElementById('catalog-grid').addEventListener('click', (e) => {
                const btn = e.target.closest('[data-page]');
                if (btn) {
                    e.preventDefault();
                    this.fetchGrid(parseInt(btn.dataset.page, 10) || 1);
                }
            });

            // ESC limpa busca
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.query) {
                    this.query = '';
                    this.refresh();
                }
            });

            // Permite que outros componentes definam busca via evento
            window.addEventListener('catalog-search', (e) => {
                this.query = (e.detail && e.detail.query) || '';
                this.refresh();
            });
        },

        toggleCat(id) {
            id = parseInt(id, 10);
            const idx = this.selectedCats.indexOf(id);
            if (idx >= 0) this.selectedCats.splice(idx, 1);
            else this.selectedCats.push(id);
            this.refresh();
        },

        removeTag(t) {
            if (t.type === 'category') {
                const idx = this.selectedCats.indexOf(parseInt(t.id, 10));
                if (idx >= 0) this.selectedCats.splice(idx, 1);
            } else if (t.type === 'search') {
                this.query = '';
            }
            this.refresh();
        },

        clearAll() {
            this.selectedCats = [];
            this.query = '';
            this.refresh();
        },

        async refresh(page = 1) {
            await this.fetchGrid(page);
            this.updateUrl();
        },

        buildQueryString(page) {
            const p = new URLSearchParams();
            if (this.selectedCats.length) p.set('cats', this.selectedCats.join(','));
            if (this.query) p.set('q', this.query);
            if (page > 1) p.set('page', page);
            return p.toString();
        },

        updateUrl() {
            const qs = this.buildQueryString(1);
            const url = this.baseUrl + '/' + (qs ? '?' + qs : '');
            history.replaceState({}, '', url);
        },

        async fetchGrid(page = 1) {
            this.loading = true;
            try {
                const qs = this.buildQueryString(page);
                const r = await fetch(this.baseUrl + '/api/produtos' + (qs ? '?' + qs : ''), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await r.json();
                if (data.success) {
                    document.getElementById('catalog-grid').innerHTML = data.html;
                    this.tags = data.tags || [];
                    window.scrollTo({ top: document.querySelector('section[x-data]').offsetTop - 80, behavior: 'smooth' });
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
<?php $this->endSection(); ?>
