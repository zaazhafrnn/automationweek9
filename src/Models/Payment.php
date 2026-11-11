<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{

    public function create(int $teamId, string $proofImage, string $originalName = ''): bool
    {
        $stmt = $this->db->prepare("INSERT INTO payments (teamId, proofImage, original_name, status) VALUES (:teamId, :proofImage, :originalName, 'pending')");
        return $stmt->execute([':teamId' => $teamId, ':proofImage' => $proofImage, ':originalName' => $originalName]);
    }

    public function findByTeamId(int $teamId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE teamId = :teamId ORDER BY submittedAt DESC LIMIT 1");
        $stmt->execute([':teamId' => $teamId]);
        return $stmt->fetch();
    }

    public function updateStatus(int $id, string $status, ?string $note = null, ?int $verifiedBy = null, bool $clearVerification = false): bool
    {
        if ($clearVerification) {
            $stmt = $this->db->prepare("UPDATE payments SET status = :status, verifiedAt = NULL, verifiedBy = NULL, note = NULL WHERE id = :id");
            return $stmt->execute([':id' => $id, ':status' => $status]);
        }

        $sql = "UPDATE payments SET status = :status, verifiedAt = NOW(), verifiedBy = :verifiedBy";
        $params = [':id' => $id, ':status' => $status, ':verifiedBy' => $verifiedBy];

        if ($note !== null) {
            $sql .= ", note = :note";
            $params[':note'] = $note;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function resetStatus(int $id): bool
    {
        return $this->updateStatus($id, 'pending', null, null, true);
    }

    public function getAllPayments(): array
    {
        $stmt = $this->db->prepare("SELECT p.*, t.name as team_name, t.teamSchool, t.division, t.leaderName, t.leaderPhoneNumber, u.email as user_email FROM payments p JOIN teams t ON p.teamId = t.id JOIN accounts u ON t.user_id = u.id ORDER BY p.submittedAt DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findWithTeam(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT p.*, t.name as team_name, t.division, t.leaderName, u.email as user_email, u.name as user_name FROM payments p JOIN teams t ON p.teamId = t.id JOIN accounts u ON t.user_id = u.id WHERE p.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function saveInvoice(int $id, string $filename, string $originalName): bool
    {
        $stmt = $this->db->prepare("UPDATE payments SET invoice_file = :file, invoice_original_name = :orig, invoice_uploaded_at = NOW() WHERE id = :id");
        return $stmt->execute([':file' => $filename, ':orig' => $originalName, ':id' => $id]);
    }
}
