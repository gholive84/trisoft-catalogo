<?php

declare(strict_types=1);

/**
 * Reverte labels 'a=Width, b=Length' para null (ou apenas extras) nos produtos
 * onde o PDF original usa generic "A"/"B" em aspas (com diagrama).
 *
 * Os labels semanticos (Diameter, Wall Height, Height, Length, etc.) sao
 * mantidos onde o PDF tem cabecalho semantico explicito.
 *
 * Uso:
 *   php scripts/revert_widthlength_labels_2026-05-19.php          # dry-run
 *   php scripts/revert_widthlength_labels_2026-05-19.php --apply  # aplica
 */

require __DIR__ . '/../vendor/autoload.php';
App\Core\Config::boot(dirname(__DIR__));
$pdo = App\Core\Database::connection();

$apply = in_array('--apply', $argv, true);

// id => novos labels (null = remover totalmente)
$reverts = [
    // Revest Form variantes (PDF usa "A"/"B")
    932 => ['e' => 'Arrow profile'],          // RF-FOR-09 mantem Arrow profile
    934 => ['e' => 'Arrow profile'],          // RF-COB-09
    935 => ['e' => 'Arrow profile'],          // RF-FFM-09
    939 => null,                              // RF-PRI-09
    941 => null,                              // RP-COB-09
    940 => null,                              // RP-PEG-03
    944 => ['d' => 'V-cut spacing'],          // RP-RFL-09 mantem V-cut spacing
    947 => ['d' => 'V-cut spacing'],          // RP-RFL-09-8EFE
    933 => null,                              // RF-ENG-09
    936 => null,                              // RF-MWV-03
    937 => null,                              // RT-SMO-03
    938 => null,                              // RF-VCT-09
    942 => null,                              // RF-PWV-30
    943 => null,                              // RF-PMO-03
    945 => null,                              // RP-RII-16
    946 => null,                              // RP-RFL-16
    948 => null,                              // RP-RIP-16
    949 => null,                              // RP-RFP-16
    950 => null,                              // RF-MDM-09

    // Revest Form Metallic (PDF usa "A"/"B")
    923 => null,                              // RF-MEC-09
    922 => null,                              // RF-MEG-09
    921 => null,                              // RF-MEL-03
    924 => null,                              // RF-MTC-09
    925 => null,                              // RF-MMO-03

    // Revest Ness Bricks (PDF usa "A"/"B"/"A"/"C")
    931 => null,                              // BR-BCK-25
];

echo "Modo: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

$updated = 0;
foreach ($reverts as $id => $newLabels) {
    $r = $pdo->prepare("SELECT id, sku, slug, spec_column_labels FROM products WHERE id=?");
    $r->execute([$id]);
    $p = $r->fetch();
    if (!$p) { echo "[skip] id=$id nao encontrado\n"; continue; }

    $newJson = $newLabels !== null ? json_encode($newLabels, JSON_UNESCAPED_UNICODE) : null;
    echo "id=$id {$p['sku']} {$p['slug']}\n";
    echo "  labels: " . (string) ($p['spec_column_labels'] ?? 'null') . " -> " . (string) ($newJson ?? 'null') . "\n";

    if ($apply) {
        $pdo->prepare("UPDATE products SET spec_column_labels=:l WHERE id=:id")
            ->execute(['l' => $newJson, 'id' => $id]);
        $updated++;
    }
}

echo "\n=== " . ($apply ? "$updated atualizados" : count($reverts) . " p/ atualizar") . " ===\n";
if (!$apply) echo "DRY-RUN. Rode com --apply para aplicar.\n";
