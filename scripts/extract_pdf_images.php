<?php

declare(strict_types=1);

/**
 * Renderiza a página de hero de cada produto do PDF como imagem JPG
 * usando ImageMagick (`magick`), e associa ao produto.
 *
 * Pré-requisito: import_baffles_pdf.php já rodou (deixa o número da
 * página em `settings._baffles_hero_page_<product_id>`).
 *
 * Uso:
 *   php scripts/extract_baffles_images.php /tmp/baffles.pdf
 *
 * Opcional:
 *   --density=200   DPI de render (default 200; alto = imagem grande)
 *   --quality=85    JPEG quality (default 85)
 *   --limit=10      processa só N produtos (debug)
 *   --force         re-renderiza mesmo que já exista
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
$pdo = Database::connection();

$args = $argv;
array_shift($args);
$pdfPath = null;
$density = 200;
$quality = 85;
$limit   = 0;
$force   = false;
foreach ($args as $a) {
    if ($a === '--force') { $force = true; continue; }
    if (preg_match('/^--density=(\d+)$/', $a, $m)) { $density = (int) $m[1]; continue; }
    if (preg_match('/^--quality=(\d+)$/', $a, $m)) { $quality = (int) $m[1]; continue; }
    if (preg_match('/^--limit=(\d+)$/', $a, $m))   { $limit = (int) $m[1]; continue; }
    if ($pdfPath === null) { $pdfPath = $a; }
}
if ($pdfPath === null || !file_exists($pdfPath)) {
    fwrite(STDERR, "Uso: php scripts/extract_baffles_images.php <baffles.pdf>\n");
    exit(1);
}

// Confirma ImageMagick disponível
exec('which magick 2>/dev/null', $out, $rc);
$magickBin = ($rc === 0 && !empty($out)) ? trim($out[0]) : 'magick';
exec("$magickBin -version", $verOut, $rc);
if ($rc !== 0) {
    fwrite(STDERR, "❌ ImageMagick não disponível.\n");
    exit(1);
}
fwrite(STDOUT, "ImageMagick: " . ($verOut[0] ?? 'unknown') . "\n");

// Pasta de destino
$uploadDir = BASE_PATH . '/public/uploads/products';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

// Carrega produtos + página de hero (do settings)
$stmt = $pdo->query(
    "SELECT p.id, p.slug, p.name, s.value AS page
       FROM products p
       JOIN settings s ON s.`key` = CONCAT('_baffles_hero_page_', p.id)
      WHERE p.sku REGEXP '^B[A-Z]-'
   ORDER BY CAST(s.value AS UNSIGNED) ASC"
);
$products = $stmt->fetchAll();
fwrite(STDOUT, "Produtos a processar: " . count($products) . "\n\n");

$count = 0;
foreach ($products as $p) {
    if ($limit > 0 && $count >= $limit) break;

    $page = (int) $p['page'];
    $slug = $p['slug'];
    $productId = (int) $p['id'];

    $outFile = $uploadDir . '/' . $slug . '.jpg';
    if (file_exists($outFile) && !$force) {
        fwrite(STDOUT, "· Pulando (já existe): {$slug}.jpg\n");
        $count++;
        continue;
    }

    // ImageMagick: páginas no PDF são 0-indexed
    $pageIdx = $page - 1;
    $tmpFile = $outFile . '.tmp.jpg';

    // Renderiza a página, faz auto-crop pra remover bordas brancas, redimensiona pra max 1600x900
    $cmd = sprintf(
        '%s -density %d "%s[%d]" -background white -alpha remove -alpha off ' .
        '-resize 1920x1080^ -gravity center -extent 1920x1080 ' .
        '-quality %d "%s" 2>&1',
        escapeshellcmd($magickBin),
        $density,
        escapeshellarg($pdfPath),
        $pageIdx,
        $quality,
        escapeshellarg($tmpFile)
    );
    // (escapeshellarg adicionado nos paths que precisam — comando montado com sprintf)
    $cmd = sprintf(
        '%s -density %d %s[%d] -background white -alpha remove -alpha off ' .
        '-resize 1920x1080^ -gravity center -extent 1920x1080 ' .
        '-quality %d %s 2>&1',
        escapeshellcmd($magickBin),
        $density,
        escapeshellarg($pdfPath),
        $pageIdx,
        $quality,
        escapeshellarg($tmpFile)
    );

    $output = [];
    exec($cmd, $output, $rc);
    if ($rc !== 0 || !file_exists($tmpFile) || filesize($tmpFile) < 1000) {
        fwrite(STDERR, "❌ Falha p/ {$slug} (page {$page}): " . implode("\n", $output) . "\n");
        @unlink($tmpFile);
        continue;
    }

    rename($tmpFile, $outFile);
    $size = filesize($outFile);
    fwrite(STDOUT, "✓ {$slug}.jpg (page {$page}, " . round($size / 1024) . " KB)\n");

    // Atualiza products.hero_image_path + insere em product_images (main image)
    $relPath = basename($outFile);
    $pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
        ->execute([$relPath, $productId]);

    $check = $pdo->prepare("SELECT id FROM product_images WHERE product_id = ? LIMIT 1");
    $check->execute([$productId]);
    if (!$check->fetch()) {
        $pdo->prepare(
            "INSERT INTO product_images (product_id, file_path, alt_text, is_main, sort_order)
             VALUES (?, ?, ?, 1, 0)"
        )->execute([$productId, $relPath, $p['name']]);
    } else {
        $pdo->prepare(
            "UPDATE product_images
                SET file_path = ?, alt_text = ?
              WHERE product_id = ? AND is_main = 1
              LIMIT 1"
        )->execute([$relPath, $p['name'], $productId]);
    }

    $count++;
}

fwrite(STDOUT, "\n✅ Concluído. {$count} imagens processadas.\n");
