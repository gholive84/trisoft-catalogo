<?php

declare(strict_types=1);

/**
 * Renderiza imagens do PDF para cada produto importado, usando ImageMagick.
 *
 * Pré-requisitos:
 *   - magick (ImageMagick) + gs (Ghostscript) — confirmado no SiteGround
 *   - import_*_pdf.php já rodou e deixou em settings:
 *     - _baffles_hero_page_<id>   → número da página de hero
 *     - _has_modulation_<id>      → 0 ou 1 (opcional; default 1 para retrocompat)
 *     - _pdf_source_<id>          → nome do PDF (opcional; quando há múltiplos PDFs)
 *
 * Gera até 3 imagens por produto:
 *   1. HERO        → <slug>.jpg (3200×1800 q95) renderizado da página ímpar
 *   2. DIMENSIONS  → <slug>-dimensions.png — crop horizontal acima da tabela
 *      (desenho técnico com cotas "A"/"B" — quando presente)
 *   3. MODULATION  → <slug>-modulation.png — só gerada se _has_modulation=1
 *
 * Uso (catálogo único, como baffles):
 *   php scripts/extract_pdf_images.php /tmp/baffles.pdf
 *
 * Uso (múltiplos PDFs, como clouds — passa diretório):
 *   php scripts/extract_pdf_images.php --pdf-dir=/tmp/clouds
 *
 *   Procura cada produto em <pdf-dir>/<_pdf_source_<id>>.
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
$pdfPath  = null;
$pdfDir   = null;
$density  = 400;
$quality  = 95;
$heroW    = 3200;
$heroH    = 1800;
$limit    = 0;
$force    = false;
$skipHero = false;
$skipMod  = false;
$skipDim  = false;
$modCrop  = '100%x16%+0%+42%';
$dimCrop  = '100%x42%+0%+22%'; // entre subtitle e tabela: cobre B-arrow no topo, quadrado, A-arrow embaixo
$skuFilter = null;             // ex.: '^N' para nuvem, '^B' para baffle

foreach ($args as $a) {
    if ($a === '--force')             { $force = true; continue; }
    if ($a === '--skip-hero')         { $skipHero = true; continue; }
    if ($a === '--skip-modulation')   { $skipMod = true; continue; }
    if ($a === '--skip-dimensions')   { $skipDim = true; continue; }
    if (preg_match('/^--density=(\d+)$/', $a, $m))    { $density = (int) $m[1]; continue; }
    if (preg_match('/^--quality=(\d+)$/', $a, $m))    { $quality = (int) $m[1]; continue; }
    if (preg_match('/^--limit=(\d+)$/', $a, $m))      { $limit   = (int) $m[1]; continue; }
    if (preg_match('/^--mod-crop=(.+)$/', $a, $m))    { $modCrop = $m[1]; continue; }
    if (preg_match('/^--dim-crop=(.+)$/', $a, $m))    { $dimCrop = $m[1]; continue; }
    if (preg_match('/^--pdf-dir=(.+)$/', $a, $m))     { $pdfDir  = rtrim($m[1], '/'); continue; }
    if (preg_match('/^--sku-prefix=(.+)$/', $a, $m))  { $skuFilter = '^' . $m[1]; continue; }
    if ($pdfPath === null) { $pdfPath = $a; }
}

if ($pdfPath === null && $pdfDir === null) {
    fwrite(STDERR, "Uso:\n");
    fwrite(STDERR, "  php scripts/extract_pdf_images.php <file.pdf> [flags]\n");
    fwrite(STDERR, "  php scripts/extract_pdf_images.php --pdf-dir=/tmp/clouds [flags]\n");
    fwrite(STDERR, "Flags: --density=N --quality=N --limit=N --force\n");
    fwrite(STDERR, "       --skip-hero --skip-modulation --skip-dimensions\n");
    fwrite(STDERR, "       --mod-crop=WxH+X+Y --dim-crop=WxH+X+Y\n");
    fwrite(STDERR, "       --sku-prefix=N (processa só produtos com prefixo)\n");
    exit(1);
}

/* ---------- ImageMagick ---------- */

exec('which magick 2>/dev/null', $out, $rc);
$magickBin = ($rc === 0 && !empty($out)) ? trim($out[0]) : 'magick';
exec("$magickBin -version", $verOut, $rc);
if ($rc !== 0) {
    fwrite(STDERR, "❌ ImageMagick não disponível.\n");
    exit(1);
}
fwrite(STDOUT, "ImageMagick: " . ($verOut[0] ?? 'unknown') . "\n");

$uploadDir = BASE_PATH . '/public/uploads/products';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

/* ---------- Buscar produtos com hero_page ---------- */

