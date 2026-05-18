<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ImageUploadService;

final class ProductController
{
    private View $view;
    private ProductRepository $products;
    private CategoryRepository $cats;
    private ImageUploadService $uploads;
    private \PDO $pdo;

    public function __construct()
    {
        $this->view     = new View(base_path('templates'));
        $this->products = new ProductRepository();
        $this->cats     = new CategoryRepository();
        $this->uploads  = new ImageUploadService();
        $this->pdo      = Database::connection();
    }

    /* ------------------------- Listagem ------------------------- */

    public function index(Request $request): string
    {
        $q       = trim((string) $request->query('q', ''));
        $status  = (string) $request->query('status', '');
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $where  = "p.deleted_at IS NULL";
        $params = [];
        if ($q !== '') {
            // MySQL native prepares (EMULATE_PREPARES=false) exigem placeholder
            // unico por ocorrencia — usamos :qN para nome/sku/subtitle.
            $where .= " AND (p.name LIKE :q1 OR p.sku LIKE :q2 OR p.subtitle LIKE :q3)";
            $like = "%{$q}%";
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }
        if ($status === 'active')   $where .= " AND p.is_active = 1";
        if ($status === 'inactive') $where .= " AND p.is_active = 0";
        if ($status === 'featured') $where .= " AND p.is_featured = 1";

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT p.id, p.sku, p.name, p.subtitle, p.price, p.is_active, p.is_featured, p.updated_at,
                       (SELECT pi.file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC, pi.sort_order ASC LIMIT 1) AS thumb,
                       (SELECT COUNT(*) FROM product_categories pc WHERE pc.product_id = p.id) AS cat_count
                  FROM products p
                 WHERE {$where}
              ORDER BY p.updated_at DESC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return $this->view->render('admin/products/index', [
            'title'    => 'Produtos',
            'items'    => $items,
            'total'    => $total,
            'q'        => $q,
            'status'   => $status,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    /* ------------------------- Criar/editar ------------------------- */

    public function create(Request $request): string
    {
        return $this->view->render('admin/products/form', [
            'title'        => 'Novo produto',
            'product'      => $this->emptyProduct(),
            'images'       => [],
            'allCategories'=> $this->cats->all(),
            'productCats'  => [],
            'isNew'        => true,
        ]);
    }

    public function edit(Request $request): string
    {
        $id = (int) $request->param('id');
        $product = $this->products->findById($id);
        if ($product === null) {
            Response::abort(404, 'Produto não encontrado.');
        }

        $images = $this->products->imagesOf($id);

        $productCats = array_map(
            fn ($c) => (int) $c['id'],
            $this->products->categoriesOf($id)
        );

        return $this->view->render('admin/products/form', [
            'title'        => $product['name'],
            'product'      => $product,
            'images'       => $images,
            'allCategories'=> $this->cats->all(),
            'productCats'  => $productCats,
            'isNew'        => false,
        ]);
    }

    public function store(Request $request): never
    {
        $data = $this->extractData($request);

        $v = $this->validate($data, isNew: true);
        if ($v->fails()) {
            Session::flashInput($data);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url('admin/produtos/novo'));
        }
        if ($this->skuExists($data['sku'])) {
            Session::flashInput($data);
            Session::flash('error', 'SKU já existe.');
            Response::redirect(url('admin/produtos/novo'));
        }
        if ($this->slugExists($data['slug'])) {
            $data['slug'] .= '-' . bin2hex(random_bytes(2));
        }

        try {
            $this->pdo->beginTransaction();
            $id = $this->insertProduct($data);
            $this->syncCategories($id, $data['categories']);
            $this->handleUploadedImages($id, $request, $data['name']);
            $this->handleTechnicalImages($id, $request, $data['name']);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            Logger::error('Falha ao criar produto', ['error' => $e->getMessage()]);
            Session::flashInput($data);
            Session::flash('error', 'Falha ao criar produto: ' . $e->getMessage());
            Response::redirect(url('admin/produtos/novo'));
        }

        Session::flash('success', 'Produto criado.');
        Response::redirect(url("admin/produtos/{$id}/editar"));
    }

    public function update(Request $request): never
    {
        $id = (int) $request->param('id');
        $product = $this->products->findById($id);
        if ($product === null) Response::abort(404);

        $data = $this->extractData($request);
        $v = $this->validate($data, isNew: false);
        if ($v->fails()) {
            Session::flashInput($data);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url("admin/produtos/{$id}/editar"));
        }
        if ($data['sku'] !== $product['sku'] && $this->skuExists($data['sku'], $id)) {
            Session::flashInput($data);
            Session::flash('error', 'SKU já existe em outro produto.');
            Response::redirect(url("admin/produtos/{$id}/editar"));
        }
        if ($data['slug'] !== $product['slug'] && $this->slugExists($data['slug'], $id)) {
            $data['slug'] .= '-' . bin2hex(random_bytes(2));
        }

        try {
            $this->pdo->beginTransaction();
            $this->updateProduct($id, $data);
            $this->syncCategories($id, $data['categories']);
            $this->handleUploadedImages($id, $request, $data['name']);
            $this->handleTechnicalImages($id, $request, $data['name']);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            Logger::error('Falha ao atualizar produto', ['id' => $id, 'error' => $e->getMessage()]);
            Session::flash('error', 'Falha ao atualizar: ' . $e->getMessage());
            Response::redirect(url("admin/produtos/{$id}/editar"));
        }

        Session::flash('success', 'Produto atualizado.');
        Response::redirect(url("admin/produtos/{$id}/editar"));
    }

    public function destroy(Request $request): never
    {
        $id = (int) $request->param('id');
        $stmt = $this->pdo->prepare("UPDATE products SET deleted_at = NOW(), is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        Session::flash('success', 'Produto removido.');
        Response::redirect(url('admin/produtos'));
    }

    /* ------------------------- Imagens ------------------------- */

    public function setMainImage(Request $request): never
    {
        $productId = (int) $request->param('id');
        $imageId   = (int) $request->post('image_id', 0);
        if ($imageId > 0) {
            $this->pdo->beginTransaction();
            $this->pdo->prepare("UPDATE product_images SET is_main = 0 WHERE product_id = ?")
                ->execute([$productId]);
            $this->pdo->prepare("UPDATE product_images SET is_main = 1 WHERE id = ? AND product_id = ?")
                ->execute([$imageId, $productId]);
            // Atualiza hero_image_path
            $stmt = $this->pdo->prepare("SELECT file_path FROM product_images WHERE id = ?");
            $stmt->execute([$imageId]);
            $file = $stmt->fetchColumn();
            if ($file) {
                $this->pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
                    ->execute([$file, $productId]);
            }
            $this->pdo->commit();
            Session::flash('success', 'Imagem principal atualizada.');
        }
        Response::redirect(url("admin/produtos/{$productId}/editar"));
    }

    public function deleteImage(Request $request): never
    {
        $productId = (int) $request->param('id');
        $imageId   = (int) $request->post('image_id', 0);
        if ($imageId > 0) {
            $stmt = $this->pdo->prepare("SELECT file_path, is_main FROM product_images WHERE id = ? AND product_id = ?");
            $stmt->execute([$imageId, $productId]);
            $row = $stmt->fetch();
            if ($row) {
                $this->uploads->delete('products/' . $row['file_path']);
                $this->pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imageId]);
                if ($row['is_main']) {
                    // Promove primeira restante a main
                    $this->pdo->prepare(
                        "UPDATE product_images SET is_main = 1
                          WHERE product_id = ?
                       ORDER BY sort_order ASC, id ASC LIMIT 1"
                    )->execute([$productId]);
                    $main = $this->pdo->prepare("SELECT file_path FROM product_images WHERE product_id = ? AND is_main = 1");
                    $main->execute([$productId]);
                    $newMain = $main->fetchColumn();
                    $this->pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
                        ->execute([$newMain ?: null, $productId]);
                }
                Session::flash('success', 'Imagem removida.');
            }
        }
        Response::redirect(url("admin/produtos/{$productId}/editar"));
    }

