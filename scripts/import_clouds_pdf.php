<?php

declare(strict_types=1);

/**
 * Importa produtos do catálogo NUVEM (Clouds) — pasta catalogos/SEPARADOS/NUVEM/.
 *
 * Cada PDF é um produto-pai (ex.: CLASSIC CLOUD SQUARE) com várias variações
 * (SOLID, HIGH RELIEF, DECOR PRINTED, etc.) em pares de páginas hero+specs.
 *
 * Diferenças do parser de baffles:
 *   - Padrão de nome: "CLASSIC CLOUD SQUARE" (linha primeiro) OU
 *                     "CLOUD FORM SQUARE"    (cloud primeiro)
 *   - SKU prefixes: NC, ND, NF, NN (Nuvem Classic/Decor/Form/Ness)
 *   - Categoria raiz: 'nuvens'
 *   - Sub-cats por linha (Classic/Form/Ness/Softfelt) e por shape
 *   - Nenhum PDF tem "Modulation suggestions" — marca _has_modulation=0
 *
 * IMPORTANTE: o texto deve ser extraído SEM `-layout` (formato column-major
 * onde valores ficam em linhas separadas dos cabeçalhos). O parser de specs
 * column-major é mais confiável para as tabelas da NUVEM que misturam dimensões
 * 500x500 e 1200x1200 no mesmo SKU pai.
 *
 * Uso:
 *   pdftotext -enc UTF-8 catalogos/SEPARADOS/NUVEM/clouds_1.pdf /tmp/clouds/clouds_1.txt
 *   php scripts/import_clouds_pdf.php /tmp/clouds/*.txt [--reset]
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
$pdo = Database::connection();

/* ---------- Args ---------- */

$args = $argv;
array_shift($args);
$reset = false;
$files = [];
foreach ($args as $a) {
    if ($a === '--reset') { $reset = true; continue; }
    if (is_dir($a)) {
        foreach (glob($a . '/*.txt') as $f) $files[] = $f;
    } elseif (is_file($a)) {
        $files[] = $a;
    } else {
        // Glob expansion (Linux shell já expande, mas no Windows pode passar literal)
        foreach (glob($a) as $f) $files[] = $f;
    }
}
if ($files === []) {
    fwrite(STDERR, "Uso: php scripts/import_clouds_pdf.php <file.txt|dir|glob> [--reset]\n");
    exit(1);
}

if ($reset) {
    fwrite(STDOUT, "⚠️  --reset: apagando produtos com SKU N[A-Z]-...\n");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^N[A-Z]-')");
    $pdo->exec("DELETE FROM product_categories WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^N[A-Z]-')");
    $pdo->exec("DELETE FROM cart_items WHERE product_id IN (SELECT id FROM products WHERE sku REGEXP '^N[A-Z]-')");
    $pdo->exec("DELETE FROM products WHERE sku REGEXP '^N[A-Z]-'");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
}

/* ---------- Helpers ---------- */

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

/* ---------- Categorias ---------- */

$nuvensCatId = ensureCategory($pdo, 'nuvens', 'Nuvens');
$lineCategories = [
    'CLASSIC'  => ensureCategory($pdo, 'nuvens-classic',  'Classic',  $nuvensCatId),
    'FORM'     => ensureCategory($pdo, 'nuvens-form',     'Form',     $nuvensCatId),
    'NESS'     => ensureCategory($pdo, 'nuvens-ness',     'Ness',     $nuvensCatId),
    'SOFTFELT' => ensureCategory($pdo, 'nuvens-softfelt', 'Softfelt', $nuvensCatId),
];
$shapeCategories = [
    'SQUARE'      => ensureCategory($pdo, 'nuvens-square',      'Square',      $nuvensCatId),
    'RECTANGULAR' => ensureCategory($pdo, 'nuvens-rectangular', 'Rectangular', $nuvensCatId),
    'HEXAGONAL'   => ensureCategory($pdo, 'nuvens-hexagonal',   'Hexagonal',   $nuvensCatId),
    'TRIANGULAR'  => ensureCategory($pdo, 'nuvens-triangular',  'Triangular',  $nuvensCatId),
    'CIRCULAR'    => ensureCategory($pdo, 'nuvens-circular',    'Circular',    $nuvensCatId),
    'ORGANIC'     => ensureCategory($pdo, 'nuvens-organic',     'Organic',     $nuvensCatId),
    'FLY'         => ensureCategory($pdo, 'nuvens-fly',         'Fly',         $nuvensCatId),
];

/* ---------- Spec parser (column-major) ---------- */

