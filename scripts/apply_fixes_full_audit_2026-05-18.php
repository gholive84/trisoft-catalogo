<?php

declare(strict_types=1);

/**
 * Aplica a revisao completa 2026-05-18 (TODOS os produtos vs PDFs).
 *
 * Lê e mescla 4 fix maps gerados pelos agentes de auditoria:
 *   - scripts/fix_baffle_full_audit_2026-05-18.php
 *   - scripts/fix_nuvem_full_audit_2026-05-18.php
 *   - scripts/fix_fachada_full_audit_2026-05-18.php
 *   - scripts/fix_parede_full_audit_2026-05-18.php
 *
 * Cria coluna backup `_full_review_backup_2026_05_18` em products.
 * Cada produto atualizado: spec_layout, spec_column_labels, spec_schema, specifications.
 *
 * Uso:
 *   php scripts/apply_fixes_full_audit_2026-05-18.php          # dry-run
 *   php scripts/apply_fixes_full_audit_2026-05-18.php --apply  # aplica
 */

require __DIR__ . '/../vendor/autoload.php';
App\Core\Config::boot(dirname(__DIR__));
$pdo = App\Core\Database::connection();

$apply = in_array('--apply', $argv, true);

$files = [
    __DIR__ . '/fix_baffle_full_audit_2026-05-18.php',
    __DIR__ . '/fix_nuvem_full_audit_2026-05-18.php',
    __DIR__ . '/fix_fachada_full_audit_2026-05-18.php',
    __DIR__ . '/fix_parede_full_audit_2026-05-18.php',
];

$fixes = [];
foreach ($files as $f) {
    if (!file_exists($f)) {
        echo "[WARN] arquivo nao encontrado: $f — skip\n";
        continue;
    }
    $part = require $f;
    if (!is_array($part)) {
        echo "[WARN] $f nao retornou array — skip\n";
        continue;
    }
    foreach ($part as $id => $fix) {
        if (isset($fixes[$id])) {
            echo "[WARN] id=$id duplicado entre fix maps — usando ultimo carregado\n";
        }
        $fixes[$id] = $fix;
    }
}

echo "Revisao completa 2026-05-18 — " . count($fixes) . " produtos a atualizar\n";
echo "Modo: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

if ($apply) {
    $col = $pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='products' AND column_name='_full_review_backup_2026_05_18'")->fetchColumn();
    if (!$col) {
        $pdo->exec("ALTER TABLE products ADD COLUMN _full_review_backup_2026_05_18 JSON NULL COMMENT 'backup pre-full-review 2026-05-18'");
        echo "[backup col] criada products._full_review_backup_2026_05_18\n\n";
    }
}

$updated = 0;
$skipped = 0;

foreach ($fixes as $id => $fix) {
    $stmt = $pdo->prepare("SELECT id, sku, slug, spec_layout, spec_column_labels, spec_schema, specifications FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) { echo "[skip] id=$id nao encontrado\n"; $skipped++; continue; }
    if (!empty($fix['_missing'])) { echo "[skip] id=$id {$product['sku']} _missing\n"; $skipped++; continue; }

    $newLayout = $fix['spec_layout'] ?? $product['spec_layout'];
    $newLabels = $fix['spec_column_labels'] ?? null;
    $newSchema = $fix['spec_schema'] ?? null;
    $newSpecs  = $fix['specifications'] ?? null;

    $newSpecsJson  = $newSpecs  !== null ? json_encode($newSpecs,  JSON_UNESCAPED_UNICODE) : null;
    $newLabelsJson = !empty($newLabels) ? json_encode($newLabels, JSON_UNESCAPED_UNICODE) : null;
    $newSchemaJson = !empty($newSchema) ? json_encode($newSchema, JSON_UNESCAPED_UNICODE) : null;

    $backupJson = json_encode($product, JSON_UNESCAPED_UNICODE);

    $oldCnt = is_array(json_decode((string) $product['specifications'], true)) ? count(json_decode((string) $product['specifications'], true)) : 0;
    $newCnt = is_array($newSpecs) ? count($newSpecs) : 0;
    echo sprintf("id=%-5d %-15s [%s -> %s] rows %d->%d\n",
        $id, $product['sku'],
        $product['spec_layout'], $newLayout,
        $oldCnt, $newCnt
    );

    if ($apply) {
        $pdo->prepare(
            "UPDATE products
                SET spec_layout = :layout,
                    spec_column_labels = :labels,
                    spec_schema = :schema,
                    specifications = :specs,
                    _full_review_backup_2026_05_18 = :bk
              WHERE id = :id"
        )->execute([
            'layout' => $newLayout,
            'labels' => $newLabelsJson,
            'schema' => $newSchemaJson,
            'specs'  => $newSpecsJson,
            'bk'     => $backupJson,
            'id'     => $id,
        ]);
        $updated++;
    }
}

echo "\n=== " . ($apply ? "$updated atualizados" : (count($fixes) - $skipped) . " p/ atualizar") . ", $skipped skipados ===\n";
if (!$apply) echo "DRY-RUN. Rode com --apply para aplicar.\n";
