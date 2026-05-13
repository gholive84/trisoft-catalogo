<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Valida o token CSRF em requisições não-GET.
 * Token vem em _csrf (form) ou X-CSRF-Token (header).
 */
final class CsrfMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (!Csrf::check($request->csrfToken())) {
                if ($request->wantsJson()) {
                    Response::json(['error' => 'Token CSRF inválido.'], 419);
                }
                Session::flash('error', 'Sessão expirada. Tente novamente.');
                Response::redirect($request->referer() ?: url('/'), 303);
            }
        }
        return $next($request);
    }
}
