<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Geração e validação de tokens CSRF.
 * Token vive na sessão e é regenerado quando consumido.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::put(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function check(?string $token): bool
    {
        $expected = Session::get(self::SESSION_KEY);
        if (!is_string($expected) || $expected === '' || !is_string($token)) {
            return false;
        }
        return hash_equals($expected, $token);
    }

    public static function rotate(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
