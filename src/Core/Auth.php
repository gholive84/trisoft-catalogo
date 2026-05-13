<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Mantém o estado de autenticação na sessão.
 * Acesso conveniente ao usuário logado a partir de qualquer lugar.
 */
final class Auth
{
    private const SESSION_KEY = '_auth_user';

    private static ?array $cachedUser = null;

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::put(self::SESSION_KEY, [
            'id'    => (int) $user['id'],
            'name'  => (string) $user['name'],
            'email' => (string) $user['email'],
            'role'  => (string) $user['role'],
        ]);
        self::$cachedUser = null;
    }

    public static function logout(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::regenerate();
        self::$cachedUser = null;
    }

    public static function check(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function user(): ?array
    {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }
        self::$cachedUser = Session::get(self::SESSION_KEY);
        return self::$cachedUser;
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u['id'] ?? null;
    }

    public static function role(): ?string
    {
        $u = self::user();
        return $u['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $r = self::role();
        return $r !== null && in_array($r, $roles, true);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isStaff(): bool
    {
        return self::hasRole('admin', 'editor', 'seller');
    }
}
