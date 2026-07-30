<?php

namespace App\Models;

use App\Core\Model;

class Team extends Model
{
    public function create(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO teams (user_id, name, teamSchool, division, leaderName, leaderPhoneNumber, leaderGender, firstMemberName, firstMemberPhoneNumber, firstMemberGender, secondMemberName, secondMemberPhoneNumber, secondMemberGender) 
            VALUES (:user_id, :name, :teamSchool, :division, :leaderName, :leaderPhoneNumber, :leaderGender, :firstMemberName, :firstMemberPhoneNumber, :firstMemberGender, :secondMemberName, :secondMemberPhoneNumber, :secondMemberGender)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $data['name'],
            ':teamSchool' => $data['teamSchool'],
            ':division' => $data['division'],
            ':leaderName' => $data['leaderName'],
            ':leaderPhoneNumber' => $data['leaderPhoneNumber'],
            ':leaderGender' => $data['leaderGender'] ?? null,
            ':firstMemberName' => $data['firstMemberName'],
            ':firstMemberPhoneNumber' => $data['firstMemberPhoneNumber'],
            ':firstMemberGender' => $data['firstMemberGender'] ?? null,
            ':secondMemberName' => $data['secondMemberName'],
            ':secondMemberPhoneNumber' => $data['secondMemberPhoneNumber'],
            ':secondMemberGender' => $data['secondMemberGender'] ?? null,
        ]);
    }

    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM teams WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetch();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE teams SET
            name = :name,
            teamSchool = :teamSchool,
            division = :division,
            leaderName = :leaderName,
            leaderPhoneNumber = :leaderPhoneNumber,
            leaderGender = :leaderGender,
            firstMemberName = :firstMemberName,
            firstMemberPhoneNumber = :firstMemberPhoneNumber,
            firstMemberGender = :firstMemberGender,
            secondMemberName = :secondMemberName,
            secondMemberPhoneNumber = :secondMemberPhoneNumber,
            secondMemberGender = :secondMemberGender
            WHERE id = :id");

        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':teamSchool' => $data['teamSchool'],
            ':division' => $data['division'] ?? '',
            ':leaderName' => $data['leaderName'],
            ':leaderPhoneNumber' => $data['leaderPhoneNumber'],
            ':leaderGender' => $data['leaderGender'] ?? null,
            ':firstMemberName' => $data['firstMemberName'],
            ':firstMemberPhoneNumber' => $data['firstMemberPhoneNumber'],
            ':firstMemberGender' => $data['firstMemberGender'] ?? null,
            ':secondMemberName' => $data['secondMemberName'],
            ':secondMemberPhoneNumber' => $data['secondMemberPhoneNumber'],
            ':secondMemberGender' => $data['secondMemberGender'] ?? null,
        ]);
    }

    public function getAllTeams(): array
    {
        $stmt = $this->db->prepare("SELECT t.*, u.email as user_email FROM teams t JOIN accounts u ON t.user_id = u.id ORDER BY t.created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
