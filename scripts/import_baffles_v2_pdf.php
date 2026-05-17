<?php

declare(strict_types=1);

/**
 * Importa produtos do catálogo BAFFLE — pasta catalogos/SEPARADOS/BAFFLE/.
 *
 * Substitui o import_baffles_pdf.php original (que usava um PDF concatenado)
 * com suporte a múltiplos PDFs (1 PDF por shape/linha), permitindo extração
 * correta de imagens de dimensão e modulação por produto.
 *
 * Títulos: "BAFFLE CLASSIC STRAIGHT", "BAFFLE NESS STRAIGHT", "BAFFLE FORM ARC", etc.
 * SKU prefix: B[A-Z]+ (BC, BN, BF, BD, etc).
 *
 * Usa texto extraído COM `-layout`:
 *   pdftotext -layout -enc UTF-8 catalogos/SEPARADOS/BAFFLE/baffles_*.pdf .../
 *   php scripts/import_baffles_v2_pdf.php /tmp/baffles/*.txt [--reset]
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
        foreach (glob($a) as $f) $files[] = $f;
    }
}
if ($files === []) {
    fwrite(STDERR, "Uso: php scripts/import_baffles_v2_pdf.php <file.txt|dir|glob> [--reset]\n");
    exit(1);
}

/* ---------- Reset ---------- */

