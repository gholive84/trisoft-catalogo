<?php
/**
 * <head> compartilhado entre layouts (public, admin, customer).
 * Centraliza: meta, fontes, Tailwind config com paleta Trisoft, Alpine.js.
 */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Catálogo Trisoft') ?> — Trisoft</title>
<meta name="description" content="<?= e($metaDescription ?? 'Catálogo de produtos Trisoft. Revestimentos funcionais com tratamento acústico. Solicite seu orçamento online.') ?>">

<link rel="icon" type="image/png" href="<?= e(asset('images/logo-mark.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('images/logo-mark.png')) ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

<script>
    // Paleta Trisoft (extraída do site institucional).
    // Cores referenciáveis em classes Tailwind como bg-brand-blue, text-brand-green, etc.
    window.tailwind = window.tailwind || {};
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                },
                colors: {
                    brand: {
                        blue:     '#0071A2',
                        'blue-dark': '#046B98',
                        'blue-900': '#1C244B',
                        green:    '#8BC750',
                        'green-dark': '#6DAE3F',
                        teal:     '#00A28E',
                        cream:    '#F6F6F6',
                        ink:      '#1C244B',
                    },
                },
                boxShadow: {
                    'brand': '0 10px 40px -10px rgba(0, 113, 162, 0.25)',
                    'soft':  '0 2px 8px -2px rgba(28, 36, 75, 0.08)',
                },
                backgroundImage: {
                    'brand-gradient': 'linear-gradient(135deg, #0071A2 0%, #00A28E 50%, #8BC750 100%)',
                    'brand-radial':   'radial-gradient(circle at 30% 20%, rgba(139,199,80,0.18), transparent 50%), radial-gradient(circle at 80% 70%, rgba(0,162,142,0.18), transparent 50%), linear-gradient(135deg, #0a2540 0%, #1C244B 100%)',
                },
            },
        },
    };
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    h1, h2, h3, .font-display { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; letter-spacing: -0.01em; }
    [x-cloak] { display: none !important; }
</style>
