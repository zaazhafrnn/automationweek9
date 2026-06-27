<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($teamId, $proofImage)
    {
        $stmt = $this->db->prepare("INSERT INTO payments (teamId, proofImage, status) VALUES (:teamId, :proofImage, 'pending')");
        return $stmt->execute([':teamId' => $teamId, ':proofImage' => $proofImage]);
    }

    public function findByTeamId($teamId)
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE teamId = :teamId ORDER BY submittedAt DESC LIMIT 1");
        $stmt->execute([':teamId' => $teamId]);
        return $stmt->fetch();
    }

    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT p.* FROM payments p JOIN teams t ON p.teamId = t.id WHERE t.user_id = :userId ORDER BY p.submittedAt DESC LIMIT 1");
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch();
    }

    public function updateStatus($id, $status, $note = null, $verifiedBy = null, $clearVerification = false)
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

    public function getAllPayments()
    {
        $stmt = $this->db->prepare("SELECT p.*, t.name as team_name, t.teamSchool, t.division, t.leaderName, t.leaderPhoneNumber FROM payments p JOIN teams t ON p.teamId = t.id ORDER BY p.submittedAt DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
