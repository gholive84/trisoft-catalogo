<?php

declare(strict_types=1);

/**
 * Importa produtos do PDF "catálogo_produtos_TRISOFT_BAFFLES.pdf"
 * usando o texto pré-extraído via pdftotext -layout.
 *
 * Cada "produto" no banco corresponde a uma página de produto do PDF
 * (ex.: "BAFFLE CLASSIC STRAIGHT - SOLID"), e a tabela de variações
 * (códigos BC-STR-50-0001..0004) vai no campo `specifications` JSON.
 *
 * Idempotente: usa slug como chave única; se o produto já existe, atualiza.
 *
 * Uso:
 *   pdftotext -layout catálogo_produtos_TRISOFT_BAFFLES.pdf /tmp/baffles.txt
 *   php scripts/import_baffles_pdf.php /tmp/baffles.txt
 *   php scripts/import_baffles_pdf.php /tmp/baffles.txt --reset   # apaga produtos BC/BN/BF/BD antes
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
    fwrite(STDERR, "Gere o text-file com: pdftotext -layout catálogo.pdf /tmp/baffles.txt\n");
    exit(1);
}

$raw = (string) file_get_contents($file);
// Conversão de encoding (pdftotext gera texto com chars latin1 / Â²)
$raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');

if ($reset) {
    fwrite(STDOUT, "⚠️  --reset: apagando produtos com SKU BC-/BN-/BF-/BD-...\n");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE sku LIKE 'B%-%')");
    $pdo->exec("DELETE FROM product_categories WHERE product_id IN (SELECT id FROM products WHERE sku LIKE 'B%-%')");
    $pdo->exec("DELETE FROM products WHERE sku LIKE 'B%-%'");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
}

/* -------------- 1) Garantir categoria Baffles e subcategorias -------------- */

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

$baffleCatId = ensureCategory($pdo, 'baffles', 'Baffles');

// Subcategorias por linha de baffle (Classic, Ness, Form)
$lineCategories = [
    'BC' => ensureCategory($pdo, 'baffles-classic', 'Classic', $baffleCatId),
    'BN' => ensureCategory($pdo, 'baffles-ness',    'Ness',    $baffleCatId),
    'BF' => ensureCategory($pdo, 'baffles-form',    'Form',    $baffleCatId),
    'BD' => ensureCategory($pdo, 'baffles-design',  'Design',  $baffleCatId),
];

/* -------------- 2) Parse do texto: identificar blocos de produto -------------- */

// Identificadores típicos:
//   "BAFFLE CLASSIC STRAIGHT"
//   <linha em branco / próxima section>
//   "SOLID" (ou "SOLID – HIGH RELIEF", "NESS SOLID", etc.)
//   ...
//   <tabela de SKUs>

// Procuramos por padrões: BAFFLE [LINE] [SHAPE], depois subtitle, depois SKU table.
// Estratégia simples: percorre linha a linha, captura nome quando encontra "BAFFLE ", subtitle na sequência,
// e SKUs até encontrar próxima section.

$lines = explode("\n", $raw);

// Padrão de linha de SKU no PDF:
//   Code  [Thickness textual|numérico]  A(mm)  B(mm)  Pieces  Coverage(m²)  [PET]
// A coluna "thickness" pode ser número simples ou texto (ex.: "N25 E 10").
$skuRegex = '/^\s*(B[A-Z]+-[A-Z]+-\d+-\d+)\s+(.+?)\s+(\d{2,4})\s+(\d{3,4})\s+(\d+)\s+([\d,\.]+\s*m[²2])\s*(\d+)?\s*$/u';

$normalizeSubtitle = function (string $s): string {
    $s = trim($s);
    $s = preg_replace('/[\s–—-]+/u', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};

$slugify = function (string $s): string {
    if (function_exists('slugify')) return slugify($s);
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $t = preg_replace('/[^A-Za-z0-9\s\-]/', '', $t) ?? $t;
    $t = preg_replace('/[\s\-]+/', '-', $t) ?? $t;
    return strtolower(trim($t, '-'));
};

/* -------------- 2) Parsing principal -------------- */

$products = [];
$current  = null;
for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $trimmed = trim($line);

    if (preg_match('/^BAFFLE\s+([A-Z]+)\s+([A-Z]+(?:\s+[A-Z]+)*)$/u', $trimmed, $m)) {
        if ($current !== null && !empty($current['specs'])) {
            $products[] = $current;
        }
        $lineName = $m[1];
        $shape    = $m[2];
        $current  = [
            'name'      => "BAFFLE {$lineName} {$shape}",
            'line_name' => $lineName,
            'shape'     => $shape,
            'subtitle'  => null,
            'specs'     => [],
            '_pending_subtitle' => true,
        ];
        continue;
    }

    if ($current !== null && !empty($current['_pending_subtitle']) && $trimmed !== ''
        && preg_match('/^[A-Z][A-Z\s–—\-]+$/u', $trimmed)
        && stripos($trimmed, 'BAFFLE') === false
        && stripos($trimmed, 'MODULATION') === false
        && strlen($trimmed) <= 60) {
        $current['subtitle'] = $normalizeSubtitle($trimmed);
        $current['name']    .= ' - ' . $current['subtitle'];
        $current['_pending_subtitle'] = false;
        continue;
    }

    if ($current !== null && preg_match($skuRegex, $line, $m)) {
        // m[1]=SKU, m[2]=thickness textual, m[3]=A, m[4]=B,
        // m[5]=pieces, m[6]=coverage, m[7]=pet (opcional)
        $current['specs'][] = [
            'code'           => $m[1],
            'thickness'      => trim($m[2]),
            'a'              => (int) $m[3],
            'b'              => (int) $m[4],
            'pieces_per_box' => (int) $m[5],
            'coverage_area'  => preg_replace('/\s+m[²2]/u', ' m²', trim($m[6])),
            'pet_bottles'    => isset($m[7]) && $m[7] !== '' ? (int) $m[7] : null,
        ];
    }
}
if ($current !== null && !empty($current['specs'])) {
    $products[] = $current;
}

