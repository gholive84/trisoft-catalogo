<?php

declare(strict_types=1);

/**
 * Executor de migrations.
 *
 * Aplica todos os arquivos .sql em database/migrations/ em ordem alfabética,
 * registrando os já aplicados em uma tabela `migrations`.
 *
 * Uso:
 *   php database/migrate.php           # aplica as pendentes
 *   php database/migrate.php --status  # lista status
 *   php database/migrate.php --fresh   # DROP de todas as tabelas e re-aplica (cuidado!)
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;

define('BASE_PATH', dirname(__DIR__));

Config::boot(BASE_PATH);
Logger::setLogDir(BASE_PATH . '/storage/logs');

$args   = $argv ?? [];
$fresh  = in_array('--fresh', $args, true);
$status = in_array('--status', $args, true);

try {
    $pdo = Database::connection();
} catch (Throwable $e) {
    fwrite(STDERR, "❌ Erro ao conectar ao banco: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Tabela de controle
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS migrations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

if ($fresh) {
    fwrite(STDOUT, "⚠️  --fresh: removendo TODAS as tabelas do banco...\n");
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
        fwrite(STDOUT, "   DROP {$t}\n");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    // Recria tabela de controle
    $pdo->exec(
        "CREATE TABLE migrations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

// Lê migrations já aplicadas
$applied = $pdo->query("SELECT filename FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
$applied = array_flip($applied);

// Lista arquivos
$files = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];
sort($files);

if ($status) {
    fwrite(STDOUT, "Status das migrations:\n");
    foreach ($files as $f) {
        $base = basename($f);
        $mark = isset($applied[$base]) ? '✓ aplicada' : '· pendente';
        fwrite(STDOUT, "  {$mark}  {$base}\n");
    }
    exit(0);
}

$count = 0;
foreach ($files as $file) {
    $name = basename($file);
    if (isset($applied[$name])) {
        continue;
    }

    $sql = file_get_contents($file);
    if (!is_string($sql) || trim($sql) === '') {
        fwrite(STDERR, "Migration vazia: {$name}\n");
        continue;
    }

    fwrite(STDOUT, "→ Aplicando {$name}... ");

    // Observação: MySQL faz implicit commit em DDL (CREATE TABLE, ALTER, etc.),
    // então não envolvemos as migrations em transação. Cada statement aplica
    // imediatamente; o registro em `migrations` é gravado ao final.
    try {
        foreach (splitSql($sql) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') {
                continue;
            }
            $pdo->exec($stmt);
        }
        $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)")->execute([$name]);
        fwrite(STDOUT, "OK\n");
        $count++;
    } catch (Throwable $e) {
        fwrite(STDERR, "ERRO\n   {$e->getMessage()}\n");
        Logger::error('Migration falhou', ['file' => $name, 'error' => $e->getMessage()]);
        exit(1);
    }
}

if ($count === 0) {
    fwrite(STDOUT, "Nada para aplicar — banco já está atualizado.\n");
} else {
    fwrite(STDOUT, "✅ {$count} migration(s) aplicada(s).\n");
}

/**
 * Divide um arquivo SQL em statements respeitando ';' como delimitador.
 * Suficiente para nossos arquivos (sem stored procedures).
 */
function splitSql(string $sql): array
{
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
    return array_filter(array_map('trim', explode(';', $sql)));
}
