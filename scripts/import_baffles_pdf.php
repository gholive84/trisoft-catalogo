<?php

declare(strict_types=1);

/**
 * Importa produtos do PDF "catálogo_produtos_TRISOFT_BAFFLES.pdf".
 *
 * Espera o text extraído via:
 *   pdftotext -enc UTF-8 catálogo.pdf /tmp/baffles_pages.txt
 * (sem -layout — usa o separador \f entre páginas).
 *
 * Estratégia:
 *   - Cada produto ocupa 2 páginas no PDF: 1ª = hero (foto + título + subtitle),
 *     2ª = tabela de especificações (Code, Thickness, A, B, etc.).
 *   - Detectamos hero por: começa com "BAFFLE XXX YYY", tem subtitle, NÃO contém
 *     "Code"/"Thickness" (que indicariam tabela).
 *   - Detectamos specs por: contém SKUs no formato B?-XXX-NN-NNNN.
 *   - Cada hero é seguido pela página seguinte (a tabela do mesmo produto).
 *
 * Idempotente: usa slug como chave única.
 *
 * Uso:
 *   pdftotext -enc UTF-8 catálogo.pdf /tmp/baffles_pages.txt
 *   php scripts/import_baffles_pdf.php /tmp/baffles_pages.txt [--reset]
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
$pdo = Database::connection();

$args = $argv;
array_shift($args);
$reset = false;
$file  = null;
foreach ($args as $a) {
    if ($a === '--reset') { $reset = true; continue; }
    if ($file === null) { $file = $a; }
}
if ($file === null || !file_exists($file)) {
    fwrite(STDERR, "Uso: php scripts/import_baffles_pdf.php <text-file> [--reset]\n");
    exit(1);
}

$raw = (string) file_get_contents($file);

if ($reset) {
    fwrite(STDOUT, "⚠️  --reset: apagando produtos com SKU B?-...\n");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^B[A-Z]-')");
    $pdo->exec("DELETE FROM product_categories WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^B[A-Z]-')");
    $pdo->exec("DELETE FROM cart_items WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^B[A-Z]-')");
    $pdo->exec("DELETE FROM products WHERE sku REGEXP '^B[A-Z]-'");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
}

/* -------------- Helpers -------------- */

function ensureCategory(\PDO $pdo, string $slug, string $name, ?int $parentId = null): int
{
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if ($row) return (int) $row['id'];
    $pdo->prepare("INSERT INTO categories (parent_id, name, slug, is_active, sort_order) VALUES (?, ?, ?, 1, 0)")
        ->execute([$parentId, $name, $slug]);
    return (int) $pdo->lastInsertId();
}

function slugifyName(string $s): string
{
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $t = preg_replace('/[^A-Za-z0-9\s\-]/', '', $t) ?? $t;
    $t = preg_replace('/[\s\-]+/', '-', $t) ?? $t;
    return strtolower(trim($t, '-'));
}

/* -------------- Categorias -------------- */

$baffleCatId = ensureCategory($pdo, 'baffles', 'Baffles');
$lineCategories = [
    'CLASSIC' => ensureCategory($pdo, 'baffles-classic', 'Classic', $baffleCatId),
    'NESS'    => ensureCategory($pdo, 'baffles-ness',    'Ness',    $baffleCatId),
    'FORM'    => ensureCategory($pdo, 'baffles-form',    'Form',    $baffleCatId),
];
$shapeCategories = [
    'STRAIGHT'       => ensureCategory($pdo, 'baffles-straight',       'Straight',       $baffleCatId),
    'TRAPEZIUM'      => ensureCategory($pdo, 'baffles-trapezium',      'Trapezium',      $baffleCatId),
    'WAVE'           => ensureCategory($pdo, 'baffles-wave',           'Wave',           $baffleCatId),
    'TRAPEZIUM WAVE' => ensureCategory($pdo, 'baffles-trapezium-wave', 'Trapezium Wave', $baffleCatId),
    'ARC'            => ensureCategory($pdo, 'baffles-arc',            'Arc',            $baffleCatId),
];

/* -------------- Parse páginas -------------- */

// Quebra por \f (page feed)
$pages = explode("\f", $raw);
fwrite(STDOUT, "PDF tem " . count($pages) . " páginas.\n");

// Regex de SKU + colunas
$skuRegex = '/^\s*(B[A-Z]+-[A-Z]+-\d+-\d+)\s+(.+?)\s+(\d{2,4})\s+(\d{3,4})\s+(\d+)\s+([\d,\.]+\s*m[²2])\s*(\d+)?\s*$/u';

$products = [];