// Agrupa por nome (já agora limpo)
$byName = [];
foreach ($products as $p) {
    $key = $p['name'];
    if (!isset($byName[$key])) {
        $byName[$key] = $p;
    } else {
        $codes = array_column($byName[$key]['specs'], 'code');
        foreach ($p['specs'] as $s) {
            if (!in_array($s['code'], $codes, true)) {
                $byName[$key]['specs'][] = $s;
                $codes[] = $s['code'];
            }
        }
    }
}

fwrite(STDOUT, "Encontrados " . count($byName) . " produtos únicos no PDF.\n\n");

$inserted = 0; $updated = 0;
foreach ($byName as $name => $p) {
    if (empty($p['specs'])) continue;

    // SKU principal: prefixo do primeiro código (BC-STR-50)
    $firstCode = $p['specs'][0]['code'];
    $skuParts  = explode('-', $firstCode);
    $mainSku   = implode('-', array_slice($skuParts, 0, 3));   // BC-STR-50
    $prefix    = $skuParts[0];                                  // BC

    // Slug derivado do nome
    $slug = $slugify($p['name']);

    // Descrição
    $description = "Covered with a flexible PET membrane finish.\n\n"
        . "Solid Colors: Chose from avaiable colors or select your pantone color. See QR CODE for avaiable colors.\n"
        . "Upcycling: All our products are 100% recyclable.";

    $shortDesc = match ($p['shape'] ?? '') {
        'STRAIGHT'         => 'Baffle reto suspenso para controle de reverberação.',
        'TRAPEZIUM'        => 'Baffle trapezoidal com volumetria sofisticada.',
        'WAVE'             => 'Baffle em ondas para difusão acústica e estética.',
        'ARC'              => 'Baffle em arco para soluções curvas.',
        'TRAPEZIUM WAVE'   => 'Baffle trapezoidal com perfil ondulado.',
        default            => 'Baffle acústico Trisoft.',
    };

    $specsJson = json_encode($p['specs'], JSON_UNESCAPED_UNICODE);

    // Verifica se existe
    $exist = $pdo->prepare("SELECT id FROM products WHERE sku = ? LIMIT 1");
    $exist->execute([$mainSku]);
    $existingId = $exist->fetchColumn();

    if ($existingId) {
        $stmt = $pdo->prepare(
            "UPDATE products
                SET name = :name, slug = :slug, subtitle = :sub,
                    short_description = :short, description = :desc,
                    specifications = :specs
              WHERE id = :id"
        );
        $stmt->execute([
            'name'  => $p['name'],
            'slug'  => $slug,
            'sub'   => $p['subtitle'] ?? null,
            'short' => $shortDesc,
            'desc'  => $description,
            'specs' => $specsJson,
            'id'    => $existingId,
        ]);
        $productId = (int) $existingId;
        $updated++;
        fwrite(STDOUT, "↻ Atualizado: {$p['name']} ({$mainSku}, " . count($p['specs']) . " specs)\n");
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO products
                (sku, name, slug, subtitle, short_description, description, specifications, price, is_active, is_featured)
             VALUES
                (:sku, :name, :slug, :sub, :short, :desc, :specs, 0.00, 1, 0)"
        );
        $stmt->execute([
            'sku'   => $mainSku,
            'name'  => $p['name'],
            'slug'  => $slug,
            'sub'   => $p['subtitle'] ?? null,
            'short' => $shortDesc,
            'desc'  => $description,
            'specs' => $specsJson,
        ]);
        $productId = (int) $pdo->lastInsertId();
        $inserted++;
        fwrite(STDOUT, "+ Inserido:   {$p['name']} ({$mainSku}, " . count($p['specs']) . " specs)\n");
    }

    // Associa às categorias: Baffles + linha (Classic/Ness/Form/Design)
    $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
        ->execute([$productId, $baffleCatId]);
    if (isset($lineCategories[$prefix])) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$productId, $lineCategories[$prefix]]);
    }
}

fwrite(STDOUT, "\n✅ Concluído. Inseridos: {$inserted} | Atualizados: {$updated}\n");
