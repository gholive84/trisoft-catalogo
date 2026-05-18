<?php

declare(strict_types=1);

/**
 * Migra id 931 (BR-BCK-25 revest-ness-bricks-solid) para layout 'flexible'.
 * O PDF Trisoft mostra cube (200x200) + brick (200x400) lado a lado num mesmo SKU.
 * Adiciona tambem variante BR-BCK-50 (50mm) que estava faltando.
 *
 * Uso:
 *   php scripts/migrate_bricks_to_flexible_2026-05-19.php          # dry-run
 *   php scripts/migrate_bricks_to_flexible_2026-05-19.php --apply  # aplica
 */

require __DIR__ . '/../vendor/autoload.php';
App\Core\Config::boot(dirname(__DIR__));
$pdo = App\Core\Database::connection();

$apply = in_array('--apply', $argv, true);
echo "Modo: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

$schema = [
    'columns' => [
        ['key'=>'code',         'label'=>'Code',                    'unit'=>null, 'color'=>null,    'group'=>null],
        ['key'=>'thickness',    'label'=>'Thickness',               'unit'=>'mm', 'color'=>null,    'group'=>null],
        ['key'=>'cube_a',       'label'=>'"A"',                     'unit'=>'mm', 'color'=>'blue',  'group'=>'Cube'],
        ['key'=>'cube_b',       'label'=>'"B"',                     'unit'=>'mm', 'color'=>'blue',  'group'=>'Cube'],
        ['key'=>'cube_units',   'label'=>'Number of units per box', 'unit'=>null, 'color'=>'blue',  'group'=>'Cube'],
        ['key'=>'brick_a',      'label'=>'"A"',                     'unit'=>'mm', 'color'=>'amber', 'group'=>'Brick'],
        ['key'=>'brick_c',      'label'=>'"C"',                     'unit'=>'mm', 'color'=>'amber', 'group'=>'Brick'],
        ['key'=>'brick_units',  'label'=>'Number of units per box', 'unit'=>null, 'color'=>'amber', 'group'=>'Brick'],
        ['key'=>'coverage',     'label'=>'Coverage Area',           'unit'=>null, 'color'=>null,    'group'=>null],
        ['key'=>'pet',          'label'=>'PET Bottles',             'unit'=>null, 'color'=>null,    'group'=>null],
    ],
];

$rows = [
    [
        'code'=>'BR-BCK-25-0001', 'thickness'=>25,
        'cube_a'=>200, 'cube_b'=>200, 'cube_units'=>72,
        'brick_a'=>200, 'brick_c'=>400, 'brick_units'=>60,
        'coverage'=>'7,68 m²', 'pet'=>2,
    ],
    [
        'code'=>'BR-BCK-50-0001', 'thickness'=>50,
        'cube_a'=>200, 'cube_b'=>200, 'cube_units'=>36,
        'brick_a'=>200, 'brick_c'=>400, 'brick_units'=>30,
        'coverage'=>'3,84 m²', 'pet'=>4,
    ],
];

$old = $pdo->query("SELECT id, sku, slug, spec_layout, spec_schema, specifications FROM products WHERE id=931")->fetch();
echo "id=931 antes:\n";
echo "  layout: {$old['spec_layout']}\n";
echo "  specifications: " . substr((string) $old['specifications'], 0, 120) . "...\n";

echo "\nNovo schema (flexible):\n";
echo "  layout: flexible\n";
echo "  colunas: " . count($schema['columns']) . " (com grupos Cube/Brick)\n";
echo "  rows: " . count($rows) . " (25mm + 50mm)\n";

if (!$apply) {
    echo "\nDRY-RUN. Rode com --apply para aplicar.\n";
    exit(0);
}

// Backup
$col = $pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='products' AND column_name='_bricks_backup_2026_05_19'")->fetchColumn();
if (!$col) {
    $pdo->exec("ALTER TABLE products ADD COLUMN _bricks_backup_2026_05_19 JSON NULL");
}
$bk = json_encode($old, JSON_UNESCAPED_UNICODE);

$pdo->prepare("UPDATE products SET spec_layout=?, spec_schema=?, specifications=?, spec_column_labels=NULL, _bricks_backup_2026_05_19=? WHERE id=931")
    ->execute([
        'flexible',
        json_encode($schema, JSON_UNESCAPED_UNICODE),
        json_encode($rows, JSON_UNESCAPED_UNICODE),
        $bk,
    ]);

echo "\nOK — id=931 migrado para flexible.\n";
