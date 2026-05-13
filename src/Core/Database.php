<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Singleton de conexão PDO. Reutilizado em todos os repositories.
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function connection(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $host    = (string) Config::get('DB_HOST', 'localhost');
        $port    = (string) Config::get('DB_PORT', '3306');
        $dbName  = (string) Config::get('DB_DATABASE', '');
        $user    = (string) Config::get('DB_USERNAME', '');
        $pass    = (string) Config::get('DB_PASSWORD', '');
        $charset = (string) Config::get('DB_CHARSET', 'utf8mb4');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $dbName,
            $charset
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ];

        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            Logger::error('Falha ao conectar ao banco', ['msg' => $e->getMessage()]);
            throw new RuntimeException('Erro de conexão com o banco de dados.', 0, $e);
        }

        return self::$instance;
    }

    /**
     * Útil para testes: força nova conexão na próxima chamada.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
