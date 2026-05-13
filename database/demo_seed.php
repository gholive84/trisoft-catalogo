<?php

declare(strict_types=1);

/**
 * Demo seed — popula o banco com categorias e produtos de exemplo
 * para testar o catálogo público antes do CRUD do admin (Sprint 4).
 *
 * Idempotente: usa slugs como chave (não duplica em re-execuções).
 *
 * Uso:
 *   php database/demo_seed.php           # popula
 *   php database/demo_seed.php --reset   # apaga categorias/produtos antes (cuidado)
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
$pdo = Database::connection();

$reset = in_array('--reset', $argv, true);

if ($reset) {
    fwrite(STDOUT, "⚠️  --reset: limpando categorias e produtos de exemplo...\n");
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec("DELETE FROM product_images");
    $pdo->exec("DELETE FROM product_categories");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM categories");
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

/* ---------- Categorias ---------- */

$categories = [
    // [slug, name, parent_slug|null, description, sort_order]
    ['paineis-acusticos',        'Painéis acústicos',      null, 'Tratamento estético e funcional para paredes e tetos.', 1],
    ['paineis-tecidos',          'Painéis em tecido',      'paineis-acusticos', 'Painéis revestidos em tecidos premium.', 1],
    ['paineis-madeira',          'Painéis em madeira',     'paineis-acusticos', 'Painéis ranhurados e perfurados.', 2],
    ['difusores',                'Difusores',              null, 'Espalham a reflexão sonora de forma uniforme.', 2],
    ['difusores-quadraticos',    'Difusores quadráticos',  'difusores', 'Baseados em sequências matemáticas.', 1],
    ['absorvedores',             'Absorvedores',           null, 'Controle de reverberação para salas técnicas.', 3],
    ['absorvedores-graves',      'Bass traps',             'absorvedores', 'Absorvedores para baixas frequências.', 1],
    ['estudio',                  'Tratamento para estúdio',null, 'Kits e produtos especializados para estúdios.', 4],
];

$catIds = [];

foreach ($categories as [$slug, $name, $parentSlug, $desc, $sort]) {
    $parentId = $parentSlug !== null ? ($catIds[$parentSlug] ?? null) : null;

    $existing = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
    $existing->execute([$slug]);
    $row = $existing->fetch();

    if ($row) {
        $catIds[$slug] = (int) $row['id'];
        continue;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO categories (parent_id, name, slug, description, sort_order, is_active)
         VALUES (?, ?, ?, ?, ?, 1)"
    );
    $stmt->execute([$parentId, $name, $slug, $desc, $sort]);
    $catIds[$slug] = (int) $pdo->lastInsertId();
    fwrite(STDOUT, "✓ Categoria: {$name}\n");
}

/* ---------- Produtos ---------- */

