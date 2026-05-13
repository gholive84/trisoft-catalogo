<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Logger;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(
        private UserRepository $users = new UserRepository()
    ) {
    }

    /**
     * Tenta autenticar com email + senha. Retorna o usuário em caso de sucesso, null caso contrário.
     */
    public function attempt(string $email, string $password): ?array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null) {
            return null;
        }
        if ($user['status'] !== 'active') {
            return null;
        }
        if (!password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        // Rehash transparente caso o algoritmo padrão tenha mudado
        if (password_needs_rehash((string) $user['password_hash'], PASSWORD_BCRYPT)) {
            $this->users->updatePassword((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        }

        Auth::login($user);
        $this->users->touchLastLogin((int) $user['id']);

        return $user;
    }

    /**
     * Cria um novo cliente (auto-cadastro). Retorna o ID.
     */
    public function register(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $id = $this->users->create([
            'name'         => trim($data['name']),
            'email'        => $data['email'],
            'password_hash' => $hash,
            'phone'        => $data['phone'] ?? null,
            'document'     => $data['document'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'role'         => 'customer',
            'status'       => 'active',
        ]);

        $user = $this->users->findById($id);
        if ($user !== null) {
            Auth::login($user);
        }

        Logger::info('Novo cadastro', ['user_id' => $id, 'email' => $data['email']]);

        return $id;
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