/**
 * Parser column-major (texto extraído com `pdftotext` SEM -layout).
 * O PDF da NUVEM organiza specs por coluna:
 *   Code
 *   NC-XX-NN-0001 NC-XX-NN-0002
 *   Thickness (mm)
 *   50 50
 *   "A" (mm)
 *   500 1200
 *   "B" (mm)
 *   500 1200
 *   Unit
 *   1 1
 *   PET Bottles per unit
 *   26 148
 *
 * Valores podem aparecer todos na mesma linha ou em linhas separadas.
 */
function parseSpecsColumnMajor(string $pageText): array
{
    $lines = preg_split('/\R/u', $pageText) ?: [];
    $n = count($lines);

    // Padrão que reconhece o início de cada seção (cabeçalho da coluna).
    // IMPORTANTE: o regex precisa consumir o cabeçalho COMPLETO (incluindo "(mm)",
    // "per unit", etc) senão o sufixo vira valor. Aspas curvas no pdftotext viram
    // "â€œ" e "â€" (mojibake UTF-8 mal interpretado).
    $headerKeys = [
        'thickness' => '/^Thickness\s*\(mm\)\s*/i',
        'a'         => '/^(?:["“]|â€œ)?A(?:["”]|â€)?\s*\(mm\)\s*/u',
        'b'         => '/^(?:["“]|â€œ)?B(?:["”]|â€)?\s*\(mm\)\s*/u',
        'c'         => '/^(?:["“]|â€œ)?C(?:["”]|â€)?\s*\(mm\)\s*/u',
        'unit'      => '/^(?:Unit|Pieces\s+per\s+box)\s*/i',
        'coverage'  => '/^Coverage\s+Area\s*/i',
        'pet'       => '/^PET\s+Bottles\s+per\s+unit\s*/i',
    ];
    // Padrão de fim de seção (qualquer header ou texto introdutório)
    $stopPattern = '/^(Thickness\s*\(mm\)|(?:["“]|â€œ)?[ABC](?:["”]|â€)?\s*\(mm\)|Unit\s*$|Pieces\s+per\s+box|Coverage\s+Area|PET\s+Bottles|Code(\s|$)|Colors?:|Prints?:|Solid\s+Colors|High\s+Relief|Upcycling|Covered|Made\s+with|Modulation)/iu';

    // Helper: localizar linha que começa com $pattern a partir de $start; retorna ['idx', 'tail']
    $findSection = function (string $pattern, int $start = 0) use ($lines, $n): ?array {
        for ($i = $start; $i < $n; $i++) {
            $line = trim($lines[$i]);
            if ($line === '') continue;
            if (preg_match($pattern, $line, $m, PREG_OFFSET_CAPTURE)) {
                $end = $m[0][1] + strlen($m[0][0]);
                $tail = trim(substr($line, $end));
                return ['idx' => $i, 'tail' => $tail];
            }
        }
        return null;
    };

    // Localizar "Code" (header pode estar standalone ou ter SKUs inline)
    $codeSec = $findSection('/^Code(\s|$)/i');
    if (!$codeSec) return [];

    // Coletar SKUs: começa pelo tail da linha "Code", continua até a próxima linha não-SKU
    $skus = [];
    $skuRegex = '/^N[A-Z]+-[A-Z]+-\d+-\d+$/';
    foreach (preg_split('/\s+/', $codeSec['tail']) as $p) {
        if ($p !== '' && preg_match($skuRegex, $p)) $skus[] = $p;
    }
    $k = $codeSec['idx'] + 1;
    while ($k < $n) {
        $line = trim($lines[$k]);
        if ($line === '') { $k++; continue; }
        if (preg_match($stopPattern, $line)) break;
        $foundAny = false;
        foreach (preg_split('/\s+/', $line) as $p) {
            if ($p !== '' && preg_match($skuRegex, $p)) { $skus[] = $p; $foundAny = true; }
        }
        if (!$foundAny) break;
        $k++;
    }
    $numSkus = count($skus);
    if ($numSkus === 0) return [];

    $startSearch = $k;

    // Localizar cada seção, sempre depois das SKUs
    $sections = [];
    foreach ($headerKeys as $key => $pat) {
        $sec = $findSection($pat, $startSearch);
        if ($sec) $sections[$key] = $sec;
    }

    // Ler $count valores a partir da linha $startIdx (pulando a própria), aproveitando $tail
    $readVals = function (int $startIdx, int $count, string $tail) use ($lines, $n, $stopPattern): array {
        $vals = [];
        $appendTokens = function (string $line) use (&$vals, $count) {
            foreach (preg_split('/\s+/', $line) as $p) {
                if ($p === '') continue;
                $vals[] = ($p === '-') ? '' : $p;
                if (count($vals) >= $count) return true;
            }
            return false;
        };
        if ($tail !== '' && $appendTokens($tail)) return array_slice($vals, 0, $count);

        $k = $startIdx + 1;
        while (count($vals) < $count && $k < $n) {
            $line = trim($lines[$k]);
            if ($line === '') { $k++; continue; }
            if (preg_match($stopPattern, $line)) break;
            if ($appendTokens($line)) break;
            $k++;
        }
        return array_slice($vals, 0, $count);
    };

    $cols = [];
    foreach (['thickness', 'a', 'b', 'c', 'unit', 'coverage', 'pet'] as $key) {
        $cols[$key] = isset($sections[$key]) ? $readVals($sections[$key]['idx'], $numSkus, $sections[$key]['tail']) : [];
        while (count($cols[$key]) < $numSkus) $cols[$key][] = '';
    }

    // Só incluir colunas opcionais (c, coverage) se houver pelo menos 1 valor não-vazio
    $hasC        = (bool) array_filter($cols['c'], fn($v) => $v !== '' && $v !== null);
    $hasCoverage = (bool) array_filter($cols['coverage'], fn($v) => $v !== '' && $v !== null);

    $cast = fn($v) => ($v === '' || $v === null) ? '' : (is_numeric($v) ? (int) $v : (string) $v);
    $specs = [];
    foreach ($skus as $idx => $sku) {
        $row = [
            'code'           => $sku,
            'thickness'      => $cast($cols['thickness'][$idx]),
            'a'              => $cast($cols['a'][$idx]),
            'b'              => $cast($cols['b'][$idx]),
        ];
        if ($hasC) $row['c'] = $cast($cols['c'][$idx]);
        $row['pieces_per_box'] = $cast($cols['unit'][$idx]);
        if ($hasCoverage) $row['coverage_area'] = (string) $cols['coverage'][$idx];
        $row['pet_bottles'] = $cast($cols['pet'][$idx]);
        $specs[] = $row;
    }
    return $specs;
}

