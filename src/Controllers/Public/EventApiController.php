<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AnalyticsMiddleware;
use App\Repositories\AnalyticsRepository;

/**
 * Endpoint para registro de eventos customizados via JavaScript.
 *
 * POST /api/event  body: {name: "...", data: {...}}
 *
 * Lista de events suportados (convenção):
 *   - view_product, view_category (já registrados pelo middleware)
 *   - add_to_cart (já registrado pelo backend)
 *   - request_quote (idem)
 *   - search
 *   - duration (tempo em página — JS dispara no unload)
 */
final class EventApiController
{
    public function record(Request $request): never
    {
        $body = file_get_contents('php://input') ?: '';
        $payload = json_decode($body, true);
        if (!is_array($payload) || empty($payload['name'])) {
            Response::json(['success' => false, 'message' => 'name obrigatório'], 400);
        }

        $name = (string) $payload['name'];
        // Whitelist básica para evitar lixo (qualquer um pode chamar essa rota)
        $allowed = ['add_to_cart', 'remove_from_cart', 'request_quote', 'search',
                    'register', 'login', 'duration', 'click_cta', 'view_product',
                    'view_category'];
        if (!in_array($name, $allowed, true)) {
            Response::json(['success' => false, 'message' => 'event_name não permitido'], 400);
        }

        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $repo = new AnalyticsRepository();
        $repo->recordEvent(
            $name,
            $data,
            Auth::check() ? Auth::id() : null,
            AnalyticsMiddleware::visitorId(),
            $request->referer() ?: null
        );

        Response::json(['success' => true]);
    }
}
