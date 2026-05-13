<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users
             WHERE password_reset_token = :t
               AND password_reset_expires_at > NOW()
               AND deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute(['t' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['email' => strtolower(trim($email))]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO users
            (name, email, password_hash, role, phone, document, company_name, status, wp_legacy_id)
            VALUES
            (:name, :email, :pwd, :role, :phone, :doc, :company, :status, :wp)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name'    => $data['name'],
            'email'   => strtolower(trim($data['email'])),
            'pwd'     => $data['password_hash'],
            'role'    => $data['role'] ?? 'customer',
            'phone'   => $data['phone'] ?? null,
            'doc'     => $data['document'] ?? null,
            'company' => $data['company_name'] ?? null,
            'status'  => $data['status'] ?? 'active',
            'wp'      => $data['wp_legacy_id'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['name', 'email', 'phone', 'document', 'company_name', 'role', 'status'];
        $fields = [];
        $params = ['id' => $id];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "{$col} = :{$col}";
                $params[$col] = $data[$col];
            }
        }
        if ($fields === []) {
            return;
        }
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->pdo->prepare($sql)->execute($params);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users
                SET password_hash = :pwd,
                    password_reset_token = NULL,
                    password_reset_expires_at = NULL
              WHERE id = :id"
        );
        $stmt->execute(['pwd' => $passwordHash, 'id' => $id]);
    }

    public function setResetToken(int $id, string $token, string $expiresAt): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users
                SET password_reset_token = :t, password_reset_expires_at = :e
              WHERE id = :id"
        );
        $stmt->execute(['t' => $token, 'e' => $expiresAt, 'id' => $id]);
    }

    public function touchLastLogin(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * @return array<int, array>
     */
    public function paginate(int $page = 1, int $perPage = 20, ?string $role = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $where = "deleted_at IS NULL";
        $params = [];
        if ($role !== null) {
            $where .= " AND role = :role";
            $params['role'] = $role;
        }
        $sql = "SELECT id, name, email, role, status, created_at, last_login_at
                  FROM users
                 WHERE {$where}
              ORDER BY created_at DESC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
