<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Logger simples baseado em arquivo. Escreve em storage/logs/app-YYYY-MM-DD.log.
 */
final class Logger
{
    private static ?string $logDir = null;

    public static function setLogDir(string $path): void
    {
        self::$logDir = rtrim($path, "/\\");
    }

    public static function debug(string $message, array $context = []): void
    {
        self::write('DEBUG', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $dir = self::$logDir ?? (defined('BASE_PATH') ? BASE_PATH . '/storage/logs' : __DIR__ . '/../../storage/logs');

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $file = $dir . '/app-' . date('Y-m-d') . '.log';

        $ctx = $context === [] ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $line = sprintf("[%s] %s: %s%s\n", date('Y-m-d H:i:s'), $level, $message, $ctx);

        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
