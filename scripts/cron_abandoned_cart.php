<?php

declare(strict_types=1);

/**
 * Cron diário: envia email de lembrete para carrinhos sem atividade
 * há ABANDONED_CART_DAYS dias. Envia no máximo 1 lembrete por carrinho.
 *
 * Cron sugerido (SiteGround):
 *   0 9 * * *  /usr/bin/php /home/u2550-7wftgcpgoimd/public_html/catalogo2/scripts/cron_abandoned_cart.php
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Services\MailService;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
Logger::setLogDir(BASE_PATH . '/storage/logs');

$days = (int) Config::get('ABANDONED_CART_DAYS', 3);
$pdo  = Database::connection();
$mail = new MailService();

$sql = "SELECT c.id AS cart_id, c.user_id, c.last_activity_at,
               u.name, u.email
          FROM carts c
          JOIN users u ON u.id = c.user_id
         WHERE c.user_id IS NOT NULL
           AND c.abandoned_email_sent_at IS NULL
           AND c.last_activity_at < NOW() - INTERVAL :d DAY
           AND EXISTS (SELECT 1 FROM cart_items ci WHERE ci.cart_id = c.id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['d' => $days]);
$carts = $stmt->fetchAll();

$sent = 0;
foreach ($carts as $c) {
    // Recupera itens
    $items = $pdo->prepare(
        "SELECT ci.quantity, p.name, p.sku, p.slug
           FROM cart_items ci JOIN products p ON p.id = ci.product_id
          WHERE ci.cart_id = ?"
    );
    $items->execute([$c['cart_id']]);
    $rows = $items->fetchAll();

    $ok = $mail->send(
        (string) $c['email'],
        'Você esqueceu itens no seu orçamento',
        'abandoned_cart',
        ['user' => $c, 'items' => $rows]
    );

    if ($ok) {
        $pdo->prepare("UPDATE carts SET abandoned_email_sent_at = NOW() WHERE id = ?")
            ->execute([$c['cart_id']]);
        $sent++;
    }
}

Logger::info('cron_abandoned_cart', ['carts_found' => count($carts), 'emails_sent' => $sent]);
fwrite(STDOUT, "✓ {$sent} lembrete(s) enviado(s) (de " . count($carts) . " carrinhos).\n");
