<?php

declare(strict_types=1);

/**
 * Seed inicial:
 *  - cria usuário admin padrão (se não existir)
 *  - carrega settings padrão
 *
 * Uso:
 *   php database/seed.php
 *   php database/seed.php --admin-email=foo@bar.com --admin-password=segredo
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));

Config::boot(BASE_PATH);
$pdo = Database::connection();

// Parse argumentos
$args = [];
foreach ($argv as $a) {
    if (str_starts_with($a, '--') && str_contains($a, '=')) {
        [$k, $v] = explode('=', substr($a, 2), 2);
        $args[$k] = $v;
    }
}

$adminEmail    = $args['admin-email']    ?? 'admin@trisoft.com.br';
$adminName     = $args['admin-name']     ?? 'Administrador';
$adminPassword = $args['admin-password'] ?? bin2hex(random_bytes(6));
$generated     = !isset($args['admin-password']);

// settings
$settingsFile = __DIR__ . '/seeds/initial_data.sql';
if (file_exists($settingsFile)) {
    foreach (array_filter(array_map('trim', explode(';', (string) file_get_contents($settingsFile)))) as $stmt) {
        if ($stmt === '' || str_starts_with($stmt, '--')) continue;
        $pdo->exec($stmt);
    }
    fwrite(STDOUT, "✓ Settings padrão aplicados.\n");
}

// admin
$exists = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$exists->execute([$adminEmail]);
if ($exists->fetch()) {
    fwrite(STDOUT, "· Admin já existe ({$adminEmail}). Pulando.\n");
} else {
    $hash = password_hash($adminPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role, status, email_verified_at)
         VALUES (?, ?, ?, 'admin', 'active', NOW())"
    );
    $stmt->execute([$adminName, $adminEmail, $hash]);

    fwrite(STDOUT, "✓ Usuário admin criado.\n");
    fwrite(STDOUT, "   email: {$adminEmail}\n");
    if ($generated) {
        fwrite(STDOUT, "   senha (gerada — anote!): {$adminPassword}\n");
    } else {
        fwrite(STDOUT, "   senha: (a definida via --admin-password)\n");
    }
}

fwrite(STDOUT, "Seed concluído.\n");