$where = "1=1";
$params = [];
if ($skuFilter !== null) {
    $where .= " AND p.sku REGEXP :sku";
    $params['sku'] = $skuFilter;
}
$stmt = $pdo->prepare(
    "SELECT p.id, p.slug, p.name, p.sku,
            s_hp.value AS hero_page,
            s_mod.value AS has_modulation,
            s_pdf.value AS pdf_source
       FROM products p
       JOIN settings s_hp  ON s_hp.`key`  = CONCAT('_baffles_hero_page_', p.id)
  LEFT JOIN settings s_mod ON s_mod.`key` = CONCAT('_has_modulation_', p.id)
  LEFT JOIN settings s_pdf ON s_pdf.`key` = CONCAT('_pdf_source_', p.id)
      WHERE {$where}
   ORDER BY p.id ASC"
);
$stmt->execute($params);
$products = $stmt->fetchAll();
fwrite(STDOUT, "Produtos a processar: " . count($products) . "\n\n");

/* ---------- Helpers ---------- */

function resolvePdf(?string $singlePath, ?string $dir, ?string $pdfSource): ?string
{
    if ($singlePath !== null) return $singlePath;
    if ($dir !== null && $pdfSource !== null) {
        // pdf_source pode estar com extensão (.txt do extract original) ou sem
        $base = preg_replace('/\.(txt|pdf)$/i', '', $pdfSource);
        foreach ([$base . '.pdf', $pdfSource] as $candidate) {
            $full = $dir . '/' . $candidate;
            if (is_file($full)) return $full;
        }
    }
    return null;
}

function renderHero(string $magickBin, string $pdfPath, int $pageIdx, int $density, int $quality, int $w, int $h, string $outFile): bool
{
    $cmd = sprintf(
        '%s -density %d %s[%d] ' .
        '-background white -alpha remove -alpha off ' .
        '-colorspace sRGB ' .
        '-filter Mitchell -resize %dx%d^ -gravity center -extent %dx%d ' .
        '-unsharp 0x0.8+0.8+0.005 ' .
        '-sampling-factor 4:2:0 -strip -interlace Plane ' .
        '-define jpeg:dct-method=float ' .
        '-quality %d %s 2>&1',
        escapeshellcmd($magickBin), $density, escapeshellarg($pdfPath), $pageIdx,
        $w, $h, $w, $h,
        $quality, escapeshellarg($outFile)
    );
    exec($cmd, $_, $rc);
    return $rc === 0 && file_exists($outFile) && filesize($outFile) > 1000;
}

/**
 * Renderiza crop com coordenadas calculadas em pixels reais.
 * Retorna true se a imagem gerada tem conteúdo (não é só branco).
 */
function renderCrop(string $magickBin, string $pdfPath, int $pageIdx, int $density, string $cropSpec, string $outFile, int $minDarkPixels = 1000): bool
{
    $tmpPage = $outFile . '.fullpage.png';
    $cmd1 = sprintf(
        '%s -density %d %s[%d] -background white -alpha remove -alpha off %s 2>&1',
        escapeshellcmd($magickBin), $density, escapeshellarg($pdfPath), $pageIdx,
        escapeshellarg($tmpPage)
    );
    exec($cmd1, $_, $rc1);
    if ($rc1 !== 0 || !file_exists($tmpPage)) return false;

    $info = @getimagesize($tmpPage);
    if (!$info || empty($info[0]) || empty($info[1])) {
        @unlink($tmpPage);
        return false;
    }
    [$pageW, $pageH] = [(int) $info[0], (int) $info[1]];

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
    exec($cmd2, $_, $rc2);
    @unlink($tmpPage);
    if ($rc2 !== 0 || !file_exists($outFile)) return false;

    // Verifica se tem conteúdo "real" (não é só branco) — usa stddev como proxy
    $statCmd = sprintf('%s identify -format "%%[standard-deviation]" %s 2>&1', escapeshellcmd($magickBin), escapeshellarg($outFile));
    exec($statCmd, $statOut, $rcStat);
    if ($rcStat === 0 && !empty($statOut)) {
        $stddev = (float) trim($statOut[0]);
        // Branco puro tem stddev ~0. Conteúdo real >= 1000 normalmente.
        if ($stddev < 800) {
            @unlink($outFile);
            return false;
        }
    }
    return true;
}

/* ---------- Loop ---------- */

$processed = 0;
$heroDone = 0; $modDone = 0; $dimDone = 0;
$heroSkip = 0; $modSkip = 0; $dimSkip = 0;

