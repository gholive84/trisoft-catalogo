<?php

declare(strict_types=1);

/**
 * Renderiza imagens do PDF para cada produto importado, usando ImageMagick.
 *
 * Pré-requisitos:
 *   - Servidor com `magick` (ImageMagick) + `gs` (Ghostscript) — confirmado no SiteGround
 *   - import_baffles_pdf.php (ou variante) já rodou, deixando o número da
 *     página de hero em `settings._baffles_hero_page_<product_id>`
 *
 * Gera duas imagens por produto:
 *   1. HERO     → <slug>.jpg (1920x1080) renderizado da página ímpar (hero)
 *      Salvo em products.hero_image_path + product_images (is_main=1)
 *   2. MODULAÇÃO → <slug>-modulation.png (faixa horizontal) crop da página de
 *      specs (hero_page + 1), região onde aparecem os ícones de modulação.
 *      Salvo em products.modulation_image_path.
 *
 * Uso:
 *   php scripts/extract_pdf_images.php /tmp/baffles.pdf
 *
 * Flags:
 *   --density=200    DPI de render
 *   --quality=85     JPEG quality
 *   --limit=N        processa só N produtos (debug)
 *   --force          re-renderiza mesmo que já exista
 *   --skip-hero      pula geração de hero (só modulações)
 *   --skip-modulation pula geração de modulação (só hero)
 *
 * Mod modulação:
 *   --mod-crop="5%x10%+0+30%"
 *       formato magick (-crop) relativo: width x height + x_offset + y_offset
 *       (% da página). Default cobre a faixa de "Modulation suggestions" no
 *       layout padrão Trisoft (página A4 com hero+specs).
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
$pdo = Database::connection();

/* ------------------------- Args ------------------------- */

$args = $argv;
array_shift($args);
$pdfPath  = null;
$density  = 250;
$quality  = 85;
$limit    = 0;
$force    = false;
$skipHero = false;
$skipMod  = false;
$modCrop  = '100%x16%+0%+42%'; // default: faixa "Modulation suggestions" no layout Trisoft padrão
foreach ($args as $a) {
    if ($a === '--force')             { $force = true; continue; }
    if ($a === '--skip-hero')         { $skipHero = true; continue; }
    if ($a === '--skip-modulation')   { $skipMod = true; continue; }
    if (preg_match('/^--density=(\d+)$/', $a, $m))  { $density = (int) $m[1]; continue; }
    if (preg_match('/^--quality=(\d+)$/', $a, $m))  { $quality = (int) $m[1]; continue; }
    if (preg_match('/^--limit=(\d+)$/', $a, $m))    { $limit   = (int) $m[1]; continue; }
    if (preg_match('/^--mod-crop=(.+)$/', $a, $m))  { $modCrop = $m[1]; continue; }
    if ($pdfPath === null) { $pdfPath = $a; }
}
if ($pdfPath === null || !file_exists($pdfPath)) {
    fwrite(STDERR, "Uso: php scripts/extract_pdf_images.php <file.pdf> [flags]\n");
    fwrite(STDERR, "Flags: --density=N --quality=N --limit=N --force --skip-hero --skip-modulation --mod-crop=WxH+X+Y\n");
    exit(1);
}

/* ------------------------- ImageMagick ------------------------- */

exec('which magick 2>/dev/null', $out, $rc);
$magickBin = ($rc === 0 && !empty($out)) ? trim($out[0]) : 'magick';
exec("$magickBin -version", $verOut, $rc);
if ($rc !== 0) {
    fwrite(STDERR, "❌ ImageMagick não disponível.\n");
    exit(1);
}
fwrite(STDOUT, "ImageMagick: " . ($verOut[0] ?? 'unknown') . "\n");

/* ------------------------- Setup ------------------------- */

$uploadDir = BASE_PATH . '/public/uploads/products';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

$stmt = $pdo->query(
    "SELECT p.id, p.slug, p.name, s.value AS page
       FROM products p
       JOIN settings s ON s.`key` = CONCAT('_baffles_hero_page_', p.id)
      WHERE p.sku REGEXP '^B[A-Z]-'
   ORDER BY CAST(s.value AS UNSIGNED) ASC"
);
$products = $stmt->fetchAll();
fwrite(STDOUT, "Produtos a processar: " . count($products) . "\n\n");

/* ------------------------- Helpers ------------------------- */

/**
 * Renderiza uma página inteira do PDF como JPG (com extent fill-cover).
 */
function renderHero(string $magickBin, string $pdfPath, int $pageIdx, int $density, int $quality, string $outFile): bool
{
    $cmd = sprintf(
        '%s -density %d %s[%d] -background white -alpha remove -alpha off ' .
        '-resize 1920x1080^ -gravity center -extent 1920x1080 ' .
        '-quality %d %s 2>&1',
        escapeshellcmd($magickBin), $density, escapeshellarg($pdfPath), $pageIdx,
        $quality, escapeshellarg($outFile)
    );
    exec($cmd, $output, $rc);
    return $rc === 0 && file_exists($outFile) && filesize($outFile) > 1000;
}

/**
 * Renderiza uma região específica de uma página (crop).
 * $cropSpec: formato "WxH+X+Y" — pode usar % nas dimensões.
 *
 * Implementação:
 *   1. Renderiza página completa
 *   2. Pega dimensões reais via identify
 *   3. Computa crop em PIXELS (não usa % do magick — buggy em offsets Y)
 */
