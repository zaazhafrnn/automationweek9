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

    public function getAllMembers(): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM accounts WHERE role = 'member' ORDER BY created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
