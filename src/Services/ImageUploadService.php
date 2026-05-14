<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;
use RuntimeException;

/**
 * Validação e armazenamento de uploads de imagem.
 * - Valida MIME type real (não confia em $_FILES['type'])
 * - Renomeia para hash (evita colisão e segurança)
 * - Salva em public/uploads/<subdir>/
 */
final class ImageUploadService
{
    private string $baseDir;
    private int $maxBytes;
    /** @var string[] */
    private array $allowedMime;

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? base_path('public/uploads');
        $this->maxBytes = (int) Config::get('UPLOAD_MAX_BYTES', 8388608);
        $allowed = (string) Config::get('UPLOAD_ALLOWED_MIME', 'image/jpeg,image/png,image/webp,image/gif');
        $this->allowedMime = array_map('trim', explode(',', $allowed));
    }

    /**
     * Salva um upload. Retorna o nome do arquivo (basename) salvo.
     *
     * @param array{name:string,tmp_name:string,error:int,size:int,type:string} $file
     * @throws RuntimeException
     */
    public function store(array $file, string $subdir, ?string $filenameBase = null): string
    {
        $this->validate($file);

        $targetDir = rtrim($this->baseDir, '/') . '/' . trim($subdir, '/');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        if (!is_writable($targetDir)) {
            throw new RuntimeException("Pasta de upload não gravável: {$targetDir}");
        }

        $ext = $this->detectExtension($file['tmp_name']);
        $base = $filenameBase !== null
            ? $this->slugify($filenameBase)
            : bin2hex(random_bytes(8));
        $filename = $base . '-' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;

        $dest = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Falha ao salvar o upload.');
        }
        @chmod($dest, 0644);

        Logger::info('Upload salvo', [
            'path' => $subdir . '/' . $filename,
            'size' => $file['size'],
        ]);

        return $filename;
    }

    public function delete(string $relativePath): bool
    {
        $relativePath = ltrim($relativePath, '/');
        if (str_contains($relativePath, '..')) {
            return false;
        }
        $full = rtrim($this->baseDir, '/') . '/' . $relativePath;
        if (!is_file($full)) {
            return false;
        }
        return @unlink($full);
    }

    private function validate(array $file): void
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $msg = match ($file['error'] ?? -1) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande.',
                UPLOAD_ERR_PARTIAL    => 'Upload incompleto.',
                UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária indisponível.',
                UPLOAD_ERR_CANT_WRITE => 'Não foi possível salvar.',
                default               => 'Erro no upload.',
            };
            throw new RuntimeException($msg);
        }
        if ($file['size'] > $this->maxBytes) {
            throw new RuntimeException("Arquivo excede o limite de " . round($this->maxBytes / 1048576, 1) . " MB.");
        }

        $mime = $this->detectMime($file['tmp_name']);
        if (!in_array($mime, $this->allowedMime, true)) {
            throw new RuntimeException("Tipo de arquivo não permitido ({$mime}).");
        }
    }

    private function detectMime(string $path): string
    {
        if (function_exists('finfo_open')) {
            $f = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($f, $path) ?: '';
            finfo_close($f);
            return $mime;
        }
        return (string) (mime_content_type($path) ?: 'application/octet-stream');
    }

    private function detectExtension(string $path): string
    {
        $mime = $this->detectMime($path);
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default       => 'bin',
        };
    }

    private function slugify(string $s): string
    {
        $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
        $t = preg_replace('/[^A-Za-z0-9\s\-]/', '', $t) ?? $t;
        $t = preg_replace('/[\s\-]+/', '-', $t) ?? $t;
        return strtolower(trim($t, '-')) ?: 'file';
    }
}
