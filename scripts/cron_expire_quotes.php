<?php

declare(strict_types=1);

/**
 * Cron diário: marca como `expired` orçamentos que passaram da data de validade.
 *
 * Cron sugerido (SiteGround):
 *   0 3 * * *  /usr/bin/php /home/u2550-7wftgcpgoimd/public_html/catalogo2/scripts/cron_expire_quotes.php
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Logger;
use App\Services\QuoteService;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
Logger::setLogDir(BASE_PATH . '/storage/logs');

try {
    $count = (new QuoteService())->expireOlderThanNow();
    Logger::info('cron_expire_quotes', ['expired' => $count]);
    fwrite(STDOUT, "✓ {$count} orçamento(s) expirado(s).\n");
} catch (Throwable $e) {
    Logger::error('cron_expire_quotes falhou', ['error' => $e->getMessage()]);
    fwrite(STDERR, "❌ Erro: " . $e->getMessage() . "\n");
    exit(1);
}