$normalizeSubtitle = function (string $s): string {
    $s = trim($s);
    // Unifica bullets e variações de separador
    $s = preg_replace('/[•·]/u', '-', $s);
    $s = preg_replace('/\s*-\s*-\s*-\s*/u', ' - ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};

for ($idx = 0; $idx < count($pages); $idx++) {
    $pageNum = $idx + 1;
    $text    = $pages[$idx];
    $lines   = preg_split('/\R/u', $text);

    // Detecta página de hero: começa com "BAFFLE XXX YYY", tem subtitle, sem tabela
    $title = '';
    $subtitle = '';
    $cleanLines = [];
    foreach ($lines as $line) {
        $l = trim($line, " \t\n\r\0\x0B\x0C");
        if ($l !== '') $cleanLines[] = $l;
    }
    if (count($cleanLines) < 2) continue;

    $first = $cleanLines[0] ?? '';
    $second = $cleanLines[1] ?? '';
    if (!preg_match('/^BAFFLE\s+([A-Z]+)\s+([A-Z]+(?:\s+[A-Z]+)*)$/u', $first, $m)) continue;
    if (strlen($second) < 2 || strlen($second) > 60) continue;
    if (stripos($text, 'Code') !== false && stripos($text, 'Thickness') !== false) continue;

    $line = $m[1];       // CLASSIC / NESS / FORM
    $shape = $m[2];      // STRAIGHT / TRAPEZIUM / etc.
    $title = $first;
    $subtitle = $normalizeSubtitle($second);

    // Pega a página seguinte (specs)
    $specsText = $pages[$idx + 1] ?? '';
    $specsLines = preg_split('/\R/u', $specsText);
    $specs = [];
    foreach ($specsLines as $sl) {
        if (preg_match($skuRegex, $sl, $sm)) {
            $specs[] = [
                'code'           => $sm[1],
                'thickness'      => trim($sm[2]),
                'a'              => (int) $sm[3],
                'b'              => (int) $sm[4],
                'pieces_per_box' => (int) $sm[5],
                'coverage_area'  => preg_replace('/\s+m[²2]/u', ' m²', trim($sm[6])),
                'pet_bottles'    => isset($sm[7]) && $sm[7] !== '' ? (int) $sm[7] : null,
            ];
        }
    }

    if (empty($specs)) continue;

    $name = $title . ' - ' . $subtitle;
    $slug = slugifyName($name);

    $products[$slug] = [
        'name'        => $name,
        'subtitle'    => $subtitle,
        'line_name'   => $line,
        'shape'       => $shape,
        'hero_page'   => $pageNum,
        'specs_page'  => $pageNum + 1,
        'specs'       => $specs,
        'slug'        => $slug,
    ];
}

fwrite(STDOUT, "Encontrados " . count($products) . " produtos únicos.\n\n");

/* -------------- Persistir -------------- */

$inserted = 0; $updated = 0;
foreach ($products as $slug => $p) {
    // SKU principal: prefixo dos 3 primeiros tokens do primeiro código
    $firstCode = $p['specs'][0]['code'];
    $parts = explode('-', $firstCode);
    $mainSku = implode('-', array_slice($parts, 0, 3));

    $shortDesc = match ($p['shape']) {
        'STRAIGHT'        => 'Baffle reto suspenso para controle de reverberação.',
        'TRAPEZIUM'       => 'Baffle trapezoidal com volumetria sofisticada.',
        'WAVE'            => 'Baffle ondulado para difusão e estética.',
        'ARC'             => 'Baffle em arco para soluções curvas.',
        'TRAPEZIUM WAVE'  => 'Baffle trapezoidal com perfil ondulado.',
        default            => 'Baffle acústico Trisoft.',
    };

    $description = "Covered with a flexible PET membrane finish.\n\n"
        . "Solid Colors: Chose from avaiable colors or select your pantone color. See QR CODE for avaiable colors.\n"
        . "Upcycling: All our products are 100% recyclable.";

    $specsJson = json_encode($p['specs'], JSON_UNESCAPED_UNICODE);

    // Verifica se existe (por slug)
    $exist = $pdo->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
    $exist->execute([$slug]);
    $existingId = $exist->fetchColumn();

    if ($existingId) {
        $pdo->prepare(
            "UPDATE products SET name=:n, sku=:sku, subtitle=:sub, short_description=:short,
                                 description=:desc, specifications=:specs
                            WHERE id=:id"
        )->execute([
            'n' => $p['name'], 'sku' => $mainSku, 'sub' => $p['subtitle'],
            'short' => $shortDesc, 'desc' => $description, 'specs' => $specsJson,
            'id' => $existingId,
        ]);
        $productId = (int) $existingId;
        $updated++;
        fwrite(STDOUT, "↻ Atualizado: {$p['name']} (page {$p['hero_page']}, " . count($p['specs']) . " specs)\n");
    } else {
        $pdo->prepare(
            "INSERT INTO products (sku, name, slug, subtitle, short_description, description,
                                   specifications, price, is_active, is_featured)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0.00, 1, 0)"
        )->execute([
            $mainSku, $p['name'], $slug, $p['subtitle'], $shortDesc, $description, $specsJson,
        ]);
        $productId = (int) $pdo->lastInsertId();
        $inserted++;
        fwrite(STDOUT, "+ Inserido:   {$p['name']} (page {$p['hero_page']}, " . count($p['specs']) . " specs)\n");
    }

    // Categorias: Baffles + linha + shape
    $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
        ->execute([$productId, $baffleCatId]);
    if (isset($lineCategories[$p['line_name']])) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$productId, $lineCategories[$p['line_name']]]);
    }
    if (isset($shapeCategories[$p['shape']])) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$productId, $shapeCategories[$p['shape']]]);
    }

    // Salva o número da página de hero em settings (auxiliar pra extrair imagens depois)
    $pdo->prepare(
        "INSERT INTO settings (`key`, `value`, `type`) VALUES (?, ?, 'int')
         ON DUPLICATE KEY UPDATE value = VALUES(value)"
    )->execute(["_baffles_hero_page_{$productId}", (string) $p['hero_page']]);
}

fwrite(STDOUT, "\n✅ Inseridos: {$inserted} | Atualizados: {$updated}\n");
