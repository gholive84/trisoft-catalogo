<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Wrapper de resposta HTTP. Usar diretamente ou via helpers (redirect(), json()).
 */
final class Response
{
    public static function redirect(string $url, int $status = 302): never
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function html(string $html, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    public static function status(int $code): void
    {
        http_response_code($code);
    }

    public static function header(string $name, string $value): void
    {
        header($name . ': ' . $value);
    }

    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');

        $titles = [
            400 => 'Requisição inválida',
            401 => 'Não autorizado',
            403 => 'Acesso negado',
            404 => 'Página não encontrada',
            419 => 'Sessão expirada',
            422 => 'Dados inválidos',
            500 => 'Erro interno do servidor',
        ];
        $title = $titles[$code] ?? 'Erro';
        $msg = $message !== '' ? $message : $title;

        echo "<!doctype html><html lang='pt-BR'><head><meta charset='utf-8'>"
           . "<title>{$code} {$title} - Catálogo Trisoft</title>"
           . "<meta name='viewport' content='width=device-width,initial-scale=1'>"
           . "<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f9fafb;color:#111}"
           . ".box{text-align:center;padding:2rem}h1{font-size:4rem;margin:0;color:#dc2626}p{color:#4b5563;margin-top:1rem}a{color:#2563eb;text-decoration:none}</style>"
           . "</head><body><div class='box'><h1>{$code}</h1><p>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')
           . "</p><p><a href='" . Config::baseUrl() . "/'>Voltar para o início</a></p></div></body></html>";
        exit;
    }
}