function renderModulation(string $magickBin, string $pdfPath, int $pageIdx, int $density, string $cropSpec, string $outFile): bool
{
    $tmpPage = $outFile . '.fullpage.png';
    $cmd1 = sprintf(
        '%s -density %d %s[%d] -background white -alpha remove -alpha off %s 2>&1',
        escapeshellcmd($magickBin), $density, escapeshellarg($pdfPath), $pageIdx,
        escapeshellarg($tmpPage)
    );
    exec($cmd1, $_, $rc1);
    if ($rc1 !== 0 || !file_exists($tmpPage)) return false;

    // Identifica dimensões reais da página renderizada
    exec(sprintf('%s -format "%%w %%h" %s 2>&1',
        escapeshellcmd($magickBin), escapeshellarg($tmpPage)
    ), $idOut, $rcId);
    if ($rcId !== 0 || empty($idOut)) {
        @unlink($tmpPage);
        return false;
    }
    [$pageW, $pageH] = array_map('intval', explode(' ', trim($idOut[0])));

    // Parseia $cropSpec: WxH+X+Y, com possíveis %
    if (!preg_match('/^(\d+%?)x(\d+%?)\+(\d+%?)\+(\d+%?)$/', $cropSpec, $m)) {
        @unlink($tmpPage);
        return false;
    }
    $resolve = function (string $v, int $base): int {
        if (str_ends_with($v, '%')) {
            return (int) round(((float) rtrim($v, '%')) * $base / 100);
        }
        return (int) $v;
    };
    $cw = $resolve($m[1], $pageW);
    $ch = $resolve($m[2], $pageH);
    $cx = $resolve($m[3], $pageW);
    $cy = $resolve($m[4], $pageH);

    $pixelCrop = "{$cw}x{$ch}+{$cx}+{$cy}";

    $cmd2 = sprintf(
        '%s %s -crop %s +repage -bordercolor white -border 10x10 %s 2>&1',
        escapeshellcmd($magickBin), escapeshellarg($tmpPage),
        escapeshellarg($pixelCrop), escapeshellarg($outFile)
    );
    exec($cmd2, $output2, $rc2);
    @unlink($tmpPage);

    return $rc2 === 0 && file_exists($outFile) && filesize($outFile) > 500;
}

/* ------------------------- Loop ------------------------- */

$processed = 0;
$heroDone = 0;
$modDone = 0;

foreach ($products as $p) {
    if ($limit > 0 && $processed >= $limit) break;

    $page      = (int) $p['page'];
    $slug      = $p['slug'];
    $productId = (int) $p['id'];

    // ---- HERO ----
    if (!$skipHero) {
        $heroFile = $uploadDir . '/' . $slug . '.jpg';
        if (file_exists($heroFile) && !$force) {
            fwrite(STDOUT, "· hero existe:        {$slug}.jpg\n");
        } else {
            $tmpHero = $heroFile . '.tmp.jpg';
            if (renderHero($magickBin, $pdfPath, $page - 1, $density, $quality, $tmpHero)) {
                rename($tmpHero, $heroFile);
                $heroBase = basename($heroFile);
                $pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
                    ->execute([$heroBase, $productId]);
                $check = $pdo->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
                $check->execute([$productId]);
                if (!$check->fetch()) {
                    $pdo->prepare(
                        "INSERT INTO product_images (product_id, file_path, alt_text, is_main, sort_order)
                         VALUES (?, ?, ?, 1, 0)"
                    )->execute([$productId, $heroBase, $p['name']]);
                } else {
                    $pdo->prepare(
                        "UPDATE product_images SET file_path = ?, alt_text = ?
                          WHERE product_id = ? AND is_main = 1 LIMIT 1"
                    )->execute([$heroBase, $p['name'], $productId]);
                }
                fwrite(STDOUT, "✓ hero renderizado:  {$slug}.jpg (" . round(filesize($heroFile) / 1024) . " KB)\n");
                $heroDone++;
            } else {
                fwrite(STDERR, "❌ hero falhou:       {$slug} (page {$page})\n");
                @unlink($tmpHero);
            }
        }
    }

    // ---- MODULATION ----
    if (!$skipMod) {
        $modFile = $uploadDir . '/' . $slug . '-modulation.png';
        if (file_exists($modFile) && !$force) {
            fwrite(STDOUT, "· mod existe:         {$slug}-modulation.png\n");
        } else {
            $modPage = $page + 1; // página de specs (par)
            $tmpMod  = $modFile . '.tmp.png';
            if (renderModulation($magickBin, $pdfPath, $modPage - 1, $density, $modCrop, $tmpMod)) {
                rename($tmpMod, $modFile);
                $modBase = basename($modFile);
                $pdo->prepare("UPDATE products SET modulation_image_path = ? WHERE id = ?")
                    ->execute([$modBase, $productId]);
                fwrite(STDOUT, "✓ modulação:         {$modBase} (" . round(filesize($modFile) / 1024) . " KB)\n");
                $modDone++;
            } else {
                fwrite(STDERR, "❌ modulação falhou:  {$slug} (page {$modPage})\n");
                @unlink($tmpMod);
            }
        }
    }

    $processed++;
}

fwrite(STDOUT, "\n✅ Concluído. Hero: {$heroDone} · Modulação: {$modDone}\n");
