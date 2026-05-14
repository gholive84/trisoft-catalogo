<?php

declare(strict_types=1);

/**
 * Migração de clientes do WordPress legado (/catalogo) para o novo
 * sistema (catalogo2.users).
 *
 * Conecta em DOIS bancos:
 *   - WP source via WP_DB_* no .env (catalogo legado)
 *   - Destino atual via DB_* no .env (catalogo2)
 *
 * O que importa:
 *   - wp_users → users (email, display_name → name, user_registered → created_at)
 *   - wp_usermeta → preenche phone, document, company_name, endereços
 *
 * Senhas:
 *   - WordPress usa phpass que NÃO é compatível com password_verify (bcrypt)
 *   - Estratégia: cria usuário com hash aleatório + token de reset
 *     válido por 14 dias. Cliente recebe email "Defina sua senha"
 *     e cai num formulário simples (a implementar) /redefinir-senha?token=...
 *   - Campo `wp_legacy_id` preserva rastreabilidade
 *
 * Uso:
 *   php scripts/wp_migration.php --dry-run        # simula, não grava
 *   php scripts/wp_migration.php --limit=50       # processa só 50
 *   php scripts/wp_migration.php                  # migra tudo
 *   php scripts/wp_migration.php --send-emails    # dispara emails de reset
 *
 * Idempotente: skipa email que já existe ou wp_legacy_id já migrado.
 *
 * Gera /tmp/wp_migration_report.csv com status por usuário.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Services\MailService;

define('BASE_PATH', dirname(__DIR__));
Config::boot(BASE_PATH);
Logger::setLogDir(BASE_PATH . '/storage/logs');

/* ---------- Args ---------- */
$args = $argv;
array_shift($args);
$dryRun     = in_array('--dry-run', $args, true);
$sendEmails = in_array('--send-emails', $args, true);
$limit      = 0;
foreach ($args as $a) {
    if (preg_match('/^--limit=(\d+)$/', $a, $m)) $limit = (int) $m[1];
}

/* ---------- Conexões ---------- */
$dest = Database::connection();

$wpHost = (string) Config::get('WP_DB_HOST', 'localhost');
$wpDb   = (string) Config::get('WP_DB_DATABASE', '');
$wpUser = (string) Config::get('WP_DB_USERNAME', '');
$wpPass = (string) Config::get('WP_DB_PASSWORD', '');
$wpPort = (string) Config::get('WP_DB_PORT', '3306');
$wpPrefix = (string) Config::get('WP_DB_PREFIX', 'wp_');

if ($wpDb === '' || $wpUser === '') {
    fwrite(STDERR, "❌ Credenciais do banco WP não configuradas no .env (WP_DB_*).\n");
    exit(1);
}

