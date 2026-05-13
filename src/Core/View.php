<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renderiza templates PHP nativos. Suporta layout via $this->extend()
 * e seções via $this->section() / $this->yield().
 */
final class View
{
    private string $templatesPath;
    private ?string $layout = null;
    private array $sections = [];
    private array $sectionStack = [];
    /** @var array<string, string> */
    private array $sectionContents = [];

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = rtrim($templatesPath, "/\\");
    }

    /**
     * Renderiza uma view. Retorna o HTML final.
     */
    public function render(string $template, array $data = []): string
    {
        $content = $this->renderRaw($template, $data);

        // Se a view chamou $this->extend('layout'), renderiza o layout
        if ($this->layout !== null) {
            $layout = $this->layout;
            $this->layout = null;
            $this->sectionContents['content'] = $content;
            $content = $this->renderRaw($layout, $data);
        }

        return $content;
    }

    private function renderRaw(string $template, array $data): string
    {
        $file = $this->resolvePath($template);
        if (!file_exists($file)) {
            throw new RuntimeException("Template não encontrado: {$template} ({$file})");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }

    private function resolvePath(string $template): string
    {
        $template = str_replace(['..', '\\'], ['', '/'], $template);
        if (!str_ends_with($template, '.php')) {
            $template .= '.php';
        }
        return $this->templatesPath . '/' . ltrim($template, '/');
    }

    /* ---------- API usada dentro dos templates ---------- */

    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    public function section(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    public function endSection(): void
    {
        if ($this->sectionStack === []) {
            throw new RuntimeException('endSection() chamado sem section() correspondente.');
        }
        $name = array_pop($this->sectionStack);
        $this->sectionContents[$name] = (string) ob_get_clean();
    }

    public function yield(string $name, string $default = ''): string
    {
        return $this->sectionContents[$name] ?? $default;
    }

    /**
     * Inclui um partial (header, footer, product_card etc.).
     */
    public function partial(string $template, array $data = []): void
    {
        echo $this->renderRaw('partials/' . ltrim($template, '/'), $data);
    }
}
