<?php

namespace App\Models;

use App\Core\Model;

class Team extends Model
{
    public function create(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO teams (user_id, name, teamSchool, division, leaderName, leaderPhoneNumber, firstMemberName, firstMemberPhoneNumber, secondMemberName, secondMemberPhoneNumber) 
            VALUES (:user_id, :name, :teamSchool, :division, :leaderName, :leaderPhoneNumber, :firstMemberName, :firstMemberPhoneNumber, :secondMemberName, :secondMemberPhoneNumber)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $data['name'],
            ':teamSchool' => $data['teamSchool'],
            ':division' => $data['division'],
            ':leaderName' => $data['leaderName'],
            ':leaderPhoneNumber' => $data['leaderPhoneNumber'],
            ':firstMemberName' => $data['firstMemberName'],
            ':firstMemberPhoneNumber' => $data['firstMemberPhoneNumber'],
            ':secondMemberName' => $data['secondMemberName'],
            ':secondMemberPhoneNumber' => $data['secondMemberPhoneNumber']
        ]);
    }

    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM teams WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetch();
    }

    public function getAllTeams(): array
    {
        $stmt = $this->db->prepare("SELECT t.*, u.email as user_email FROM teams t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