$products = [
    [
        'sku'   => 'PNL-TEC-001',
        'name'  => 'Painel acústico Linus 60x60',
        'slug'  => 'painel-acustico-linus-60x60',
        'short' => 'Painel quadrado revestido em tecido, ideal para áreas técnicas e residenciais.',
        'desc'  => "Painel acústico de alta performance em fibra de poliéster.\nAbsorção NRC 0.85.\nRevestimento em tecido premium disponível em diversas cores.",
        'price' => 189.90,
        'featured' => true,
        'cats'  => ['paineis-tecidos', 'estudio'],
    ],
    [
        'sku'   => 'PNL-TEC-002',
        'name'  => 'Painel acústico Wave 100x100',
        'slug'  => 'painel-acustico-wave-100x100',
        'short' => 'Painel em formato ondulado para tratamento estético-funcional.',
        'desc'  => "Estética em ondas suaves, ideal para salas de reunião e home theater.\nNRC 0.80.",
        'price' => 349.00,
        'featured' => true,
        'cats'  => ['paineis-tecidos'],
    ],
    [
        'sku'   => 'PNL-MAD-001',
        'name'  => 'Painel ranhurado MDF 120x60',
        'slug'  => 'painel-ranhurado-mdf-120x60',
        'short' => 'Painel ranhurado em MDF, acabamento amadeirado.',
        'desc'  => "Painel ranhurado para difusão + absorção combinadas.\nAcabamento em laminado de alta resistência.\nDisponível em carvalho, nogueira e freixo.",
        'price' => 459.00,
        'featured' => true,
        'cats'  => ['paineis-madeira'],
    ],
    [
        'sku'   => 'DIF-QRD-001',
        'name'  => 'Difusor quadrático QRD-7',
        'slug'  => 'difusor-quadratico-qrd-7',
        'short' => 'Difusor baseado em sequência de resíduos quadráticos, primo 7.',
        'desc'  => "Difusor 1D para tratamento de paredes traseiras em salas de áudio.\nFaixa de operação: 800Hz–5kHz.",
        'price' => 1290.00,
        'featured' => true,
        'cats'  => ['difusores-quadraticos'],
    ],
    [
        'sku'   => 'DIF-SKY-001',
        'name'  => 'Difusor Skyline 60x60',
        'slug'  => 'difusor-skyline-60x60',
        'short' => 'Difusor 2D em padrão Manhattan.',
        'desc'  => "Difusor de números primitivos em duas dimensões.\nAlta dispersão omnidirecional.",
        'price' => 980.00,
        'cats'  => ['difusores'],
    ],
    [
        'sku'   => 'ABS-BAS-001',
        'name'  => 'Bass trap canto Pro 80cm',
        'slug'  => 'bass-trap-canto-pro-80cm',
        'short' => 'Bass trap triangular para cantos verticais.',
        'desc'  => "Absorvedor triangular para cantos.\nFaixa de operação: 80–500Hz.\nVendido em pares.",
        'price' => 690.00,
        'featured' => true,
        'cats'  => ['absorvedores-graves'],
    ],
    [
        'sku'   => 'ABS-PLT-001',
        'name'  => 'Absorvedor de placa 60x60',
        'slug'  => 'absorvedor-de-placa-60x60',
        'short' => 'Placa absorvedora sintonizada para baixas frequências.',
        'desc'  => "Membrana sintonizada para absorção entre 60 e 200Hz.\nInstalação em parede.",
        'price' => 549.00,
        'cats'  => ['absorvedores'],
    ],
    [
        'sku'   => 'KIT-EST-001',
        'name'  => 'Kit estúdio caseiro 12 peças',
        'slug'  => 'kit-estudio-caseiro-12-pecas',
        'short' => 'Kit completo para tratamento de salas de até 12 m².',
        'desc'  => "Inclui:\n- 6 painéis Linus 60x60\n- 4 bass traps\n- 2 difusores Skyline\n\nIdeal para home studios.",
        'price' => 2890.00,
        'cats'  => ['estudio'],
    ],
];

foreach ($products as $p) {
    $existing = $pdo->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
    $existing->execute([$p['slug']]);
    if ($existing->fetch()) {
        continue;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO products
            (sku, name, slug, short_description, description, price, is_active, is_featured)
         VALUES (?, ?, ?, ?, ?, ?, 1, ?)"
    );
    $stmt->execute([
        $p['sku'], $p['name'], $p['slug'], $p['short'], $p['desc'], $p['price'],
        !empty($p['featured']) ? 1 : 0,
    ]);
    $productId = (int) $pdo->lastInsertId();

    $assoc = $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)");
    foreach ($p['cats'] as $catSlug) {
        if (isset($catIds[$catSlug])) {
            $assoc->execute([$productId, $catIds[$catSlug]]);
        }
    }
    fwrite(STDOUT, "✓ Produto: {$p['name']}\n");
}

fwrite(STDOUT, "\n✅ Demo seed concluído. Acesse / para ver o catálogo.\n");
