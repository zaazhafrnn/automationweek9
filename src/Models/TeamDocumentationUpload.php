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

    public function upsertColumn(int $teamId, int $member, string $column, ?string $fileName, ?string $originalName = ''): bool
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

    public function moveToMember(int $teamId, int $fromMember, int $toMember, string $uploadDir = ''): bool
    {
        $from = $this->findOne($teamId, $fromMember);
        if (!$from) return false;

        $existing = $this->findOne($teamId, $toMember);
        if (!$existing) {
            $stmt = $this->db->prepare("INSERT INTO team_documentation_uploads (team_id, member_number) VALUES (:team_id, :member)");
            $stmt->execute([':team_id' => $teamId, ':member' => $toMember]);
            $existing = $this->findOne($teamId, $toMember);
        }

        if ($uploadDir) {
            foreach (['student_card', 'ig_follow', 'twibbon'] as $col) {
                if (!empty($existing[$col]) && file_exists($uploadDir . $existing[$col])) {
                    unlink($uploadDir . $existing[$col]);
                }
            }
        }

        $stmt = $this->db->prepare("UPDATE team_documentation_uploads SET
            student_card = :sc, original_student_card = :osc,
            ig_follow = :ig, original_ig_follow = :oig,
            twibbon = :tw, original_twibbon = :otw
            WHERE team_id = :team_id AND member_number = :member");
        $stmt->execute([
            ':sc' => $from['student_card'],
            ':osc' => $from['original_student_card'],
            ':ig' => $from['ig_follow'],
            ':oig' => $from['original_ig_follow'],
            ':tw' => $from['twibbon'],
            ':otw' => $from['original_twibbon'],
            ':team_id' => $teamId,
            ':member' => $toMember,
        ]);

        $stmt = $this->db->prepare("UPDATE team_documentation_uploads SET
            student_card = NULL, original_student_card = NULL,
            ig_follow = NULL, original_ig_follow = NULL,
            twibbon = NULL, original_twibbon = NULL
            WHERE team_id = :team_id AND member_number = :member");
        $stmt->execute([':team_id' => $teamId, ':member' => $fromMember]);

        return true;
    }
}