$normalizeSubtitle = function (string $s): string {
    $s = trim($s);
    $s = preg_replace('/[•·]/u', '-', $s);
    $s = preg_replace('/\s*-\s*-\s*-\s*/u', ' - ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};

/**
 * Captura o título da NUVEM em 2 formatos:
 *   "CLASSIC CLOUD SQUARE"   ou  "CLOUD FORM SQUARE"   ou  "CLOUD FORM FLY 01"
 *   "CLOUD NESS SQUARE/RECTANGLE"  (com barra)
 *
 * Retorna [linha, shape, fullName] ou null.
 */
$validLines = ['CLASSIC', 'FORM', 'NESS', 'SOFTFELT'];
$parseTitle = function (string $rawLine) use ($validLines): ?array {
    $rawLine = trim($rawLine);
    if ($rawLine === '') return null;

    // Padrão 1: "CLASSIC CLOUD SHAPE" (line first)
    if (preg_match('/^([A-Z]+)\s+CLOUD\s+([A-Z][A-Z0-9 \/]*)$/u', $rawLine, $m)) {
        $line  = strtoupper(trim($m[1]));
        $shape = strtoupper(trim($m[2]));
        if (in_array($line, $validLines, true)) {
            return [$line, $shape, "CLOUD {$line} {$shape}"];
        }
    }
    // Padrão 2: "CLOUD LINE SHAPE [NN]" — line vem depois de CLOUD
    if (preg_match('/^CLOUD\s+([A-Z]+)\s+([A-Z][A-Z0-9 \/]*)$/u', $rawLine, $m)) {
        $line  = strtoupper(trim($m[1]));
        $shape = strtoupper(trim($m[2]));
        // Remove "01", "02" no final do shape (apenas Form Fly)
        $shape = preg_replace('/\s+\d+$/', '', $shape);
        if (in_array($line, $validLines, true)) {
            return [$line, $shape, "CLOUD {$line} {$shape}"];
        }
    }
    return null;
};

/* ---------- Parsing por arquivo ---------- */

$products = [];

foreach ($files as $file) {
    $raw = (string) file_get_contents($file);
    if ($raw === '') continue;

    $pages = explode("\f", $raw);
    fwrite(STDOUT, "Processando " . basename($file) . " ({" . count($pages) . "} páginas)...\n");

    for ($idx = 0; $idx < count($pages); $idx++) {
        $text = $pages[$idx];
        $lines = preg_split('/\R/u', $text);
        $cleanLines = [];
        foreach ($lines as $line) {
            $l = trim($line, " \t\n\r\0\x0B\x0C");
            if ($l !== '') $cleanLines[] = $l;
        }
        if (count($cleanLines) < 2) continue;

        $first = $cleanLines[0];
        $second = $cleanLines[1];

        $parsed = $parseTitle($first);
        if ($parsed === null) continue;
        [$line, $shape, $baseName] = $parsed;

        if (stripos($text, 'Code') !== false && stripos($text, 'Thickness') !== false) continue;
        if (strlen($second) < 2 || strlen($second) > 60) continue;

        $subtitle = $normalizeSubtitle($second);
        $name = $baseName . ' - ' . $subtitle;
        $slug = slugifyName($name);

        // Specs ficam na próxima página (formato column-major do pdftotext sem -layout)
        $specsText = $pages[$idx + 1] ?? '';
        $specs = parseSpecsColumnMajor($specsText);
        if (empty($specs)) continue;

        $hasModulation = (stripos($specsText, 'Modulation suggestions') !== false) ? 1 : 0;

        $products[$slug] = [
            'name'           => $name,
            'subtitle'       => $subtitle,
            'line_name'      => $line,
            'shape'          => $shape,
            'hero_page'      => $idx + 1,         // 1-indexed
            'specs_page'     => $idx + 2,
            'specs'          => $specs,
            'slug'           => $slug,
            'pdf_file'       => basename($file),
            'has_modulation' => $hasModulation,
        ];
    }
}

fwrite(STDOUT, "\nEncontrados " . count($products) . " produtos únicos no NUVEM.\n\n");

/* ---------- Persistir ---------- */

$inserted = 0; $updated = 0;
foreach ($products as $slug => $p) {
    $firstCode = $p['specs'][0]['code'];
    $parts = explode('-', $firstCode);
    $mainSku = implode('-', array_slice($parts, 0, 3));

    $shortDesc = match ($p['shape']) {
        'SQUARE'                => 'Nuvem acústica quadrada suspensa.',
        'RECTANGULAR'           => 'Nuvem acústica retangular suspensa.',
        'HEXAGONAL'             => 'Nuvem acústica hexagonal suspensa.',
        'TRIANGULAR'            => 'Nuvem acústica triangular suspensa.',
        'CIRCULAR'              => 'Nuvem acústica circular suspensa.',
        'ORGANIC'               => 'Nuvem acústica em forma orgânica.',
        'FLY'                   => 'Nuvem acústica com perfil aerodinâmico.',
        default                  => 'Nuvem acústica Trisoft.',
    };

    $description = "Covered with a flexible PET membrane finish.\n\n"
        . "Solid Colors: Chose from avaiable colors or select your pantone color. See QR CODE for avaiable colors.\n"
        . "Upcycling: All our products are 100% recyclable.";

    $specsJson = json_encode($p['specs'], JSON_UNESCAPED_UNICODE);

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
        fwrite(STDOUT, "↻ Atualizado: {$p['name']} ({$mainSku}, " . count($p['specs']) . " specs, mod=" . $p['has_modulation'] . ")\n");
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
        fwrite(STDOUT, "+ Inserido: {$p['name']} ({$mainSku}, " . count($p['specs']) . " specs, mod=" . $p['has_modulation'] . ")\n");
    }

    // Categorias
    $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
        ->execute([$productId, $nuvensCatId]);
    if (isset($lineCategories[$p['line_name']])) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$productId, $lineCategories[$p['line_name']]]);
    }
    if (isset($shapeCategories[$p['shape']])) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$productId, $shapeCategories[$p['shape']]]);
    }

    // Settings: hero page, has_modulation, pdf file (para extract usar)
    $upsert = function (string $key, string $value) use ($pdo) {
        $pdo->prepare(
            "INSERT INTO settings (`key`, `value`, `type`) VALUES (?, ?, 'string')
             ON DUPLICATE KEY UPDATE value = VALUES(value)"
        )->execute([$key, $value]);
    };
    $upsert("_baffles_hero_page_{$productId}",  (string) $p['hero_page']);
    $upsert("_has_modulation_{$productId}",     (string) $p['has_modulation']);
    $upsert("_pdf_source_{$productId}",         $p['pdf_file']);
}

fwrite(STDOUT, "\n✅ Inseridos: {$inserted} | Atualizados: {$updated}\n");
