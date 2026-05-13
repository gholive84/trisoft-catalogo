<?php

declare(strict_types=1);

/**
 * Helpers globais. Carregados via composer "files" autoload.
 *
 * Convenção: nomes curtos e práticos; toda lógica complexa fica em classes.
 */

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('e')) {
    /**
     * Escape HTML seguro. Use SEMPRE em saída para o navegador.
     */
    function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /**
     * Constrói uma URL absoluta no domínio atual, respeitando BASE_URL.
     */
    function url(string $path = ''): string
    {
        $base = Config::baseUrl();
        $path = '/' . ltrim($path, '/');
        return $base . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * URL para um asset em /public/assets.
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    /**
     * URL para arquivo em /public/uploads.
     */
    function upload_url(string $path): string
    {
        return url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('current_url')) {
    function current_url(): string
    {
        return (string) ($_SERVER['REQUEST_URI'] ?? '/');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('method_field')) {
    /**
     * Spoof de método HTTP em forms (PUT/PATCH/DELETE).
     */
    function method_field(string $method): string
    {
        $method = strtoupper($method);
        return '<input type="hidden" name="_method" value="' . e($method) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::old($key, $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Lê (e consome) uma flash message.
     */
    function flash(string $key, mixed $default = null): mixed
    {
        return Session::getFlash($key, $default);
    }
}

if (!function_exists('auth')) {
    /**
     * Retorna o usuário logado (array) ou null.
     */
    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('auth_id')) {
    function auth_id(): ?int
    {
        return Auth::id();
    }
}

if (!function_exists('auth_role')) {
    function auth_role(): ?string
    {
        return Auth::role();
    }
}

if (!function_exists('has_role')) {
    function has_role(string ...$roles): bool
    {
        return Auth::hasRole(...$roles);
    }
}

if (!function_exists('slugify')) {
    /**
     * Converte uma string em slug ASCII compatível com URLs.
     */
    function slugify(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }
        $text = preg_replace('/[^A-Za-z0-9\s\-]/', '', $text) ?? $text;
        $text = preg_replace('/[\s\-]+/', '-', $text) ?? $text;
        $text = trim($text, '-');
        return strtolower($text);
    }
}

if (!function_exists('money_br')) {
    /**
     * Formata float como BRL: 1234.5 → "R$ 1.234,50".
     */
    function money_br(float|int|string|null $value): string
    {
        if ($value === null || $value === '') {
            return 'R$ 0,00';
        }
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }
}

if (!function_exists('date_br')) {
    function date_br(?string $datetime, string $format = 'd/m/Y'): string
    {
        if (!$datetime) {
            return '';
        }
        $ts = strtotime($datetime);
        return $ts ? date($format, $ts) : '';
    }
}

if (!function_exists('only_digits')) {
    function only_digits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value) ?? '';
    }
}

if (!function_exists('str_truncate')) {
    function str_truncate(string $text, int $limit = 100, string $suffix = '…'): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . $suffix;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $append = ''): string
    {
        $root = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        return $append === '' ? $root : $root . '/' . ltrim($append, '/');
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $append = ''): string
    {
        return base_path('storage' . ($append === '' ? '' : '/' . ltrim($append, '/')));
    }
}

if (!function_exists('upload_path')) {
    function upload_path(string $append = ''): string
    {
        return base_path('public/uploads' . ($append === '' ? '' : '/' . ltrim($append, '/')));
    }
}
