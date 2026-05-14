<?php
/**
 * Drawer lateral do carrinho. Wrapper + overlay; o conteúdo (lista + footer)
 * é renderizado pelo partial cart_drawer_content e pode ser substituído via
 * AJAX (CartApiController) sem recarregar a página.
 */

// SSR inicial: carrega estado atual do carrinho (apenas para logados).
$cartItems    = [];
$cartCount    = 0;
$cartSubtotal = 0.0;
$isGuest      = !\App\Core\Auth::check();
if (!$isGuest) {
    try {
        $summary      = (new \App\Services\CartService())->summary();
        $cartItems    = $summary['items'];
        $cartCount    = (int) $summary['count'];
        $cartSubtotal = (float) ($summary['subtotal'] ?? 0);
    } catch (\Throwable) {}
}
?>
<div x-cloak
     x-show="cartOpen"
     @keydown.escape.window="cartOpen = false"
     @open-cart.window="cartOpen = true"
     @close-cart.window="cartOpen = false"
     class="fixed inset-0 z-50 overflow-hidden">

    <!-- Overlay -->
    <div x-show="cartOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cartOpen = false"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Painel lateral -->
    <div x-show="cartOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col"
         id="cart-drawer-panel">

        <div id="cart-drawer-content" class="contents">
            <?php $this->partial('cart_drawer_content', [
                'cartItems'    => $cartItems,
                'cartCount'    => $cartCount,
                'cartSubtotal' => $cartSubtotal,
                'isGuest'      => $isGuest,
            ]); ?>
        </div>
    </div>
</div>

<!-- JS do carrinho AJAX. Define funções globais no escopo Alpine ($store). -->
<script>
(function() {
    const baseUrl = document.querySelector('meta[name="base-url"]').content;
    const getCsrf = () => document.querySelector('meta[name="csrf-token"]').content;

    function showToast(msg, type = 'success') {
        const div = document.createElement('div');
        div.className = 'fixed top-4 right-4 z-[60] px-4 py-3 rounded-xl shadow-lg text-sm font-medium ' +
            (type === 'error'
                ? 'bg-rose-50 border border-rose-200 text-rose-800'
                : 'bg-emerald-50 border border-emerald-200 text-emerald-800');
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => { div.style.opacity = '0'; div.style.transition = 'opacity .3s'; }, 1800);
        setTimeout(() => div.remove(), 2200);
    }

    function updateBadge(count) {
        document.querySelectorAll('[data-cart-badge]').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? '' : 'none';
        });
    }

    function setDrawerContent(html) {
        const container = document.getElementById('cart-drawer-content');
        if (container) container.innerHTML = html;
    }

    async function postJson(path, body) {
        const r = await fetch(baseUrl + path, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify(body || {}),
        });
        return await r.json();
    }

    // Funções globais usadas pelos botões nos templates
    window.addToCart = async function(productId, qty = 1, btn = null) {
        if (btn) btn.disabled = true;
        try {
            const data = await postJson('/api/carrinho/adicionar', { product_id: productId, quantity: qty });
            if (data.requireLogin) {
                showToast('Faça login para adicionar ao orçamento.', 'error');
                setTimeout(() => { window.location.href = data.redirect; }, 800);
                return;
            }
            if (!data.success) {
                showToast(data.message || 'Erro ao adicionar', 'error');
                return;
            }
            setDrawerContent(data.html);
            updateBadge(data.count);
            // Abre o drawer via evento Alpine
            window.dispatchEvent(new CustomEvent('open-cart'));
            showToast('Adicionado ao orçamento');
        } catch (e) {
            showToast('Falha de conexão', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    };

    window.updateCartItem = async function(itemId, quantity) {
        const data = await postJson('/api/carrinho/atualizar', { item_id: itemId, quantity });
        if (data.success !== false) {
            setDrawerContent(data.html);
            updateBadge(data.count);
        }
    };

    window.removeCartItem = async function(itemId) {
        const data = await postJson('/api/carrinho/remover', { item_id: itemId });
        if (data.success !== false) {
            setDrawerContent(data.html);
            updateBadge(data.count);
        }
    };
})();
</script>
