<?php

declare(strict_types=1);

/**
 * Definição centralizada de rotas.
 *
 * - Public:   livres
 * - Customer: /minha-conta/*  (AuthMiddleware)
 * - Admin:    /admin/*        (RoleMiddleware: admin/editor/seller)
 *             /admin/usuarios* somente admin
 */

use App\Core\Router;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Controllers\Customer\OrderController as CustomerOrderController;
use App\Controllers\Public\AuthController;
use App\Controllers\Public\CartApiController;
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

$router->get('/categoria/{slug}', [CatalogController::class, 'show']);
$router->get('/produto/{slug}',   [ProductController::class, 'show']);
$router->get('/busca',            [SearchController::class, 'index']);

$router->get('/carrinho',                      [CartController::class, 'show']);
$router->post('/carrinho/adicionar',           [CartController::class, 'add'],          [new CsrfMiddleware()]);
$router->post('/carrinho/atualizar',           [CartController::class, 'update'],       [new CsrfMiddleware()]);
$router->post('/carrinho/remover',             [CartController::class, 'remove'],       [new CsrfMiddleware()]);
$router->post('/carrinho/limpar',              [CartController::class, 'clear'],        [new CsrfMiddleware()]);
$router->post('/carrinho/solicitar-orcamento', [CartController::class, 'requestQuote'], [new CsrfMiddleware()]);

// AJAX API do carrinho (JSON)
$router->post('/api/carrinho/adicionar', [CartApiController::class, 'add']);
$router->post('/api/carrinho/atualizar', [CartApiController::class, 'update']);
$router->post('/api/carrinho/remover',   [CartApiController::class, 'remove']);
$router->get('/api/carrinho',            [CartApiController::class, 'state']);

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
$staffMw = [new RoleMiddleware('admin', 'editor', 'seller')];
$adminMw = [new RoleMiddleware('admin')];
$csrf    = new CsrfMiddleware();

$router->group(
    ['prefix' => '/admin', 'middlewares' => $staffMw],
    function (Router $r) use ($csrf, $adminMw) {

        $r->get('/', [AdminDashboard::class, 'index']);

        // Produtos
        $r->get('/produtos',                [AdminProductController::class, 'index']);
        $r->get('/produtos/novo',           [AdminProductController::class, 'create']);
        $r->post('/produtos',               [AdminProductController::class, 'store'],          [$csrf]);
        $r->get('/produtos/{id}/editar',    [AdminProductController::class, 'edit']);
        $r->put('/produtos/{id}',           [AdminProductController::class, 'update'],         [$csrf]);
        $r->post('/produtos/{id}',          [AdminProductController::class, 'update'],         [$csrf]);
        $r->post('/produtos/{id}/excluir',  [AdminProductController::class, 'destroy'],        [$csrf]);
        $r->post('/produtos/{id}/imagens/principal', [AdminProductController::class, 'setMainImage'], [$csrf]);
        $r->post('/produtos/{id}/imagens/excluir',   [AdminProductController::class, 'deleteImage'],  [$csrf]);

        // Categorias
        $r->get('/categorias',                [AdminCategoryController::class, 'index']);
        $r->get('/categorias/nova',           [AdminCategoryController::class, 'create']);
        $r->post('/categorias',               [AdminCategoryController::class, 'store'],   [$csrf]);
        $r->get('/categorias/{id}/editar',    [AdminCategoryController::class, 'edit']);
        $r->put('/categorias/{id}',           [AdminCategoryController::class, 'update'],  [$csrf]);
        $r->post('/categorias/{id}',          [AdminCategoryController::class, 'update'],  [$csrf]);
        $r->post('/categorias/{id}/excluir',  [AdminCategoryController::class, 'destroy'], [$csrf]);

        // Orçamentos
        $r->get('/orcamentos',                  [AdminOrderController::class, 'index']);
        $r->get('/orcamentos/{id}',             [AdminOrderController::class, 'show']);
        $r->post('/orcamentos/{id}/responder',  [AdminOrderController::class, 'respond'], [$csrf]);
        $r->post('/orcamentos/{id}/cancelar',   [AdminOrderController::class, 'cancel'],  [$csrf]);
    }
);

// Apenas admin
$router->group(
    ['prefix' => '/admin', 'middlewares' => $adminMw],
    function (Router $r) use ($csrf) {
        $r->get('/usuarios',               [AdminUserController::class, 'index']);
        $r->get('/usuarios/novo',          [AdminUserController::class, 'create']);
        $r->post('/usuarios',              [AdminUserController::class, 'store'],   [$csrf]);
        $r->get('/usuarios/{id}/editar',   [AdminUserController::class, 'edit']);
        $r->put('/usuarios/{id}',          [AdminUserController::class, 'update'],  [$csrf]);
        $r->post('/usuarios/{id}',         [AdminUserController::class, 'update'],  [$csrf]);
        $r->post('/usuarios/{id}/excluir', [AdminUserController::class, 'destroy'], [$csrf]);

        // Stubs Sprint 5
        $r->get('/analytics',     fn () => '<!doctype html><meta charset=utf-8><div style="font-family:system-ui;padding:3rem">Analytics — Sprint 5</div>');
        $r->get('/configuracoes', fn () => '<!doctype html><meta charset=utf-8><div style="font-family:system-ui;padding:3rem">Configurações — Sprint 5</div>');
    }
);
