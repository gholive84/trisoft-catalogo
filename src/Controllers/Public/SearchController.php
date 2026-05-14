<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;

/**
 * /busca redireciona para a home/listagem unificada com a query como tag.
 * (A listagem unificada já lida com `?q=` e renderiza tag "Busca: termo".)
 */
final class SearchController
{
    public function index(Request $request): never
    {
        $q = trim((string) $request->query('q', ''));
        Response::redirect(url($q !== '' ? '/?q=' . rawurlencode($q) : '/'));
    }
}
