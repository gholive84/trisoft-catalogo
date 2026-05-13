<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validador simples baseado em regras textuais.
 *
 * Exemplo:
 *   $v = new Validator($data, [
 *     'email' => 'required|email|max:150',
 *     'password' => 'required|min:8',
 *   ]);
 *   if (!$v->passes()) { ... $v->errors() ... }
 */
final class Validator
{
    private array $data;
    /** @var array<string, array<int, string>> */
    private array $rules;
    /** @var array<string, array<int, string>> */
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = array_map(fn ($r) => is_array($r) ? $r : explode('|', $r), $rules);
    }

    public function passes(): bool
    {
        $this->errors = [];
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
                if (isset($this->errors[$field])) {
                    break; // primeira falha por campo basta
                }
            }
        }
        return $this->errors === [];
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $msgs) {
            return $msgs[0] ?? null;
        }
        return null;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

        switch ($name) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && $value === [])) {
                    $this->addError($field, 'O campo é obrigatório.');
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'E-mail inválido.');
                }
                break;

            case 'min':
                if ($value !== null && $value !== '' && mb_strlen((string) $value) < (int) $param) {
                    $this->addError($field, "Mínimo de {$param} caracteres.");
                }
                break;

            case 'max':
                if ($value !== null && $value !== '' && mb_strlen((string) $value) > (int) $param) {
                    $this->addError($field, "Máximo de {$param} caracteres.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, 'Deve ser um número.');
                }
                break;

            case 'integer':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, 'Deve ser um número inteiro.');
                }
                break;

            case 'in':
                $allowed = $param !== null ? explode(',', $param) : [];
                if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
                    $this->addError($field, 'Valor inválido.');
                }
                break;

            case 'same':
                $other = $this->data[$param] ?? null;
                if ($value !== $other) {
                    $this->addError($field, 'Os campos não conferem.');
                }
                break;

            case 'confirmed':
                $other = $this->data[$field . '_confirmation'] ?? null;
                if ($value !== $other) {
                    $this->addError($field, 'A confirmação não confere.');
                }
                break;

            case 'cpf_cnpj':
                if ($value !== null && $value !== '') {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (!in_array(strlen($digits), [11, 14], true)) {
                        $this->addError($field, 'CPF ou CNPJ inválido.');
                    }
                }
                break;

            case 'cep':
                if ($value !== null && $value !== '') {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (strlen($digits) !== 8) {
                        $this->addError($field, 'CEP inválido.');
                    }
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}
