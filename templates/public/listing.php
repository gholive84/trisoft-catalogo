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

<section class="max-w-content mx-auto px-6 lg:px-10 pt-12 lg:pt-16 pb-6 text-center">
    <h1 class="display text-5xl md:text-6xl lg:text-7xl text-brand-ink"><?= e($pageTitle) ?></h1>
    <p class="text-brand-muted mt-4 text-base md:text-lg"><?= e($pageSubtitle) ?></p>
</section>

<section class="max-w-content mx-auto px-6 lg:px-10 py-8 grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-10"
         x-data='catalogListing(<?= e(json_encode([
             "selectedCats" => array_values(array_map("intval", $initialSelected)),
             "query"        => $query ?? "",
             "tags"         => $activeTags ?? [],
             "baseUrl"      => \App\Core\Config::baseUrl(),
         ], JSON_UNESCAPED_UNICODE)) ?>)'
         x-init="init()">

    <!-- Sidebar de categorias (checkboxes) -->
    <aside class="lg:sticky lg:top-8 h-fit">
        <div class="text-xs uppercase tracking-widest text-brand-muted font-medium mb-3 px-1">Categorias</div>

        <!-- Limpar -->
        <div x-show="selectedCats.length > 0 || query.length > 0" x-cloak class="mb-3">
            <button type="button" @click="clearAll()"
                    class="text-xs text-brand-blue hover:underline">
                ✕ Limpar filtros
            </button>
        </div>

        <nav class="flex flex-col gap-0.5 text-sm">
            <?php
            $renderCategory = function (array $node, int $depth = 0) use (&$renderCategory) {
                $id = (int) $node['id'];
                $hasChildren = !empty($node['children']);
            ?>
                <div class="text-sm">
                    <label class="flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-gray-50 cursor-pointer"
                           style="padding-left: <?= 8 + ($depth * 16) ?>px">
                        <input type="checkbox" value="<?= $id ?>"
                               @change="toggleCat(<?= $id ?>)"
                               :checked="selectedCats.includes(<?= $id ?>)"
                               class="rounded border-gray-300 text-brand-ink focus:ring-brand-ink">
                        <span class="text-brand-ink"><?= e($node['name']) ?></span>
                    </label>
                    <?php if ($hasChildren): ?>
                        <div class="flex flex-col gap-0.5">
                            <?php foreach ($node['children'] as $child) $renderCategory($child, $depth + 1); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php };
            foreach ($tree as $n) $renderCategory($n, 0);
            ?>
        </nav>
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