foreach ($products as $p) {
    if ($limit > 0 && $processed >= $limit) break;

    $page      = (int) $p['hero_page'];
    $slug      = $p['slug'];
    $productId = (int) $p['id'];
    $hasMod    = ($p['has_modulation'] === null) ? 1 : (int) $p['has_modulation'];

    $thisPdf = resolvePdf($pdfPath, $pdfDir, $p['pdf_source']);
    if ($thisPdf === null) {
        fwrite(STDERR, "❌ PDF não encontrado para {$slug} (source={$p['pdf_source']})\n");
        continue;
    }

    // ---- HERO ----
    if (!$skipHero) {
        $heroFile = $uploadDir . '/' . $slug . '.jpg';
        if (file_exists($heroFile) && !$force) {
            // Mesmo pulando a renderizacao, garante que product_images tenha o link
            // (importante apos --reset que limpou product_images mas deixou JPGs)
            $base = basename($heroFile);
            $pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ? AND (hero_image_path IS NULL OR hero_image_path != ?)")
                ->execute([$base, $productId, $base]);
            $check = $pdo->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
            $check->execute([$productId]);
            if (!$check->fetch()) {
                $pdo->prepare("INSERT INTO product_images (product_id, file_path, alt_text, is_main, sort_order) VALUES (?, ?, ?, 1, 0)")
                    ->execute([$productId, $base, $p['name']]);
            }
            $heroSkip++;
        } else {
            $tmpHero = $heroFile . '.tmp.jpg';
            if (renderHero($magickBin, $thisPdf, $page - 1, $density, $quality, $heroW, $heroH, $tmpHero)) {
                rename($tmpHero, $heroFile);
                $base = basename($heroFile);
                $pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
                    ->execute([$base, $productId]);
                $check = $pdo->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
                $check->execute([$productId]);
                if (!$check->fetch()) {
                    $pdo->prepare("INSERT INTO product_images (product_id, file_path, alt_text, is_main, sort_order) VALUES (?, ?, ?, 1, 0)")
                        ->execute([$productId, $base, $p['name']]);
                } else {
                    $pdo->prepare("UPDATE product_images SET file_path = ?, alt_text = ? WHERE product_id = ? AND is_main = 1 LIMIT 1")
                        ->execute([$base, $p['name'], $productId]);
                }
                fwrite(STDOUT, "✓ hero       {$slug}.jpg (" . round(filesize($heroFile) / 1024) . " KB)\n");
                $heroDone++;
            } else {
                fwrite(STDERR, "❌ hero falhou: {$slug} (page {$page})\n");
                @unlink($tmpHero);
            }
        }
    }

    // ---- DIMENSIONS ----
    if (!$skipDim) {
        $dimFile = $uploadDir . '/' . $slug . '-dimensions.png';
        if (file_exists($dimFile) && !$force) {
            $dimSkip++;
        } else {
            $specsPage = $page + 1;
            $tmp = $dimFile . '.tmp.png';
            if (renderCrop($magickBin, $thisPdf, $specsPage - 1, $density, $dimCrop, $tmp)) {
                rename($tmp, $dimFile);
                $base = basename($dimFile);
                $pdo->prepare("UPDATE products SET dimensions_image_path = ? WHERE id = ?")
                    ->execute([$base, $productId]);
                fwrite(STDOUT, "✓ dimensões {$slug}-dimensions.png (" . round(filesize($dimFile) / 1024) . " KB)\n");
                $dimDone++;
            } else {
                @unlink($tmp);
                // OK silencioso — alguns produtos podem não ter desenho técnico
            }
        }
    }

    // ---- MODULATION ----
    if (!$skipMod) {
        if ($hasMod !== 1) {
            $modSkip++; // marcado como sem modulação no PDF
        } else {
            $modFile = $uploadDir . '/' . $slug . '-modulation.png';
            if (file_exists($modFile) && !$force) {
                $modSkip++;
            } else {
                $specsPage = $page + 1;
                $tmp = $modFile . '.tmp.png';
                if (renderCrop($magickBin, $thisPdf, $specsPage - 1, $density, $modCrop, $tmp)) {
                    rename($tmp, $modFile);
                    $base = basename($modFile);
                    $pdo->prepare("UPDATE products SET modulation_image_path = ? WHERE id = ?")
                        ->execute([$base, $productId]);
                    fwrite(STDOUT, "✓ modulação {$base} (" . round(filesize($modFile) / 1024) . " KB)\n");
                    $modDone++;
                } else {
                    @unlink($tmp);
                    fwrite(STDERR, "❌ modulação falhou: {$slug}\n");
                }
            }
        }
    }

    $processed++;
}

fwrite(STDOUT, "\n✅ Concluído.\n");
fwrite(STDOUT, "   Hero:       feitos {$heroDone}, pulados {$heroSkip}\n");
fwrite(STDOUT, "   Dimensões:  feitos {$dimDone}\n");
fwrite(STDOUT, "   Modulação:  feitos {$modDone}, pulados {$modSkip}\n");
