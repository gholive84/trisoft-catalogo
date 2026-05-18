<?php

declare(strict_types=1);

/**
 * 1) Renomeia id 859 (NF-FLY-02 cloud-form-fly-solid) -> "CLOUD FORM FLY 01 - SOLID"
 *    SKU NF-FLY-01, codigo NF-FLY-01-0001. Slug PRESERVADO (cloud-form-fly-solid)
 *    para nao quebrar URLs ja indexadas.
 *
 * 2) Cria NOVO produto "CLOUD FORM FLY 02 - SOLID":
 *    SKU NF-FLY-02 (liberado pelo passo 1), slug cloud-form-fly-02-solid.
 *    Mesmas categorias do 859 (Nuvens, Form, Fly).
 *
 * 3) Renomeia id 968 (SS-BSPBS-02-47E3 sunshade-board-pbs) -> "SUNSHADE BOARD - SOLID 02"
 *    Slug -> sunshade-board-solid-02. SKU mantido (ja disambiguado).
 *
 * Backup automatico em produtos._fly_solid_backup_2026-05-18 (snapshot JSON do estado pre-mudanca).
 *
 * Uso:
 *   php scripts/add_fly02_and_solid02_2026-05-18.php          (dry-run)
 *   php scripts/add_fly02_and_solid02_2026-05-18.php --apply  (aplica)
 */

require __DIR__ . '/../vendor/autoload.php';
App\Core\Config::boot(dirname(__DIR__));
$pdo = App\Core\Database::connection();

$apply = in_array('--apply', $argv, true);
echo "Modo: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

// Specs reutilizados.
$flySpec = function (string $code, int $piecesPerBox, int $petBottles): array {
    return [[
        'code' => $code, 'thickness' => 9, 'a' => 1200, 'b' => 540,
        'c' => '', 'd' => '',
        'pieces_per_box' => $piecesPerBox, 'coverage_area' => '', 'pet_bottles' => $petBottles,
    ]];
};

// ============== 1) Atualiza id 859 para Fly 01 ==============
$old859 = $pdo->query("SELECT * FROM products WHERE id=859")->fetch();
echo "[1] id=859 antes: sku={$old859['sku']} name={$old859['name']} slug={$old859['slug']}\n";

$new859 = [
    'name' => 'CLOUD FORM FLY 01 - SOLID',
    'subtitle' => 'SOLID',
    'sku' => 'NF-FLY-01',
    'slug' => 'cloud-form-fly-solid', // preserva URL
    'specifications' => json_encode($flySpec('NF-FLY-01-0001', 5, 72), JSON_UNESCAPED_UNICODE),
];
echo "    -> sku={$new859['sku']} name={$new859['name']} (slug preserved)\n";

// ============== 2) Cria novo Fly 02 ==============
$newProductData = [
    'sku' => 'NF-FLY-02',
    'name' => 'CLOUD FORM FLY 02 - SOLID',
    'subtitle' => 'SOLID',
    'slug' => 'cloud-form-fly-02-solid',
    'short_description' => $old859['short_description'],
    'description' => $old859['description'],
    'specifications' => json_encode($flySpec('NF-FLY-02-0002', 5, 72), JSON_UNESCAPED_UNICODE),
    'spec_layout' => 'simple',
    'price' => $old859['price'] ?? 0,
    'is_active' => 1,
    'is_featured' => 0,
];
echo "[2] novo produto: sku={$newProductData['sku']} name={$newProductData['name']} slug={$newProductData['slug']}\n";

// ============== 3) Atualiza id 968 para Sunshade Solid 02 ==============
$old968 = $pdo->query("SELECT * FROM products WHERE id=968")->fetch();
echo "[3] id=968 antes: sku={$old968['sku']} name={$old968['name']} slug={$old968['slug']}\n";

$new968 = [
    'name' => 'SUNSHADE BOARD - SOLID 02',
    'subtitle' => 'SOLID 02',
    'slug' => 'sunshade-board-solid-02',
];
echo "    -> name={$new968['name']} slug={$new968['slug']}\n";

if (!$apply) {
    echo "\nDRY-RUN concluido. Rode com --apply para aplicar.\n";
    exit(0);
}

// Backup column
$col = $pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='products' AND column_name='_fly_solid_backup_2026_05_18'")->fetchColumn();
if (!$col) {
    $pdo->exec("ALTER TABLE products ADD COLUMN _fly_solid_backup_2026_05_18 JSON NULL");
    echo "[backup col] criada.\n";
}

$pdo->beginTransaction();
try {
    // (1) Backup + update 859
    $bk = json_encode($old859, JSON_UNESCAPED_UNICODE);
    $pdo->prepare("UPDATE products SET _fly_solid_backup_2026_05_18=:bk, sku=:sku, name=:name, subtitle=:sub, slug=:slug, specifications=:specs WHERE id=859")
        ->execute([
            'bk' => $bk,
            'sku' => $new859['sku'], 'name' => $new859['name'], 'sub' => $new859['subtitle'],
            'slug' => $new859['slug'], 'specs' => $new859['specifications'],
        ]);
    echo "[1] id=859 atualizado para Fly 01\n";

    // (2) Insert novo Fly 02
    $cols = 'sku, name, subtitle, slug, short_description, description, specifications, spec_layout, price, is_active, is_featured';
    $vals = ':sku, :name, :sub, :slug, :short, :desc, :specs, :slay, :price, :active, :featured';
    $pdo->prepare("INSERT INTO products ($cols) VALUES ($vals)")
        ->execute([
            'sku' => $newProductData['sku'], 'name' => $newProductData['name'],
            'sub' => $newProductData['subtitle'], 'slug' => $newProductData['slug'],
            'short' => $newProductData['short_description'], 'desc' => $newProductData['description'],
            'specs' => $newProductData['specifications'], 'slay' => 'simple',
            'price' => $newProductData['price'],
            'active' => $newProductData['is_active'], 'featured' => $newProductData['is_featured'],
        ]);
    $newId = (int) $pdo->lastInsertId();
    echo "[2] novo produto criado id=$newId\n";

    // Categorias do novo (mesmas do 859: 19,21,30 = Nuvens, Form, Fly)
    foreach ([19, 21, 30] as $cid) {
        $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)")
            ->execute([$newId, $cid]);
    }
    echo "[2] categorias atribuidas: 19, 21, 30\n";

    // (3) Backup + update 968
    $bk2 = json_encode($old968, JSON_UNESCAPED_UNICODE);
    $pdo->prepare("UPDATE products SET _fly_solid_backup_2026_05_18=:bk, name=:name, subtitle=:sub, slug=:slug WHERE id=968")
        ->execute([
            'bk' => $bk2,
            'name' => $new968['name'], 'sub' => $new968['subtitle'], 'slug' => $new968['slug'],
        ]);
    echo "[3] id=968 atualizado para Sunshade Board Solid 02\n";

    $pdo->commit();
    echo "\nOK — transaction committed.\n";
} catch (\Throwable $e) {
    $pdo->rollBack();
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
