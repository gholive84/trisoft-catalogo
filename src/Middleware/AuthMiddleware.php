<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Garante que há usuário logado. Caso contrário, redireciona para /login.
 */
final class AuthMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (Auth::guest()) {
            Session::flash('error', 'Faça login para acessar esta área.');
            Session::put('_intended_url', $request->path());
            Response::redirect(url('login'));
        }
        return $next($request);
    }
}
