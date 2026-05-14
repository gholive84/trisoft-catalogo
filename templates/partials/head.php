<?php
/**
 * <head> compartilhado entre layouts.
 *
 * Importante: Tailwind CDN deve carregar ANTES da config (a config registra-se
 * no objeto tailwind global criado pelo CDN). Inverter a ordem perde o config.
 */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Catálogo Trisoft') ?> — Trisoft</title>
<meta name="description" content="<?= e($metaDescription ?? 'Catálogo Trisoft. Revestimentos acústicos com tratamento de alta performance para arquitetura e design.') ?>">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<meta name="base-url" content="<?= e(\App\Core\Config::baseUrl()) ?>">

<link rel="icon" href="<?= e(asset('images/favicon.ico')) ?>" sizes="any">
<link rel="icon" type="image/png" sizes="16x16"   href="<?= e(asset('images/favicon-16.png')) ?>">
<link rel="icon" type="image/png" sizes="32x32"   href="<?= e(asset('images/favicon-32.png')) ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= e(asset('images/favicon-192.png')) ?>">
<link rel="apple-touch-icon"      sizes="180x180" href="<?= e(asset('images/apple-touch-icon.png')) ?>">
<link rel="manifest"              href="<?= e(asset('site.webmanifest')) ?>">
<meta name="theme-color" content="#0071A2">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Paleta Trisoft (extraída do site institucional).
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                },
                colors: {
                    brand: {
                        blue:        '#2962FF',
                        'blue-dark': '#1E4DCC',
                        'blue-soft': '#0071A2',
                        green:       '#8BC750',
                        'green-dark': '#6DAE3F',
                        teal:        '#00A28E',
                        navy:        '#1C244B',
                        ink:         '#111111',
                        muted:       '#6b6b6b',
                        line:        '#E5E5E5',
                    },
                },
                letterSpacing: {
                    tightest: '-0.04em',
                    tighter:  '-0.02em',
                    wider:    '0.06em',
                    widest:   '0.18em',
                },
                maxWidth: {
                    'content': '1280px',
                },
            },
        },
    };
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; font-feature-settings: 'ss01' on, 'cv11' on; -webkit-font-smoothing: antialiased; }
    .display { font-weight: 300; letter-spacing: -0.025em; }
    [x-cloak] { display: none !important; }
</style>
