<?php

namespace App\Models;

use App\Core\Model;

class Submission extends Model
{
    public function findByTeamId(int $teamId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE team_id = :team_id LIMIT 1");
        $stmt->execute([':team_id' => $teamId]);
        return $stmt->fetch();
    }

    public function upsert(int $teamId, string $type, string $value): bool
    {
        $existing = $this->findByTeamId($teamId);
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE submissions SET type = :type, value = :value WHERE team_id = :team_id");
            return $stmt->execute([':type' => $type, ':value' => $value, ':team_id' => $teamId]);
        }
        $stmt = $this->db->prepare("INSERT INTO submissions (team_id, type, value) VALUES (:team_id, :type, :value)");
        return $stmt->execute([':team_id' => $teamId, ':type' => $type, ':value' => $value]);
    }

    public function getByDivision(string $division): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, t.name as team_name, t.division, u.email, t.leaderName
            FROM submissions s
            JOIN teams t ON s.team_id = t.id
            JOIN accounts u ON t.user_id = u.id
            WHERE t.division = :division
            ORDER BY s.updated_at DESC
        ");
        $stmt->execute([':division' => $division]);
        return $stmt->fetchAll();
    }
}
