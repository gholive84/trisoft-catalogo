<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Auth;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Repositories\UserRepository;
use App\Services\AuthService;

final class AuthController
{
    private View $view;
    private AuthService $auth;
    private UserRepository $users;

    public function __construct()
    {
        $this->view  = new View(base_path('templates'));
        $this->auth  = new AuthService();
        $this->users = new UserRepository();
    }

    /* ---------- Login ---------- */

    public function showLogin(Request $request): string
    {
        if (Auth::check()) {
            Response::redirect(url($this->dashboardUrlFor(Auth::role())));
        }
        return $this->view->render('public/login', [
            'title' => 'Entrar',
        ]);
    }

    public function login(Request $request): never
    {
        $data = $request->only(['email', 'password']);

        $v = new Validator($data, [
            'email'    => 'required|email|max:150',
            'password' => 'required|min:6|max:255',
        ]);

        if ($v->fails()) {
            Session::flashInput(['email' => $data['email'] ?? '']);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url('login'));
        }

        $user = $this->auth->attempt((string) $data['email'], (string) $data['password']);
        if ($user === null) {
            Logger::warning('Login falhou', ['email' => $data['email'], 'ip' => $request->ip()]);
            Session::flashInput(['email' => $data['email']]);
            Session::flash('error', 'E-mail ou senha inválidos.');
            Response::redirect(url('login'));
        }

        $intended = Session::get('_intended_url');
        Session::forget('_intended_url');

        $target = $intended ?: $this->dashboardUrlFor((string) $user['role']);
        Response::redirect(url($target));
    }

    /* ---------- Cadastro ---------- */

    public function showRegister(Request $request): string
    {
        if (Auth::check()) {
            Response::redirect(url($this->dashboardUrlFor(Auth::role())));
        }
        return $this->view->render('public/register', [
            'title' => 'Criar conta',
        ]);
    }

    public function register(Request $request): never
    {
        $data = $request->only([
            'name', 'email', 'phone', 'document', 'company_name',
            'password', 'password_confirmation', 'terms',
        ]);

        $v = new Validator($data, [
            'name'                  => 'required|max:150',
            'email'                 => 'required|email|max:150',
            'password'              => 'required|min:8|max:255|confirmed',
            'phone'                 => 'max:20',
            'document'              => 'max:20',
            'company_name'          => 'max:200',
        ]);

        if ($v->fails()) {
            Session::flashInput($data);
            Session::flash('error', $v->firstError() ?? 'Verifique os dados.');
            Response::redirect(url('cadastro'));
        }

        if (empty($data['terms'])) {
            Session::flashInput($data);
            Session::flash('error', 'Você precisa aceitar os termos para continuar.');
            Response::redirect(url('cadastro'));
        }

        if ($this->users->emailExists((string) $data['email'])) {
            Session::flashInput($data);
            Session::flash('error', 'Este e-mail já está cadastrado.');
            Response::redirect(url('cadastro'));
        }

        $this->auth->register($data);

        Session::flash('success', 'Cadastro realizado! Bem-vindo(a).');
        Response::redirect(url('minha-conta'));
    }

    /* ---------- Logout ---------- */

    public function logout(Request $request): never
    {
        $this->auth->logout();
        Session::flash('success', 'Você saiu da conta.');
        Response::redirect(url('/'));
    }

    /* ---------- Helpers ---------- */

    private function dashboardUrlFor(?string $role): string
    {
        return match ($role) {
            'admin', 'editor', 'seller' => 'admin',
            default => 'minha-conta',
        };
    }
}
