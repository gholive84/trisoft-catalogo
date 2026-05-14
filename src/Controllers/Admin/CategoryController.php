<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Services\CategoryTreeService;

final class CategoryController
{
    private View $view;
    private CategoryRepository $cats;
    private CategoryTreeService $tree;
    private \PDO $pdo;

    public function __construct()
    {
        $this->view = new View(base_path('templates'));
        $this->cats = new CategoryRepository();
        $this->tree = new CategoryTreeService($this->cats);
        $this->pdo  = Database::connection();
    }

    public function index(Request $request): string
    {
        // Contagem de produtos por categoria
        $counts = [];
        $rows = $this->pdo->query(
            "SELECT category_id, COUNT(*) AS n
               FROM product_categories
              GROUP BY category_id"
        )->fetchAll();
        foreach ($rows as $r) {
            $counts[(int) $r['category_id']] = (int) $r['n'];
        }

        return $this->view->render('admin/categories/index', [
            'title'    => 'Categorias',
            'tree'     => $this->tree->tree(),
            'allCategories' => $this->cats->all(),
            'counts'   => $counts,
        ]);
    }

    public function create(Request $request): string
    {
        return $this->view->render('admin/categories/form', [
            'title'    => 'Nova categoria',
            'category' => $this->emptyCategory(),
            'allCategories' => $this->cats->all(),
            'isNew'    => true,
        ]);
    }

    public function edit(Request $request): string
    {
        $id = (int) $request->param('id');
        $cat = $this->cats->findById($id);
        if ($cat === null) Response::abort(404);
        return $this->view->render('admin/categories/form', [
            'title'    => $cat['name'],
            'category' => $cat,
            'allCategories' => $this->cats->all(),
            'isNew'    => false,
        ]);
    }

    public function store(Request $request): never
    {
        $data = $this->extractData($request);
        $v = $this->validateData($data);
        if ($v->fails()) {
            Session::flashInput($data);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url('admin/categorias/nova'));
        }
        $slug = $data['slug'] !== '' ? $data['slug'] : slugify($data['name']);
        if ($this->slugExists($slug)) {
            $slug .= '-' . bin2hex(random_bytes(2));
        }
        $data['slug'] = $slug;

        $id = $this->cats->create($data);
        Session::flash('success', 'Categoria criada.');
        Response::redirect(url("admin/categorias/{$id}/editar"));
    }

    public function update(Request $request): never
    {
        $id = (int) $request->param('id');
        $cat = $this->cats->findById($id);
        if ($cat === null) Response::abort(404);

        $data = $this->extractData($request);
        $v = $this->validateData($data);
        if ($v->fails()) {
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url("admin/categorias/{$id}/editar"));
        }
        $slug = $data['slug'] !== '' ? $data['slug'] : slugify($data['name']);
        if ($slug !== $cat['slug'] && $this->slugExists($slug, $id)) {
            $slug .= '-' . bin2hex(random_bytes(2));
        }

        // Previne ciclos: parent_id não pode ser um descendente
        if ($data['parent_id'] !== null) {
            $descendants = $this->cats->descendantIds($id);
            if (in_array((int) $data['parent_id'], $descendants, true)) {
                Session::flash('error', 'A categoria pai não pode ser um descendente.');
                Response::redirect(url("admin/categorias/{$id}/editar"));
            }
        }

        $stmt = $this->pdo->prepare(
            "UPDATE categories SET
                parent_id = :pid, name = :name, slug = :slug,
                description = :desc, sort_order = :sort, is_active = :active,
                meta_title = :mt, meta_description = :md
              WHERE id = :id"
        );
        $stmt->execute([
            'id'     => $id,
            'pid'    => $data['parent_id'],
            'name'   => $data['name'],
            'slug'   => $slug,
            'desc'   => $data['description'] ?: null,
            'sort'   => (int) $data['sort_order'],
            'active' => (int) $data['is_active'],
            'mt'     => $data['meta_title'] ?: null,
            'md'     => $data['meta_description'] ?: null,
        ]);

        Session::flash('success', 'Categoria atualizada.');
        Response::redirect(url("admin/categorias/{$id}/editar"));
    }

    public function destroy(Request $request): never
    {
        $id = (int) $request->param('id');
        // Categoria com produtos → impedimos (poderia ser cascade no schema; mas
        // melhor evitar perda silenciosa de associações)
        $n = (int) $this->pdo->query(
            "SELECT COUNT(*) FROM product_categories WHERE category_id = " . (int) $id
        )->fetchColumn();
        if ($n > 0) {
            Session::flash('error', "Não é possível excluir: {$n} produto(s) ainda usam esta categoria.");
            Response::redirect(url('admin/categorias'));
        }
        $this->pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        Session::flash('success', 'Categoria excluída.');
        Response::redirect(url('admin/categorias'));
    }

    private function emptyCategory(): array
    {
        return [
            'id' => null, 'parent_id' => null, 'name' => '', 'slug' => '',
            'description' => '', 'image_path' => null, 'sort_order' => 0,
            'is_active' => 1, 'meta_title' => '', 'meta_description' => '',
        ];
    }

    private function extractData(Request $request): array
    {
        $pid = $request->post('parent_id');
        return [
            'parent_id'        => $pid !== '' && $pid !== null ? (int) $pid : null,
            'name'             => trim((string) $request->post('name', '')),
            'slug'             => trim((string) $request->post('slug', '')),
            'description'      => trim((string) $request->post('description', '')),
            'sort_order'       => (int) $request->post('sort_order', 0),
            'is_active'        => $request->post('is_active') ? 1 : 0,
            'meta_title'       => trim((string) $request->post('meta_title', '')),
            'meta_description' => trim((string) $request->post('meta_description', '')),
        ];
    }

    private function validateData(array $data): Validator
    {
        return new Validator($data, [
            'name' => 'required|max:150',
        ]);
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM categories WHERE slug = :slug" . ($excludeId !== null ? " AND id <> :id" : "") . " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $params = ['slug' => $slug];
        if ($excludeId !== null) $params['id'] = $excludeId;
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }
}
