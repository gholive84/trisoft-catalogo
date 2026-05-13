<?php

declare(strict_types=1);

/**
 * Definição centralizada de rotas.
 *
 * Convenção:
 * - Public:   rotas livres (algumas exigem auth, ex.: carrinho)
 * - Customer: /minha-conta/*  (AuthMiddleware)
 * - Admin:    /admin/*        (RoleMiddleware com admin/editor/seller)
 */

use App\Core\Router;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Controllers\Customer\OrderController as CustomerOrderController;
use App\Controllers\Public\AuthController;
use App\Controllers\Public\CartController;
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

// Carrinho de orçamento
$router->get('/carrinho',                    [CartController::class, 'show']);
$router->post('/carrinho/adicionar',         [CartController::class, 'add'],          [new CsrfMiddleware()]);
$router->post('/carrinho/atualizar',         [CartController::class, 'update'],       [new CsrfMiddleware()]);
$router->post('/carrinho/remover',           [CartController::class, 'remove'],       [new CsrfMiddleware()]);
$router->post('/carrinho/limpar',            [CartController::class, 'clear'],        [new CsrfMiddleware()]);
$router->post('/carrinho/solicitar-orcamento', [CartController::class, 'requestQuote'], [new CsrfMiddleware()]);

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
        $r->get('/',                       [CustomerDashboard::class, 'index']);
        $r->get('/orcamentos',             [CustomerOrderController::class, 'index']);
        $r->get('/orcamentos/{number}',    [CustomerOrderController::class, 'show']);

        // Stubs (próxima iteração)
        $stub = fn (string $titulo) => '<!doctype html><html lang="pt-BR"><meta charset="utf-8">'
            . '<title>' . $titulo . ' — Em breve</title><body style="font-family:system-ui,sans-serif;padding:3rem;background:#f9fafb;color:#111;">'
            . '<a href="' . url('minha-conta') . '" style="color:#6b7280;text-decoration:none;">← Voltar</a>'
            . '<h1 style="margin-top:1rem;">' . $titulo . '</h1>'
            . '<p style="color:#6b7280;">Esta seção está em construção.</p></body></html>';
        $r->get('/perfil',     fn () => $stub('Meus dados'));
        $r->get('/enderecos',  fn () => $stub('Meus endereços'));
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
