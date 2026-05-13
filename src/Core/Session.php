<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Wrapper de sessão PHP com configuração segura.
 */
final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $lifetime = (int) Config::get('SESSION_LIFETIME_MINUTES', 120) * 60;
        $secure   = (bool) Config::get('SESSION_SECURE', false);
        $httpOnly = (bool) Config::get('SESSION_HTTPONLY', true);
        $sameSite = (string) Config::get('SESSION_SAMESITE', 'Lax');
        $name     = (string) Config::get('SESSION_NAME', 'trisoft_session');

        session_name($name);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);

        session_start();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flush(): void
    {
        $_SESSION = [];
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /* ---------- Flash messages ---------- */

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Recupera input enviado em request anterior (após erro de validação).
     */
    public static function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }

    public static function flashInput(array $input): void
    {
        $_SESSION['_old'] = $input;
    }

    public static function clearOld(): void
    {
        unset($_SESSION['_old']);
    }
}
