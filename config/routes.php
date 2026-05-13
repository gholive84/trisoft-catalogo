<?php

declare(strict_types=1);

/**
 * Definição centralizada de rotas.
 *
 * Espera receber uma instância de Router via $router.
 *
 * Convenção:
 * - Public:   rotas livres
 * - Customer: /minha-conta/*  (AuthMiddleware)
 * - Admin:    /admin/*        (RoleMiddleware com admin/editor/seller)
 */

use App\Core\Router;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Controllers\Public\AuthController;
use App\Controllers\Public\CatalogController;
use App\Controllers\Public\HomeController;
use App\Controllers\Public\ProductController;
use App\Controllers\Public\SearchController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RoleMiddleware;

/** @var Router $router */

// ============== PÚBLICO ==============
$router->get('/', [HomeController::class, 'index']);

// Catálogo
$router->get('/categoria/{slug}', [CatalogController::class, 'show']);
$router->get('/produto/{slug}',   [ProductController::class, 'show']);
$router->get('/busca',            [SearchController::class, 'index']);

// Carrinho (Sprint 3) — stubs amigáveis
$router->get('/carrinho', function () {
    return '<!doctype html><meta charset="utf-8"><div style="font-family:sans-serif;padding:2rem">Carrinho em construção (Sprint 3). <a href="' . url('/') . '">Voltar</a></div>';
});
$router->post('/carrinho/adicionar', function () {
    \App\Core\Session::flash('info', 'O carrinho será habilitado em breve.');
    \App\Core\Response::back();
}, [new CsrfMiddleware()]);

// Auth
$router->get('/login',    [AuthController::class, 'showLogin']);
$router->post('/login',   [AuthController::class, 'login'],    [new CsrfMiddleware()]);
$router->get('/cadastro', [AuthController::class, 'showRegister']);
$router->post('/cadastro',[AuthController::class, 'register'], [new CsrfMiddleware()]);
$router->post('/logout',  [AuthController::class, 'logout'],   [new CsrfMiddleware()]);

// ============== ÁREA DO CLIENTE ==============
$router->group(
    [
        'prefix'      => '/minha-conta',
        'middlewares' => [new AuthMiddleware()],
    ],
    function (Router $r) {
        $r->get('/', [CustomerDashboard::class, 'index']);
    }
);

// ============== ADMIN ==============
$router->group(
    [
        'prefix'      => '/admin',
        'middlewares' => [new RoleMiddleware('admin', 'editor', 'seller')],
    ],
    function (Router $r) {
        $r->get('/', [AdminDashboard::class, 'index']);
    }
);