if ($reset) {
    fwrite(STDOUT, "⚠️  --reset: apagando produtos da categoria 'baffles'...\n");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("
        DELETE FROM product_images WHERE product_id IN (
            SELECT pc.product_id FROM product_categories pc
              JOIN categories c ON c.id = pc.category_id
             WHERE c.slug = 'baffles'
        )
    ");
    $pdo->exec("
        DELETE FROM cart_items WHERE product_id IN (
            SELECT pc.product_id FROM product_categories pc
              JOIN categories c ON c.id = pc.category_id
             WHERE c.slug = 'baffles'
        )
    ");
    $pdo->exec("
        DELETE FROM products WHERE id IN (
            SELECT pc.product_id FROM product_categories pc
              JOIN categories c ON c.id = pc.category_id
             WHERE c.slug = 'baffles'
        )
    ");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
}

/* ---------- Helpers ---------- */

function ensureCategoryBaffles(\PDO $pdo, string $slug, string $name, ?int $parentId = null): int
{
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if ($row) return (int) $row['id'];
    $pdo->prepare("INSERT INTO categories (parent_id, name, slug, is_active, sort_order) VALUES (?, ?, ?, 1, 0)")
        ->execute([$parentId, $name, $slug]);
    return (int) $pdo->lastInsertId();
}

function slugifyBaffles(string $s): string
{
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $t = preg_replace('/[^A-Za-z0-9\s\-]/', '', $t) ?? $t;
    $t = preg_replace('/[\s\-]+/', '-', $t) ?? $t;
    return strtolower(trim($t, '-'));
}

/* ---------- Categorias raiz e sub-categorias por família ---------- */

$bafflesId = ensureCategoryBaffles($pdo, 'baffles', 'Baffles');

/* ---------- Parser ---------- */

// Aceita qualquer SKU [A-Z]{1,3}-[A-Z]+-\d+-\d+ no início da linha
// Captura SKU + resto da linha (valores) para split posterior.
$skuRegex = '/^\s*([A-Z]{1,3}-[A-Z]+-\d+-\d+)(\s.+)?$/u';

// Pattern de título: linha curta em UPPERCASE; muitas opções possíveis
$nonTitlePatterns = [
    '/^Code\b/i', '/^Thickness/i', '/^Made with/i', '/^Covered with/i',
    '/^Solid Colors/i', '/^Prints/i', '/^High Relief/i', '/^Upcycling/i',
    '/^Modulation/i', '/^Installation/i', '/^NRC/i', '/^Blocks/i',
    '/^Colors\b/i',
];

$normalizeSubtitle = function (string $s): string {
    $s = trim($s);
    $s = preg_replace('/[•·]/u', '-', $s);
    $s = preg_replace('/\s*-\s*-\s*-\s*/u', ' - ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};

$isTitleLine = function (string $line) use ($nonTitlePatterns): bool {
    $line = trim($line);
    if ($line === '' || strlen($line) < 3 || strlen($line) > 80) return false;
    // Deve ser uppercase
    if (preg_match('/[a-z]/', $line)) return false;
    // Não pode começar com texto descritivo conhecido
    foreach ($nonTitlePatterns as $p) {
        if (preg_match($p, $line)) return false;
    }
    // Deve ter pelo menos 1 letra
    if (!preg_match('/[A-Z]/', $line)) return false;
    return true;
};

$parseSpecsLine = function (string $sl) use ($skuRegex): ?array {
    if (!preg_match($skuRegex, $sl, $m)) return null;
    $code = $m[1];
    $rest = isset($m[2]) ? trim($m[2]) : '';

    // Divide o resto por 2+ espaços (preserva "0,25 m²" e "N25 E 10" inteiros)
    $tokens = preg_split('/\s{2,}/', $rest) ?: [];
    $tokens = array_values(array_filter(array_map('trim', $tokens), fn($t) => $t !== ''));

    $row = [
        'code' => $code,
        'thickness' => '', 'a' => '', 'b' => '', 'c' => '', 'd' => '',
        'pieces_per_box' => '', 'coverage_area' => '', 'pet_bottles' => '',
    ];

    // Identifica coverage_area (contém m²) — divisor natural da tabela
    $coverageIdx = null;
    foreach ($tokens as $i => $tok) {
        if (preg_match('/m[²2]/u', $tok)) {
            $coverageIdx = $i;
            $row['coverage_area'] = preg_replace('/\s*m[²2]\s*/u', ' m²', trim($tok));
            break;
        }
    }

    // Separa tokens em ANTES e DEPOIS do coverage_area, preservando texto não-numérico
    $tokensBefore = [];
    $tokensAfter  = [];
    foreach ($tokens as $i => $tok) {
        if ($i === $coverageIdx) continue;
        if ($coverageIdx === null || $i < $coverageIdx) {
            $tokensBefore[] = $tok;
        } else {
            $tokensAfter[] = $tok;
        }
    }

    // Cast: número puro → int; "1,5" → string; "N25 E 10" → string
    $cast = function ($v) {
        $v = trim((string) $v);
        if ($v === '') return '';
        if (preg_match('/^\d+$/', $v)) return (int) $v;
        return $v;
    };

    // 1º token antes do m² = thickness (pode ser "9" ou "N25 E 10")
    if (count($tokensBefore) >= 1) $row['thickness'] = $cast($tokensBefore[0]);
    $dims = array_slice($tokensBefore, 1);

    // Heurística: se o último valor numérico antes do m² for pequeno (<=100) E houver
    // ao menos 2 outros valores numéricos antes dele, provavelmente é Unit (Peças/cx)
    if (count($dims) >= 3 && preg_match('/^\d+$/', (string) end($dims)) && (int) end($dims) <= 100) {
        $row['pieces_per_box'] = (int) array_pop($dims);
    }

    // Atribui dimensões em ordem A, B, C, D (preserva textuais)
    $dimKeys = ['a', 'b', 'c', 'd'];
    foreach ($dims as $idx => $val) {
        if (isset($dimKeys[$idx])) $row[$dimKeys[$idx]] = $cast($val);
    }

    // Após o m² = PET Bottles (preserva texto se houver)
    if ($tokensAfter !== []) $row['pet_bottles'] = $cast($tokensAfter[0]);

    return $row;
};

/* ---------- Parsing por arquivo ---------- */

$products = [];

foreach ($files as $file) {
    $raw = (string) file_get_contents($file);
    if ($raw === '') continue;

    $pages = explode("\f", $raw);
    $pdfName = preg_replace('/\.txt$/', '.pdf', basename($file));
    fwrite(STDOUT, "Processando " . basename($file) . " (" . count($pages) . " páginas)...\n");

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

        if (!$isTitleLine($first)) continue;
        if (!$isTitleLine($second) && strlen($second) > 60) continue;

        // Página de hero NÃO tem "Code"/"Thickness"
        if (stripos($text, 'Code') !== false && stripos($text, 'Thickness') !== false) continue;

        $title = trim(preg_replace('/\s+/u', ' ', $first) ?? $first);

        if (preg_match('/^\d+$/', trim($second))) {
            $subtitle = '';
            $name = $title;
        } else {
            $subtitle = $normalizeSubtitle($second);
            $name = $title . ' - ' . $subtitle;
        }
        $slug = slugifyBaffles($name);

        // Specs vêm na página seguinte
        $specsText = $pages[$idx + 1] ?? '';
        $specsLines = preg_split('/\R/u', $specsText);
        $specs = [];
        foreach ($specsLines as $sl) {
            $row = $parseSpecsLine($sl);
            if ($row !== null) $specs[] = $row;
        }
        if (empty($specs)) continue;

        $hasModulation = (stripos($specsText, 'Modulation suggestions') !== false) ? 1 : 0;

        $products[$slug] = [
            'name'           => $name,
            'subtitle'       => $subtitle,
            'title'          => $title,
            'hero_page'      => $idx + 1,
            'specs_page'     => $idx + 2,
            'specs'          => $specs,
            'slug'           => $slug,
            'pdf_file'       => $pdfName,
            'has_modulation' => $hasModulation,
        ];
    }
}

fwrite(STDOUT, "\nEncontrados " . count($products) . " produtos únicos em BAFFLE.\n\n");

/* ---------- Persistir ---------- */

$inserted = 0; $updated = 0; $skipped = 0;
foreach ($products as $slug => $p) {
    $firstCode = $p['specs'][0]['code'];
    $parts = explode('-', $firstCode);
    $mainSku = implode('-', array_slice($parts, 0, 3));

    // Se outro produto já usou este mainSku, gera variante com base no slug
    $exists = $pdo->prepare("SELECT slug FROM products WHERE sku = ? LIMIT 1");
    $exists->execute([$mainSku]);
    $existingSlug = $exists->fetchColumn();
    if ($existingSlug && $existingSlug !== $slug) {
        // Adiciona sufixo com hash curto baseado no slug pra desambiguar
        $mainSku .= '-' . strtoupper(substr(md5($slug), 0, 4));
    }

    // Sub-categoria por família (slug do título)
    $familySlug = 'paredes-' . slugifyBaffles($p['title']);
    $familyCatId = ensureCategoryBaffles($pdo, $familySlug, ucwords(strtolower($p['title'])), $bafflesId);

    $description = "Baffle acústico Trisoft.\n\n"
        . "Covered with a flexible PET membrane finish.\n"
        . "Upcycling: All our products are 100% recyclable.";

    $specsJson = json_encode($p['specs'], JSON_UNESCAPED_UNICODE);
    $shortDesc = "Baffle acústico suspenso — " . strtolower($p['title']) . ".";

    try {
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
        ->execute([$productId, $bafflesId]);
    $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
        ->execute([$productId, $familyCatId]);

    // Settings: hero_page, has_modulation, pdf_source
    $upsert = function (string $key, string $value) use ($pdo) {
        $pdo->prepare(
            "INSERT INTO settings (`key`, `value`, `type`) VALUES (?, ?, 'string')
             ON DUPLICATE KEY UPDATE value = VALUES(value)"
        )->execute([$key, $value]);
    };
    $upsert("_baffles_hero_page_{$productId}", (string) $p['hero_page']);
    $upsert("_has_modulation_{$productId}",    (string) $p['has_modulation']);
    $upsert("_pdf_source_{$productId}",        $p['pdf_file']);
    } catch (\Throwable $e) {
        $skipped++;
        fwrite(STDERR, "⚠️  Pulado {$p['name']}: " . $e->getMessage() . "\n");
    }
}

fwrite(STDOUT, "\n✅ Inseridos: {$inserted} | Atualizados: {$updated} | Pulados: {$skipped}\n");
