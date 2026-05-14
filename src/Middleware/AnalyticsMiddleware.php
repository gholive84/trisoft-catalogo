<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Repositories\AnalyticsRepository;

/**
 * Registra cada page view de rotas públicas em `page_views`.
 *
 * Identifica visitantes via cookie `trisoft_visitor` (gera UUID se não existe,
 * válido por 30 dias). Detecta product_id / category_id pelo path.
 *
 * Não bloqueia o request — falhas são logadas e a request segue.
 */
final class AnalyticsMiddleware
{
    private const COOKIE_NAME = 'trisoft_visitor';
    private const COOKIE_DAYS = 30;

    public function handle(Request $request, callable $next): mixed
    {
        try {
            $this->record($request);
        } catch (\Throwable $e) {
            Logger::warning('AnalyticsMiddleware falhou', ['err' => $e->getMessage()]);
        }
        return $next($request);
    }

    public static function visitorId(): string
    {
        if (!empty($_COOKIE[self::COOKIE_NAME]) && preg_match('/^[a-f0-9-]{32,40}$/', $_COOKIE[self::COOKIE_NAME])) {
            return $_COOKIE[self::COOKIE_NAME];
        }
        $id = bin2hex(random_bytes(16));
        setcookie(
            self::COOKIE_NAME,
            $id,
            [
                'expires'  => time() + self::COOKIE_DAYS * 86400,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
        $_COOKIE[self::COOKIE_NAME] = $id;
        return $id;
    }

    private function record(Request $request): void
    {
        // Não trackeia AJAX nem assets nem requests não-GET
        if ($request->method() !== 'GET') return;
        if ($request->isAjax()) return;

        $path = $request->path();
        if ($path === '' || $path === '/') {
            // OK: home
        }
        // Não trackeia páginas de admin
        if (str_starts_with($path, '/admin')) return;
        if (str_starts_with($path, '/api/')) return;

        $sessionId = self::visitorId();
        $pdo       = Database::connection();
        $repo      = new AnalyticsRepository($pdo);

        $productId  = null;
        $categoryId = null;

        if (preg_match('#^/produto/([\w-]+)#', $path, $m)) {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
            $stmt->execute([$m[1]]);
            $productId = ($v = $stmt->fetchColumn()) !== false ? (int) $v : null;
        } elseif (preg_match('#^/categoria/([\w-]+)#', $path, $m)) {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
            $stmt->execute([$m[1]]);
            $categoryId = ($v = $stmt->fetchColumn()) !== false ? (int) $v : null;
        }

        $repo->recordPageView([
            'user_id'    => Auth::check() ? Auth::id() : null,
            'session_id' => $sessionId,
            'url'        => $request->path(),
            'referrer'   => $request->referer(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'product_id' => $productId,
            'category_id'=> $categoryId,
        ]);

        // Registra evento dedicado para certas páginas (facilita queries)
        if ($productId !== null) {
            $repo->recordEvent('view_product', ['product_id' => $productId], Auth::check() ? Auth::id() : null, $sessionId, $request->path());
        } elseif ($categoryId !== null) {
            $repo->recordEvent('view_category', ['category_id' => $categoryId], Auth::check() ? Auth::id() : null, $sessionId, $request->path());
        }

        // Busca: se path = '/' e tem ?q=, registra event search
        if (($path === '/' || $path === '/busca') && $request->query('q')) {
            $repo->recordEvent('search', [
                'query'   => (string) $request->query('q'),
                // results não é trivial calcular aqui (controller que faz a query) — deixa null
            ], Auth::check() ? Auth::id() : null, $sessionId, $request->path());
        }
    }
}
