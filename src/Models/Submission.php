<?php

namespace App\Models;

use App\Core\Model;

class Submission extends Model
{
    public const PLC_ROUND_1_SESSION_2 = 'plc_round_1_s2';

    /** Max teams approved per round before advancing. */
    public const PLC_APPROVAL_LIMITS = [1 => 20, 2 => 10];

    public const PLC_ROUNDS = [
        1 => ['type' => 'plc_round_1', 'label' => 'Round 1'],
        2 => ['type' => 'plc_round_2', 'label' => 'Round 2'],
        3 => ['type' => 'plc_round_3', 'label' => 'Round 3 (Final)'],
    ];

    /** @return array<int, string> */
    public static function plcRoundOneSessionTypes(): array
    {
        return ['plc_round_1', self::PLC_ROUND_1_SESSION_2];
    }

    /** @return array<int, array{type: string, label: string}> */
    public static function plcSessions(int $round): array
    {
        if ($round === 1) {
            return [
                ['type' => 'plc_round_1', 'label' => 'Sesi 1'],
                ['type' => self::PLC_ROUND_1_SESSION_2, 'label' => 'Sesi 2'],
            ];
        }

        $meta = self::PLC_ROUNDS[$round];

        return [['type' => $meta['type'], 'label' => $meta['label']]];
    }

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
        $row = $this->findRowType($id);
        $types = self::plcRoundOneSessionTypes();
        $isRoundOne = $row && in_array($row['type'], $types, true);

        if ($isRoundOne) {
            // A Round 1 decision applies to both sessions of the team.
            $in = implode(',', array_fill(0, count($types), '?'));
            $stmt = $this->db->prepare("UPDATE submissions SET status = ?, updated_at = updated_at WHERE team_id = ? AND type IN ($in)");
            return $stmt->execute(array_merge([$status, (int) $row['team_id']], $types));
        }

        $stmt = $this->db->prepare("UPDATE submissions SET status = :status, updated_at = updated_at WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    private function findRowType(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT team_id, type FROM submissions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
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
        $types = array_merge(array_column(self::PLC_ROUNDS, 'type'), [self::PLC_ROUND_1_SESSION_2]);
        $in = implode(',', array_fill(0, count($types), '?'));
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE type IN ($in)");
        $stmt->execute($types);

        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[(int) $row['team_id']][$row['type']] = $row;
        }
        return $out;
    }

    /** Decision status of a round for a team: approved, rejected, submitted, or null if nothing sent. */
    public function plcRoundStatus(int $teamId, int $round): ?string
    {
        $types = array_column(self::plcSessions($round), 'type');
        $in = implode(',', array_fill(0, count($types), '?'));

        $stmt = $this->db->prepare("SELECT status FROM submissions WHERE team_id = ? AND type IN ($in)");
        $stmt->execute(array_merge([$teamId], $types));
        $statuses = array_column($stmt->fetchAll(), 'status');

        if (in_array('approved', $statuses, true)) return 'approved';
        if (in_array('rejected', $statuses, true)) return 'rejected';
        return $statuses ? 'submitted' : null;
    }

    /** Number of distinct teams already approved in a round. */
    public function plcApprovedTeamCount(int $round): int
    {
        $types = array_column(self::plcSessions($round), 'type');
        $in = implode(',', array_fill(0, count($types), '?'));

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT team_id) FROM submissions WHERE type IN ($in) AND status = 'approved'");
        $stmt->execute($types);

        return (int) $stmt->fetchColumn();
    }
}
