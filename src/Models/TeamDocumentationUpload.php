<?php

namespace App\Models;

use App\Core\Model;

class TeamDocumentationUpload extends Model
{
    public function findByTeam(int $teamId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM team_documentation_uploads WHERE team_id = :team_id");
        $stmt->execute([':team_id' => $teamId]);
        return $stmt->fetchAll();
    }

    public function findOne(int $teamId, int $member): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM team_documentation_uploads WHERE team_id = :team_id AND member_number = :member LIMIT 1");
        $stmt->execute([':team_id' => $teamId, ':member' => $member]);
        return $stmt->fetch();
    }

    public function upsertColumn(int $teamId, int $member, string $column, string $fileName, string $originalName = ''): bool
    {
        $allowed = ['student_card', 'ig_follow', 'twibbon'];
        if (!in_array($column, $allowed)) return false;

        $existing = $this->findOne($teamId, $member);
        if (!$existing) {
            $stmt = $this->db->prepare("INSERT INTO team_documentation_uploads (team_id, member_number) VALUES (:team_id, :member)");
            $stmt->execute([':team_id' => $teamId, ':member' => $member]);
        }

        $origColumn = 'original_' . $column;
        $stmt = $this->db->prepare("UPDATE team_documentation_uploads SET $column = :file, $origColumn = :original WHERE team_id = :team_id AND member_number = :member");
        return $stmt->execute([':file' => $fileName, ':original' => $originalName, ':team_id' => $teamId, ':member' => $member]);
    }
}
