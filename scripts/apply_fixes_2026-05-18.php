<?php

declare(strict_types=1);

/**
 * Aplica as correcoes da auditoria 2026-05-18.
 *
 * - Atualiza products.specifications (JSON) e products.spec_column_labels (JSON).
 * - Faz BACKUP automatico em products._spec_backup_2026-05-18 (coluna criada se nao existir).
 *
 * Uso (no servidor):
 *   php scripts/apply_fixes_2026-05-18.php          # dry-run (mostra o que seria feito)
 *   php scripts/apply_fixes_2026-05-18.php --apply  # aplica
 */

require __DIR__ . '/../vendor/autoload.php';
App\Core\Config::boot(dirname(__DIR__));
$pdo = App\Core\Database::connection();

$apply = in_array('--apply', $argv, true);
$fixes = require __DIR__ . '/fix_products_audit_2026-05-18.php';

echo "Auditoria 2026-05-18 — " . count($fixes) . " produtos\n";
echo "Modo: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

if ($apply) {
    // Backup: cria coluna temporaria com os valores atuais.
    $colExists = $pdo->query(
        "SELECT COUNT(*) FROM information_schema.columns
          WHERE table_schema = DATABASE()
            AND table_name = 'products'
            AND column_name = '_spec_backup_2026_05_18'"
    )->fetchColumn();
    if (!$colExists) {
        $pdo->exec("ALTER TABLE products ADD COLUMN _spec_backup_2026_05_18 JSON NULL COMMENT 'backup pre-audit 2026-05-18'");
        echo "[backup col] Criada products._spec_backup_2026_05_18\n";
    }
}

$updated = 0;
$skipped = 0;

foreach ($fixes as $id => $fix) {
    $stmt = $pdo->prepare("SELECT id, sku, slug, specifications, spec_column_labels FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "[skip] id=$id NAO encontrado\n";
        $skipped++;
        continue;
    }

    if (!empty($fix['_missing'])) {
        echo "[skip] id=$id {$product['sku']} marcado como _missing no audit\n";
        $skipped++;
        continue;
    }

    $newSpecs   = $fix['specifications'] ?? null;
    $newLabels  = $fix['spec_column_labels'] ?? null;

    $newSpecsJson  = $newSpecs  !== null ? json_encode($newSpecs,  JSON_UNESCAPED_UNICODE) : null;
    $newLabelsJson = $newLabels !== null ? json_encode($newLabels, JSON_UNESCAPED_UNICODE) : null;

    $oldSpecs  = $product['specifications'];
    $oldLabels = $product['spec_column_labels'];

    // Backup snapshot (combina specs + labels antigos num unico JSON)
    $backupJson = json_encode([
        'specifications'     => $oldSpecs  !== null ? json_decode($oldSpecs,  true) : null,
        'spec_column_labels' => $oldLabels !== null ? json_decode($oldLabels, true) : null,
    ], JSON_UNESCAPED_UNICODE);

    echo "id=$id {$product['sku']} {$product['slug']}\n";
    echo "  labels: " . (string) ($oldLabels ?? 'null') . " -> " . (string) ($newLabelsJson ?? 'null') . "\n";
    if ($oldSpecs !== $newSpecsJson) {
        $oldCnt = is_array(json_decode((string) $oldSpecs, true)) ? count(json_decode((string) $oldSpecs, true)) : 0;
        $newCnt = is_array($newSpecs) ? count($newSpecs) : 0;
        echo "  rows: $oldCnt -> $newCnt\n";
    }

    if ($apply) {
        $upd = $pdo->prepare(
            "UPDATE products
                SET specifications     = :specs,
                    spec_column_labels = :labels,
                    _spec_backup_2026_05_18 = :bkp
              WHERE id = :id"
        );
        $upd->execute([
            'specs'  => $newSpecsJson,
            'labels' => $newLabelsJson,
            'bkp'    => $backupJson,
            'id'     => $id,
        ]);
        $updated++;
    }
}

echo "\n=== Total: " . ($apply ? "$updated atualizados" : count($fixes) - $skipped . " p/ atualizar") . ", $skipped skipados ===\n";

if (!$apply) {
    echo "\nDRY-RUN. Rode com --apply para aplicar.\n";
}
