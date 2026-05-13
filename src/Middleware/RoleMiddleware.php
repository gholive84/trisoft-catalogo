<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

/**
 * Exige que o usuário logado tenha um dos roles fornecidos.
 *
 * Uso em rotas:
 *   middlewares: [new RoleMiddleware('admin'), ...]
 *   ou:           [RoleMiddleware::class . ':admin,editor']  (futuro)
 */
final class RoleMiddleware
{
    /** @var string[] */
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, callable $next): mixed
    {
        if (Auth::guest()) {
            Response::redirect(url('login'));
        }
        if ($this->roles !== [] && !Auth::hasRole(...$this->roles)) {
            Response::abort(403, 'Você não tem permissão para acessar esta área.');
        }
        return $next($request);
    }
}
