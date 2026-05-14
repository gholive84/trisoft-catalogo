<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Repositories\UserRepository;

final class UserController
{
    private View $view;
    private UserRepository $users;
    private \PDO $pdo;

    public function __construct()
    {
        $this->view  = new View(base_path('templates'));
        $this->users = new UserRepository();
        $this->pdo   = Database::connection();
    }

    public function index(Request $request): string
    {
        $role    = (string) $request->query('role', '');
        $q       = trim((string) $request->query('q', ''));
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;

        $where  = "deleted_at IS NULL";
        $params = [];
        if ($role !== '') { $where .= " AND role = :r"; $params['r'] = $role; }
        if ($q !== '') {
            $where .= " AND (name LIKE :q OR email LIKE :q OR company_name LIKE :q)";
            $params['q'] = "%{$q}%";
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT id, name, email, role, status, company_name, created_at, last_login_at
                  FROM users
                 WHERE {$where}
              ORDER BY created_at DESC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        $kpis = [];
        $rows = $this->pdo->query("SELECT role, COUNT(*) AS n FROM users WHERE deleted_at IS NULL GROUP BY role")->fetchAll();
        foreach ($rows as $r) $kpis[$r['role']] = (int) $r['n'];

        return $this->view->render('admin/users/index', [
            'title'    => 'Usuários',
            'items'    => $items,
            'total'    => $total,
            'kpis'     => $kpis,
            'q'        => $q,
            'role'     => $role,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    public function create(Request $request): string
    {
        return $this->view->render('admin/users/form', [
            'title' => 'Novo usuário',
            'user'  => ['id' => null, 'name' => '', 'email' => '', 'role' => 'customer',
                         'status' => 'active', 'phone' => '', 'document' => '', 'company_name' => ''],
            'isNew' => true,
        ]);
    }

    public function edit(Request $request): string
    {
        $id = (int) $request->param('id');
        $user = $this->users->findById($id);
        if ($user === null) Response::abort(404);
        return $this->view->render('admin/users/form', [
            'title' => $user['name'],
            'user'  => $user,
            'isNew' => false,
        ]);
    }

    public function store(Request $request): never
    {
        $data = $this->extractData($request);
        $v = new Validator($data + ['password' => $request->post('password', '')], [
            'name'     => 'required|max:150',
            'email'    => 'required|email|max:150',
            'password' => 'required|min:8|max:255',
            'role'     => 'required|in:admin,editor,seller,customer',
            'status'   => 'required|in:active,inactive,pending',
        ]);
        if ($v->fails()) {
            Session::flashInput($data);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url('admin/usuarios/novo'));
        }
        if ($this->users->emailExists($data['email'])) {
            Session::flash('error', 'E-mail já cadastrado.');
            Response::redirect(url('admin/usuarios/novo'));
        }

        $id = $this->users->create($data + [
            'password_hash' => password_hash($request->post('password'), PASSWORD_BCRYPT),
        ]);

        Session::flash('success', 'Usuário criado.');
        Response::redirect(url("admin/usuarios/{$id}/editar"));
    }

    public function update(Request $request): never
    {
        $id = (int) $request->param('id');
        $user = $this->users->findById($id);
        if ($user === null) Response::abort(404);

        $data = $this->extractData($request);
        $v = new Validator($data, [
            'name'   => 'required|max:150',
            'email'  => 'required|email|max:150',
            'role'   => 'required|in:admin,editor,seller,customer',
            'status' => 'required|in:active,inactive,pending',
        ]);
        if ($v->fails()) {
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url("admin/usuarios/{$id}/editar"));
        }

        $this->users->update($id, $data);

        // Senha opcional (só atualiza se preenchida)
        $newPassword = (string) $request->post('password', '');
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                Session::flash('error', 'Senha deve ter ao menos 8 caracteres.');
                Response::redirect(url("admin/usuarios/{$id}/editar"));
            }
            $this->users->updatePassword($id, password_hash($newPassword, PASSWORD_BCRYPT));
        }

        Session::flash('success', 'Usuário atualizado.');
        Response::redirect(url("admin/usuarios/{$id}/editar"));
    }

    public function destroy(Request $request): never
    {
        $id = (int) $request->param('id');
        if ($id === (int) Auth::id()) {
            Session::flash('error', 'Você não pode excluir o próprio usuário.');
            Response::redirect(url('admin/usuarios'));
        }
        // Soft delete
        $this->pdo->prepare("UPDATE users SET deleted_at = NOW(), status = 'inactive' WHERE id = ?")
            ->execute([$id]);
        Session::flash('success', 'Usuário removido.');
        Response::redirect(url('admin/usuarios'));
    }

    private function extractData(Request $request): array
    {
        return [
            'name'         => trim((string) $request->post('name', '')),
            'email'        => strtolower(trim((string) $request->post('email', ''))),
            'role'         => (string) $request->post('role', 'customer'),
            'status'       => (string) $request->post('status', 'active'),
            'phone'        => trim((string) $request->post('phone', '')) ?: null,
            'document'     => trim((string) $request->post('document', '')) ?: null,
            'company_name' => trim((string) $request->post('company_name', '')) ?: null,
        ];
    }
}
