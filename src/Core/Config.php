<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

/**
 * Centraliza acesso a variáveis de ambiente e configuração.
 * Carrega .env uma única vez.
 */
final class Config
{
    private static array $cache = [];
    private static bool $booted = false;

    public static function boot(string $basePath): void
    {
        if (self::$booted) {
            return;
        }

        if (file_exists($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->safeLoad();
        }

        date_default_timezone_set(self::get('APP_TIMEZONE', 'America/Sao_Paulo'));

        self::$booted = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        $value = match (strtolower((string) $value)) {
            'true', '(true)'  => true,
            'false', '(false)' => false,
            'null', '(null)'  => null,
            'empty', '(empty)' => '',
            default => $value,
        };

        self::$cache[$key] = $value;
        return $value;
    }

    public static function isDebug(): bool
    {
        return (bool) self::get('APP_DEBUG', false);
    }

    public static function isProduction(): bool
    {
        return self::get('APP_ENV', 'production') === 'production';
    }

    public static function baseUrl(): string
    {
        return rtrim((string) self::get('BASE_URL', ''), '/');
    }

    public static function appUrl(): string
    {
        return rtrim((string) self::get('APP_URL', ''), '/');
    }
}
