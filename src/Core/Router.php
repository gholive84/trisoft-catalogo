<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

/**
 * Router simples baseado em expressões regulares.
 *
 * Suporta parâmetros nomeados: /produto/{slug} → captura "slug".
 * Suporta middlewares por rota (array de classes implementando handle(Request, callable): mixed).
 */
final class Router
{
    /** @var array<int, array<int, array{pattern:string,paramNames:array,handler:mixed,middlewares:array}>> */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    /** @var array<string, mixed> */
    private array $groupStack = [];

    public function get(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('PUT', $path, $handler, $middlewares);
    }

    public function patch(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('PATCH', $path, $handler, $middlewares);
    }

    public function delete(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Agrupa rotas com prefixo e/ou middlewares comuns.
     *
     * @param array{prefix?: string, middlewares?: array} $attrs
     */
    public function group(array $attrs, Closure $callback): void
    {
        $this->groupStack[] = $attrs;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function add(string $method, string $path, mixed $handler, array $middlewares): void
    {
        $prefix = '';
        $stackMiddlewares = [];
        foreach ($this->groupStack as $g) {
            if (isset($g['prefix'])) {
                $prefix .= '/' . trim($g['prefix'], '/');
            }
            if (isset($g['middlewares'])) {
                $stackMiddlewares = array_merge($stackMiddlewares, $g['middlewares']);
            }
        }

        $fullPath = ($prefix === '' ? '' : $prefix) . '/' . ltrim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');
        if ($fullPath === '/') {
            $pathToCompile = '/';
        } else {
            $pathToCompile = $fullPath;
        }

        [$pattern, $paramNames] = $this->compile($pathToCompile);

        $this->routes[$method][] = [
            'pattern'     => $pattern,
            'paramNames'  => $paramNames,
            'handler'     => $handler,
            'middlewares' => array_merge($stackMiddlewares, $middlewares),
        ];
    }

    private function compile(string $path): array
    {
        $paramNames = [];
        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
            function ($matches) use (&$paramNames) {
                $paramNames[] = $matches[1];
                $pattern = $matches[2] ?? '[^/]+';
                return '(' . $pattern . ')';
            },
            $path
        );
        return ['#^' . $regex . '$#', $paramNames];
    }

    /**
     * Resolve a requisição. Retorna a string renderizada ou termina via Response.
     */
    public function dispatch(Request $request): mixed
    {
        $method = $request->method();
        $path = rtrim($request->path(), '/');
        if ($path === '') {
            $path = '/';
        }

        if (!isset($this->routes[$method])) {
            Response::abort(405, 'Método não permitido.');
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $params = [];
                foreach ($route['paramNames'] as $i => $name) {
                    $params[$name] = $matches[$i] ?? null;
                }
                $request->setRouteParams($params);

                return $this->runWithMiddlewares($route['middlewares'], $route['handler'], $request);
            }
        }

        Response::abort(404, 'Página não encontrada.');
    }

    private function runWithMiddlewares(array $middlewares, mixed $handler, Request $request): mixed
    {
        // Constrói a chain: middlewares[0]→middlewares[1]→...→handler
        $next = fn(Request $req) => $this->invoke($handler, $req);

        foreach (array_reverse($middlewares) as $mw) {
            $previous = $next;
            $next = function (Request $req) use ($mw, $previous) {
                $instance = is_string($mw) ? new $mw() : $mw;
                return $instance->handle($req, $previous);
            };
        }

        return $next($request);
    }

    private function invoke(mixed $handler, Request $request): mixed
    {
        if ($handler instanceof Closure) {
            return $handler($request);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = is_string($class) ? new $class() : $class;
            return $instance->{$method}($request);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $instance = new $class();
            return $instance->{$method}($request);
        }

        throw new RuntimeException('Handler de rota inválido.');
    }
}
