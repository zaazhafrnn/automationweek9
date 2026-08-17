<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{

    public function create(string $name, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO accounts (name, email, password) VALUES (:name, :email, :password)");

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM accounts WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getAllMembers(): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM accounts WHERE role = 'member' ORDER BY created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function storeResetToken(int $userId, string $token): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO password_resets (account_id, token, expires_at)
             VALUES (:id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
             ON DUPLICATE KEY UPDATE token = :new_token, expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)"
        );

        return $stmt->execute([':id' => $userId, ':token' => $token, ':new_token' => $token]);
    }
    public function findResetToken(string $token): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = :token LIMIT 1");
        $stmt->execute([':token' => $token]);

        return $stmt->fetch();
    }

    public function updatePassword(int $userId, string $password): bool
    {
        $stmt = $this->db->prepare("UPDATE accounts SET password = :password WHERE id = :id");

        return $stmt->execute([
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':id' => $userId
        ]);
    }

    public function deleteResetToken(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE account_id = :id");

        return $stmt->execute([':id' => $userId]);
    }

    public function getPasswordHash(int $id): string|false
    {
        $stmt = $this->db->prepare("SELECT password FROM accounts WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $row['password'] : false;
    }

    public function updateName(int $id, string $name): bool
    {
        $stmt = $this->db->prepare("UPDATE accounts SET name = :name WHERE id = :id");

        return $stmt->execute([
            ':name' => $name,
            ':id' => $id
        ]);
    }
}
