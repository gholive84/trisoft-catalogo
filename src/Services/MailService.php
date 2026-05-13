<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;
use App\Core\View;
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Wrapper de envio de emails. Usa PHPMailer + SMTP do .env.
 * Se as credenciais SMTP estiverem vazias, NÃO lança erro — apenas loga e segue.
 * (Útil em dev/staging antes de configurar SMTP real.)
 */
final class MailService
{
    private View $view;

    public function __construct(?View $view = null)
    {
        $this->view = $view ?? new View(base_path('templates'));
    }

    /**
     * Envia email usando um template em templates/emails/{name}.php.
     *
     * @param string|string[] $to
     */
    public function send(string|array $to, string $subject, string $template, array $data = []): bool
    {
        $host = (string) Config::get('MAIL_HOST', '');
        if ($host === '') {
            Logger::warning('MailService: SMTP não configurado. Email não enviado.', [
                'to' => $to, 'subject' => $subject,
            ]);
            return false;
        }

        $body = $this->view->render('emails/' . $template, $data);

        $mailer = new PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host       = $host;
            $mailer->Port       = (int) Config::get('MAIL_PORT', 587);
            $mailer->SMTPAuth   = true;
            $mailer->Username   = (string) Config::get('MAIL_USERNAME', '');
            $mailer->Password   = (string) Config::get('MAIL_PASSWORD', '');
            $encryption = (string) Config::get('MAIL_ENCRYPTION', 'tls');
            $mailer->SMTPSecure = $encryption === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->CharSet    = 'UTF-8';

            $mailer->setFrom(
                (string) Config::get('MAIL_FROM_ADDRESS', 'no-reply@trisoft.com.br'),
                (string) Config::get('MAIL_FROM_NAME', 'Catálogo Trisoft')
            );

            foreach ((array) $to as $addr) {
                $mailer->addAddress($addr);
            }

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body    = $body;
            $mailer->AltBody = strip_tags($body);

            $mailer->send();
            Logger::info('Email enviado', ['to' => $to, 'subject' => $subject]);
            return true;
        } catch (MailException $e) {
            Logger::error('Falha ao enviar email', [
                'to' => $to, 'subject' => $subject, 'error' => $mailer->ErrorInfo ?: $e->getMessage(),
            ]);
            return false;
        }
    }
}