try {
    $wp = new PDO(
        "mysql:host={$wpHost};port={$wpPort};dbname={$wpDb};charset=utf8mb4",
        $wpUser,
        $wpPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    fwrite(STDOUT, "✓ Conectado ao banco WP: {$wpDb}@{$wpHost}\n");
} catch (Throwable $e) {
    fwrite(STDERR, "❌ Falha ao conectar no banco WP: " . $e->getMessage() . "\n");
    exit(1);
}

/* ---------- Helpers ---------- */

function getMetaMap(PDO $wp, string $prefix, int $userId): array
{
    $stmt = $wp->prepare("SELECT meta_key, meta_value FROM {$prefix}usermeta WHERE user_id = ?");
    $stmt->execute([$userId]);
    $map = [];
    foreach ($stmt->fetchAll() as $row) {
        $map[$row['meta_key']] = $row['meta_value'];
    }
    return $map;
}

function pickName(array $meta, string $fallback): string
{
    $first = trim($meta['first_name'] ?? '');
    $last  = trim($meta['last_name'] ?? '');
    $full  = trim("{$first} {$last}");
    return $full !== '' ? $full : trim($fallback);
}

function pickDocument(array $meta): ?string
{
    $candidates = ['billing_cnpj', 'billing_cpf', 'billing_document', 'cnpj', 'cpf'];
    foreach ($candidates as $k) {
        if (!empty($meta[$k])) return mb_substr((string) $meta[$k], 0, 20);
    }
    return null;
}

function pickAddress(array $meta): ?array
{
    $street = trim((string) ($meta['billing_address_1'] ?? ''));
    $cep    = trim((string) ($meta['billing_postcode'] ?? ''));
    if ($street === '' || $cep === '') return null;
    return [
        'cep'           => $cep,
        'street'        => $street,
        'number'        => trim((string) ($meta['billing_number'] ?? 's/n')),
        'complement'    => trim((string) ($meta['billing_address_2'] ?? '')) ?: null,
        'neighborhood'  => trim((string) ($meta['billing_neighborhood'] ?? $meta['billing_district'] ?? '')),
        'city'          => trim((string) ($meta['billing_city'] ?? '')),
        'state'         => strtoupper(trim((string) ($meta['billing_state'] ?? ''))),
    ];
}

/* ---------- Migração ---------- */

$query = "SELECT ID, user_login, user_email, display_name, user_registered
            FROM {$wpPrefix}users
           WHERE user_email LIKE '%@%'
           ORDER BY ID ASC";
if ($limit > 0) $query .= " LIMIT {$limit}";
$wpUsers = $wp->query($query)->fetchAll();
fwrite(STDOUT, count($wpUsers) . " usuário(s) no WP elegíveis.\n\n");

$reportPath = '/tmp/wp_migration_report.csv';
$report = fopen($reportPath, 'w');
fputcsv($report, ['wp_id', 'email', 'name', 'status', 'address', 'reset_token', 'message']);

$inserted = 0;
$skipped  = 0;
$failed   = 0;
$emails   = 0;

foreach ($wpUsers as $u) {
    $wpId  = (int) $u['ID'];
    $email = strtolower(trim((string) $u['user_email']));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        fputcsv($report, [$wpId, $email, $u['display_name'], 'skip-invalid-email', '0', '', 'Email inválido']);
        $skipped++; continue;
    }

    // Já migrado?
    $check = $dest->prepare("SELECT id FROM users WHERE wp_legacy_id = ? OR email = ? LIMIT 1");
    $check->execute([$wpId, $email]);
    if ($check->fetchColumn()) {
        fputcsv($report, [$wpId, $email, $u['display_name'], 'skip-exists', '0', '', 'Já existe (por email ou wp_legacy_id)']);
        $skipped++; continue;
    }

    $meta = getMetaMap($wp, $wpPrefix, $wpId);
    $name = pickName($meta, (string) $u['display_name']);
    if ($name === '') $name = explode('@', $email)[0];

    $phone    = trim((string) ($meta['billing_phone'] ?? $meta['phone'] ?? '')) ?: null;
    if ($phone) $phone = mb_substr($phone, 0, 20);
    $document = pickDocument($meta);
    $company  = trim((string) ($meta['billing_company'] ?? '')) ?: null;
    if ($company) $company = mb_substr($company, 0, 200);

    // Hash placeholder (cliente não consegue logar sem reset)
    $passwordHash = password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT);
    $resetToken   = bin2hex(random_bytes(20));
    $resetExpires = date('Y-m-d H:i:s', strtotime('+14 days'));
    $registeredAt = !empty($u['user_registered']) && $u['user_registered'] !== '0000-00-00 00:00:00'
        ? $u['user_registered']
        : date('Y-m-d H:i:s');

    if ($dryRun) {
        fputcsv($report, [$wpId, $email, $name, 'dry-run', pickAddress($meta) ? '1' : '0', $resetToken, 'simulação']);
        $inserted++;
        fwrite(STDOUT, "[DRY] {$email} ({$name})\n");
        continue;
    }

    try {
        $dest->beginTransaction();

        $stmt = $dest->prepare(
            "INSERT INTO users
                (name, email, password_hash, role, phone, document, company_name,
                 status, wp_legacy_id, password_reset_token, password_reset_expires_at, created_at)
             VALUES
                (:name, :email, :pwd, 'customer', :phone, :doc, :company,
                 'pending', :wp, :token, :exp, :created)"
        );
        $stmt->execute([
            'name'    => $name,
            'email'   => $email,
            'pwd'     => $passwordHash,
            'phone'   => $phone,
            'doc'     => $document,
            'company' => $company,
            'wp'      => $wpId,
            'token'   => $resetToken,
            'exp'     => $resetExpires,
            'created' => $registeredAt,
        ]);
        $newUserId = (int) $dest->lastInsertId();

        // Endereço
        $addr = pickAddress($meta);
        $addrFlag = $addr ? '1' : '0';
        if ($addr) {
            $aStmt = $dest->prepare(
                "INSERT INTO addresses (user_id, label, cep, street, number, complement,
                                        neighborhood, city, state, is_default)
                 VALUES (:uid, 'Cobrança', :cep, :s, :n, :cp, :nb, :ci, :st, 1)"
            );
            $aStmt->execute([
                'uid' => $newUserId,
                'cep' => $addr['cep'], 's' => $addr['street'], 'n' => $addr['number'],
                'cp' => $addr['complement'], 'nb' => $addr['neighborhood'],
                'ci' => $addr['city'], 'st' => $addr['state'] ?: 'SP',
            ]);
        }

        $dest->commit();
        $inserted++;
        fputcsv($report, [$wpId, $email, $name, 'inserted', $addrFlag, $resetToken, '']);
        fwrite(STDOUT, "+ {$email} ({$name})\n");

    } catch (Throwable $e) {
        if ($dest->inTransaction()) $dest->rollBack();
        $failed++;
        fputcsv($report, [$wpId, $email, $name, 'error', '0', '', $e->getMessage()]);
        fwrite(STDERR, "❌ {$email}: " . $e->getMessage() . "\n");
        Logger::error('wp_migration falha', ['email' => $email, 'err' => $e->getMessage()]);
        continue;
    }

    // Email de reset (opcional)
    if ($sendEmails) {
        try {
            $mail = new MailService();
            $sent = $mail->send(
                $email,
                'Sua conta foi migrada — defina sua nova senha',
                'wp_migrated_password_reset',
                [
                    'user'        => ['name' => $name, 'email' => $email],
                    'reset_url'   => Config::appUrl() . '/redefinir-senha?token=' . $resetToken,
                    'expires_at'  => $resetExpires,
                ]
            );
            if ($sent) $emails++;
        } catch (Throwable $e) {
            Logger::warning('wp_migration email falhou', ['email' => $email, 'err' => $e->getMessage()]);
        }
    }
}

fclose($report);

fwrite(STDOUT, "\n========== RESUMO ==========\n");
fwrite(STDOUT, ($dryRun ? '[DRY-RUN] ' : '') . "Inseridos: {$inserted}\n");
fwrite(STDOUT, "Pulados:   {$skipped}\n");
fwrite(STDOUT, "Falhas:    {$failed}\n");
if ($sendEmails) fwrite(STDOUT, "Emails:    {$emails}\n");
fwrite(STDOUT, "Relatório: {$reportPath}\n");
Logger::info('wp_migration concluído', compact('inserted', 'skipped', 'failed', 'emails', 'dryRun'));
