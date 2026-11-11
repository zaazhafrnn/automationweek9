<?php

namespace App\Models;

use App\Core\Model;

class Submission extends Model
{
    public const PLC_ROUNDS = [
        1 => ['type' => 'plc_round_1', 'label' => 'Round 1'],
        2 => ['type' => 'plc_round_2', 'label' => 'Round 2'],
        3 => ['type' => 'plc_round_3', 'label' => 'Round 3 (Final)'],
    ];

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

    public function upsert(int $teamId, string $type, ?string $value, string $status = 'submitted', ?string $category = null, ?string $originalName = ''): bool
    {
        $existing = $this->findByTeamAndType($teamId, $type);
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE submissions SET value = :value, status = :status, category = :category, original_name = :original_name WHERE team_id = :team_id AND type = :type");
            return $stmt->execute([':value' => $value, ':status' => $status, ':category' => $category, ':original_name' => $originalName, ':team_id' => $teamId, ':type' => $type]);
        }
        $stmt = $this->db->prepare("INSERT INTO submissions (team_id, type, value, status, category, original_name) VALUES (:team_id, :type, :value, :status, :category, :original_name)");
        return $stmt->execute([':team_id' => $teamId, ':type' => $type, ':value' => $value, ':status' => $status, ':category' => $category, ':original_name' => $originalName]);
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

    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, t.name as team_name, t.division, t.teamSchool, t.leaderName, u.email
            FROM submissions s
            JOIN teams t ON s.team_id = t.id
            JOIN accounts u ON t.user_id = u.id
            WHERE s.type != 'registration'
            ORDER BY s.updated_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT s.*, t.name as team_name, t.division, t.teamSchool, t.leaderName, u.email, u.name as user_name
            FROM submissions s
            JOIN teams t ON s.team_id = t.id
            JOIN accounts u ON t.user_id = u.id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE submissions SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
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

    public function getRoundStates(): array
    {
        $rows = $this->db->query("SELECT type, is_open FROM submission_rounds")->fetchAll();
        return array_map('intval', array_column($rows, 'is_open', 'type'));
    }

    public function setRoundOpen(string $type, bool $open): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO submission_rounds (type, is_open) VALUES (:type, :is_open)
            ON DUPLICATE KEY UPDATE is_open = VALUES(is_open)
        ");
        return $stmt->execute([':type' => $type, ':is_open' => $open ? 1 : 0]);
    }

    public function updateScore(int $teamId, string $type, ?int $score): bool
    {
        $stmt = $this->db->prepare("UPDATE submissions SET score = :score WHERE team_id = :team_id AND type = :type");
        return $stmt->execute([':score' => $score, ':team_id' => $teamId, ':type' => $type]);
    }

    public function getPlcTeams(): array
    {
        $stmt = $this->db->prepare("
            SELECT t.id, t.name, t.teamSchool, t.leaderName,
                   (SELECT p.status FROM payments p WHERE p.teamId = t.id ORDER BY p.submittedAt DESC LIMIT 1) AS payment_status
            FROM teams t
            WHERE t.division = 'PLC'
            ORDER BY t.name ASC
        ");
        $stmt->execute();

        return array_values(array_filter(
            $stmt->fetchAll(),
            fn($t) => ($t['payment_status'] ?? '') === 'verified'
        ));
    }

    /** @return array<int, array<string, array>> team_id => type => row */
    public function getPlcSubmissions(): array
    {
        $types = array_column(self::PLC_ROUNDS, 'type');
        $in = implode(',', array_fill(0, count($types), '?'));
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE type IN ($in)");
        $stmt->execute($types);

        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[(int) $row['team_id']][$row['type']] = $row;
        }
        return $out;
    }

    /** Teams ranked in the top N by combined round 1 + 2 score. */
    public function plcTopTeamIds(int $limit = 20): array
    {
        $limit = max(1, $limit);
        $stmt = $this->db->query("
            SELECT s.team_id,
                   SUM(COALESCE(s.score, 0)) AS total_score,
                   MAX(s.updated_at) AS last_at
            FROM submissions s
            JOIN teams t ON t.id = s.team_id
            WHERE t.division = 'PLC'
              AND s.type IN ('plc_round_1', 'plc_round_2')
              AND EXISTS (SELECT 1 FROM payments p WHERE p.teamId = t.id AND p.status = 'verified')
            GROUP BY s.team_id
            HAVING COUNT(DISTINCT s.type) = 2
            ORDER BY total_score DESC, last_at ASC
            LIMIT $limit
        ");

        return array_map('intval', array_column($stmt->fetchAll(), 'team_id'));
    }
}
