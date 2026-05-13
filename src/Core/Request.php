<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Wrapper de requisição HTTP.
 */
final class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $post;
    private array $files;
    private array $server;
    private array $cookies;
    private array $routeParams = [];

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->query   = $_GET ?? [];
        $this->post    = $_POST ?? [];
        $this->files   = $_FILES ?? [];
        $this->server  = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Remove BASE_URL para roteamento interno
        $base = Config::baseUrl();
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        // Honra _method override em POST (forms HTML não suportam PUT/PATCH/DELETE nativamente)
        if ($this->method === 'POST' && isset($this->post['_method'])) {
            $override = strtoupper((string) $this->post['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $this->method = $override;
            }
        }

        $this->path = $uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    public function only(array $keys): array
    {
        $out = [];
        $all = $this->all();
        foreach ($keys as $k) {
            if (array_key_exists($k, $all)) {
                $out[$k] = $all[$k];
            }
        }
        return $out;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return isset($this->server[$key]) ? (string) $this->server[$key] : null;
    }

    public function ip(): string
    {
        return (string) ($this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function userAgent(): string
    {
        return (string) ($this->server['HTTP_USER_AGENT'] ?? '');
    }

    public function referer(): string
    {
        return (string) ($this->server['HTTP_REFERER'] ?? '');
    }

    public function isAjax(): bool
    {
        return strtolower((string) ($this->server['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    public function wantsJson(): bool
    {
        $accept = (string) ($this->server['HTTP_ACCEPT'] ?? '');
        return $this->isAjax() || str_contains($accept, 'application/json');
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function param(string $name, mixed $default = null): mixed
    {
        return $this->routeParams[$name] ?? $default;
    }

    public function routeParams(): array
    {
        return $this->routeParams;
    }

    public function csrfToken(): ?string
    {
        return $this->post['_csrf'] ?? $this->header('X-CSRF-Token');
    }
}