    /* ------------------------- Helpers ------------------------- */

    private function emptyProduct(): array
    {
        return [
            'id' => null,
            'sku' => '',
            'name' => '',
            'subtitle' => '',
            'slug' => '',
            'short_description' => '',
            'description' => '',
            'specifications' => null,
            'price' => 0,
            'cost' => null,
            'stock_quantity' => null,
            'weight_kg' => null,
            'width_cm' => null,
            'height_cm' => null,
            'length_cm' => null,
            'is_active' => 1,
            'is_featured' => 0,
            'meta_title' => '',
            'meta_description' => '',
            'hero_image_path' => null,
            'dimensions_image_path' => null,
            'modulation_image_path' => null,
        ];
    }

    private function extractData(Request $request): array
    {
        $name = trim((string) $request->post('name', ''));
        $slug = trim((string) $request->post('slug', ''));
        if ($slug === '') $slug = slugify($name);

        // Variações: novo formato (form dinâmico) — specifications[N][campo] array de arrays.
        // Fallback: specifications_json (textarea) para retrocompat.
        $specifications = null;
        $specsArray = (array) $request->post('specifications', []);
        if ($specsArray !== []) {
            $filtered = [];
            foreach ($specsArray as $row) {
                if (!is_array($row)) continue;
                // descarta linhas totalmente vazias
                $nonEmpty = array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : (string) $v, $row));
                if ($nonEmpty === []) continue;
                $filtered[] = [
                    'code'           => trim((string) ($row['code'] ?? '')),
                    'thickness'      => is_numeric($row['thickness'] ?? null) ? (int) $row['thickness'] : trim((string) ($row['thickness'] ?? '')),
                    'a'              => is_numeric($row['a'] ?? null) ? (int) $row['a'] : trim((string) ($row['a'] ?? '')),
                    'b'              => is_numeric($row['b'] ?? null) ? (int) $row['b'] : trim((string) ($row['b'] ?? '')),
                    'c'              => is_numeric($row['c'] ?? null) ? (int) $row['c'] : trim((string) ($row['c'] ?? '')),
                    'd'              => is_numeric($row['d'] ?? null) ? (int) $row['d'] : trim((string) ($row['d'] ?? '')),
                    'pieces_per_box' => is_numeric($row['pieces_per_box'] ?? null) ? (int) $row['pieces_per_box'] : trim((string) ($row['pieces_per_box'] ?? '')),
                    'coverage_area'  => trim((string) ($row['coverage_area'] ?? '')),
                    'pet_bottles'    => is_numeric($row['pet_bottles'] ?? null) ? (int) $row['pet_bottles'] : trim((string) ($row['pet_bottles'] ?? '')),
                ];
            }
            if ($filtered !== []) $specifications = $filtered;
        } else {
            $specsRaw = trim((string) $request->post('specifications_json', ''));
            if ($specsRaw !== '') {
                $decoded = json_decode($specsRaw, true);
                if (is_array($decoded)) $specifications = $decoded;
            }
        }

        return [
            'sku'               => trim((string) $request->post('sku', '')),
            'name'              => $name,
            'subtitle'          => trim((string) $request->post('subtitle', '')),
            'slug'              => $slug,
            'short_description' => trim((string) $request->post('short_description', '')),
            'description'       => trim((string) $request->post('description', '')),
            'specifications'    => $specifications,
            'price'             => (float) str_replace(',', '.', (string) $request->post('price', '0')),
            'cost'              => $request->post('cost') !== null && $request->post('cost') !== ''
                                    ? (float) str_replace(',', '.', (string) $request->post('cost')) : null,
            'stock_quantity'    => $request->post('stock_quantity') !== '' ? (int) $request->post('stock_quantity') : null,
            'weight_kg'         => $request->post('weight_kg') !== '' ? (float) str_replace(',', '.', (string) $request->post('weight_kg')) : null,
            'width_cm'          => $request->post('width_cm') !== '' ? (float) str_replace(',', '.', (string) $request->post('width_cm')) : null,
            'height_cm'         => $request->post('height_cm') !== '' ? (float) str_replace(',', '.', (string) $request->post('height_cm')) : null,
            'length_cm'         => $request->post('length_cm') !== '' ? (float) str_replace(',', '.', (string) $request->post('length_cm')) : null,
            'is_active'         => $request->post('is_active') ? 1 : 0,
            'is_featured'       => $request->post('is_featured') ? 1 : 0,
            'meta_title'        => trim((string) $request->post('meta_title', '')),
            'meta_description'  => trim((string) $request->post('meta_description', '')),
            'categories'        => array_map('intval', (array) $request->post('categories', [])),
        ];
    }

    private function validate(array $data, bool $isNew): Validator
    {
        return new Validator($data, [
            'sku'   => 'required|max:80',
            'name'  => 'required|max:200',
            'slug'  => 'required|max:220',
            'price' => 'numeric',
        ]);
    }

    private function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM products WHERE sku = :sku" . ($excludeId !== null ? " AND id <> :id" : "") . " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $params = ['sku' => $sku];
        if ($excludeId !== null) $params['id'] = $excludeId;
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM products WHERE slug = :slug" . ($excludeId !== null ? " AND id <> :id" : "") . " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $params = ['slug' => $slug];
        if ($excludeId !== null) $params['id'] = $excludeId;
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    private function insertProduct(array $d): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO products
                (sku, name, subtitle, slug, short_description, description, specifications,
                 price, cost, stock_quantity, weight_kg, width_cm, height_cm, length_cm,
                 is_active, is_featured, meta_title, meta_description)
             VALUES
                (:sku, :name, :sub, :slug, :short, :desc, :specs,
                 :price, :cost, :stock, :wkg, :w, :h, :l,
                 :active, :featured, :mt, :md)"
        );
        $stmt->execute([
            'sku' => $d['sku'], 'name' => $d['name'], 'sub' => $d['subtitle'] ?: null,
            'slug' => $d['slug'], 'short' => $d['short_description'] ?: null,
            'desc' => $d['description'] ?: null,
            'specs' => $d['specifications'] !== null ? json_encode($d['specifications'], JSON_UNESCAPED_UNICODE) : null,
            'price' => $d['price'], 'cost' => $d['cost'], 'stock' => $d['stock_quantity'],
            'wkg' => $d['weight_kg'], 'w' => $d['width_cm'], 'h' => $d['height_cm'], 'l' => $d['length_cm'],
            'active' => $d['is_active'], 'featured' => $d['is_featured'],
            'mt' => $d['meta_title'] ?: null, 'md' => $d['meta_description'] ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    private function updateProduct(int $id, array $d): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE products SET
                sku = :sku, name = :name, subtitle = :sub, slug = :slug,
                short_description = :short, description = :desc, specifications = :specs,
                price = :price, cost = :cost, stock_quantity = :stock,
                weight_kg = :wkg, width_cm = :w, height_cm = :h, length_cm = :l,
                is_active = :active, is_featured = :featured,
                meta_title = :mt, meta_description = :md
              WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'sku' => $d['sku'], 'name' => $d['name'], 'sub' => $d['subtitle'] ?: null,
            'slug' => $d['slug'], 'short' => $d['short_description'] ?: null,
            'desc' => $d['description'] ?: null,
            'specs' => $d['specifications'] !== null ? json_encode($d['specifications'], JSON_UNESCAPED_UNICODE) : null,
            'price' => $d['price'], 'cost' => $d['cost'], 'stock' => $d['stock_quantity'],
            'wkg' => $d['weight_kg'], 'w' => $d['width_cm'], 'h' => $d['height_cm'], 'l' => $d['length_cm'],
            'active' => $d['is_active'], 'featured' => $d['is_featured'],
            'mt' => $d['meta_title'] ?: null, 'md' => $d['meta_description'] ?: null,
        ]);
    }

    private function syncCategories(int $productId, array $categoryIds): void
    {
        $this->pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$productId]);
        if ($categoryIds === []) return;
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $cid) {
            $stmt->execute([$productId, $cid]);
        }
    }

    /**
     * Substitui ou remove as imagens técnicas (dimensions/modulation) do produto.
     * Cada campo tem um upload opcional + checkbox "_remove" para limpar.
     */
    private function handleTechnicalImages(int $productId, Request $request, string $productName): void
    {
        $fields = [
            'dimensions_image_path' => 'dim',
            'modulation_image_path' => 'mod',
        ];

        // Diagnostico: o que chegou em $_FILES p/ tech images
        $debug = [];
        foreach ($fields as $column => $_) {
            $u = $_FILES[$column] ?? null;
            if (is_array($u)) {
                $debug[$column] = [
                    'name'  => $u['name']  ?? '',
                    'size'  => $u['size']  ?? 0,
                    'error' => $u['error'] ?? null,
                ];
            } else {
                $debug[$column] = '(ausente em $_FILES)';
            }
        }
        Logger::info('handleTechnicalImages: $_FILES recebidos', ['product_id' => $productId, 'files' => $debug]);

        foreach ($fields as $column => $suffix) {
            $remove   = (bool) $request->post($column . '_remove', false);
            $uploaded = $_FILES[$column] ?? null;
            $hasUpload = is_array($uploaded) && ($uploaded['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

            if (!$remove && !$hasUpload) continue;

            // Recupera o path atual pra excluir o arquivo antigo
            $stmt = $this->pdo->prepare("SELECT {$column} FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $current = (string) $stmt->fetchColumn();

            $newPath = null;
            if ($hasUpload) {
                try {
                    $newPath = $this->uploads->store($uploaded, 'products', $productName . '-' . $suffix);
                } catch (\Throwable $e) {
                    Logger::warning('Falha em upload de imagem técnica', ['col' => $column, 'err' => $e->getMessage()]);
                    Session::flash('error', "Falha ao salvar imagem técnica ({$column}): " . $e->getMessage());
                    continue;
                }
            }

            // Atualiza no banco
            $this->pdo->prepare("UPDATE products SET {$column} = ? WHERE id = ?")
                ->execute([$newPath, $productId]);

            // Apaga o arquivo antigo se foi substituído ou removido
            if ($current !== '' && $current !== $newPath) {
                $this->uploads->delete('products/' . $current);
            }

            // Como o admin tem APENAS UM campo de imagem técnica (dimensions OU modulação,
            // nunca ambos), ao subir/remover dimensions tambem limpa modulation
            // (e vice-versa) pro public mostrar SEMPRE a mais recente.
            if ($hasUpload || $remove) {
                $otherColumn = $column === 'dimensions_image_path'
                    ? 'modulation_image_path'
                    : 'dimensions_image_path';
                $stmt2 = $this->pdo->prepare("SELECT {$otherColumn} FROM products WHERE id = ?");
                $stmt2->execute([$productId]);
                $otherCurrent = (string) $stmt2->fetchColumn();
                if ($otherCurrent !== '') {
                    $this->pdo->prepare("UPDATE products SET {$otherColumn} = NULL WHERE id = ?")
                        ->execute([$productId]);
                    $this->uploads->delete('products/' . $otherCurrent);
                }
            }
        }
    }

    private function handleUploadedImages(int $productId, Request $request, string $productName): void
    {
        $files = $_FILES['images'] ?? null;
        if (!$files || empty($files['name']) || !is_array($files['name'])) {
            return;
        }
        $countExisting = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM product_images WHERE product_id = {$productId}")
            ->fetchColumn();

        for ($i = 0; $i < count($files['name']); $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
            $file = [
                'name'     => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
                'type'     => $files['type'][$i] ?? '',
            ];
            try {
                $filename = $this->uploads->store($file, 'products', $productName);
                $isMain = $countExisting === 0 ? 1 : 0;
                $this->pdo->prepare(
                    "INSERT INTO product_images (product_id, file_path, alt_text, is_main, sort_order)
                     VALUES (?, ?, ?, ?, ?)"
                )->execute([$productId, $filename, $productName, $isMain, $countExisting + $i]);
                if ($isMain) {
                    $this->pdo->prepare("UPDATE products SET hero_image_path = ? WHERE id = ?")
                        ->execute([$filename, $productId]);
                }
                $countExisting++;
            } catch (\Throwable $e) {
                Logger::warning('Falha em upload', ['err' => $e->getMessage()]);
                Session::flash('error', 'Algumas imagens não foram salvas: ' . $e->getMessage());
            }
        }
    }
}
