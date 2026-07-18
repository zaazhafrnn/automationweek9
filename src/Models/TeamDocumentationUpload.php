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

    public function findOne(int $teamId, int $member, string $type): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM team_documentation_uploads WHERE team_id = :team_id AND member_number = :member AND upload_type = :type LIMIT 1");
        $stmt->execute([':team_id' => $teamId, ':member' => $member, ':type' => $type]);
        return $stmt->fetch();
    }

    public function upsert(int $teamId, int $member, string $type, string $fileName): bool
    {
        $existing = $this->findOne($teamId, $member, $type);
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE team_documentation_uploads SET file_name = :file WHERE id = :id");
            return $stmt->execute([':file' => $fileName, ':id' => $existing['id']]);
        }
        $stmt = $this->db->prepare("INSERT INTO team_documentation_uploads (team_id, member_number, upload_type, file_name) VALUES (:team_id, :member, :type, :file)");
        return $stmt->execute([':team_id' => $teamId, ':member' => $member, ':type' => $type, ':file' => $fileName]);
    }

    public function delete(int $teamId, int $member, string $type): bool
    {
        $stmt = $this->db->prepare("DELETE FROM team_documentation_uploads WHERE team_id = :team_id AND member_number = :member AND upload_type = :type");
        return $stmt->execute([':team_id' => $teamId, ':member' => $member, ':type' => $type]);
    }
}
