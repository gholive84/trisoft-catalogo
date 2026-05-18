<?php

declare(strict_types=1);

/**
 * Endpoint de deploy via URL.
 *
 * Uso: GET /catalogo2/_deploy.php?token=XXX
 * Token é validado contra DEPLOY_TOKEN do .env.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

App\Core\Config::boot(BASE_PATH);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

$expected = App\Core\Config::get('DEPLOY_TOKEN', '');
$got      = $_GET['token'] ?? '';

if ($expected === '' || !hash_equals($expected, (string) $got)) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

chdir(BASE_PATH);

echo "=== git fetch ===\n";
echo shell_exec('git fetch origin main 2>&1');

echo "\n=== git reset --hard origin/main ===\n";
echo shell_exec('git reset --hard origin/main 2>&1');

echo "\n=== HEAD ===\n";
echo shell_exec('git log -1 --oneline 2>&1');

echo "\nOK\n";
