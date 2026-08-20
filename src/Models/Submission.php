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

    public function findByTeamAndType(int $teamId, string $type): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE team_id = :team_id AND type = :type LIMIT 1");
        $stmt->execute([':team_id' => $teamId, ':type' => $type]);
        return $stmt->fetch();
    }

    public function upsert(int $teamId, string $type, ?string $value, string $status = 'submitted'): bool
    {
        $existing = $this->findByTeamAndType($teamId, $type);
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE submissions SET value = :value, status = :status WHERE team_id = :team_id AND type = :type");
            return $stmt->execute([':value' => $value, ':status' => $status, ':team_id' => $teamId, ':type' => $type]);
        }
        $stmt = $this->db->prepare("INSERT INTO submissions (team_id, type, value, status) VALUES (:team_id, :type, :value, :status)");
        return $stmt->execute([':team_id' => $teamId, ':type' => $type, ':value' => $value, ':status' => $status]);
    }

    public function markReviewed(int $teamId): bool
    {
        return $this->upsert($teamId, 'registration', null, 'submitted');
    }

    public function isReviewed(int $teamId): bool
    {
        $row = $this->findByTeamAndType($teamId, 'registration');
        return !empty($row);
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
