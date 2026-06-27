<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Team
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $teamName, $teamSchool, $division, $leaderName, $leaderPhoneNumber, $firstMemberName, $firstMemberPhoneNumber, $secondMemberName, $secondMemberPhoneNumber)
    {
        $stmt = $this->db->prepare("
            INSERT INTO teams (user_id, name, teamSchool, division, leaderName, leaderPhoneNumber, firstMemberName, firstMemberPhoneNumber, secondMemberName, secondMemberPhoneNumber) 
            VALUES (:user_id, :name, :teamSchool, :division, :leaderName, :leaderPhoneNumber, :firstMemberName, :firstMemberPhoneNumber, :secondMemberName, :secondMemberPhoneNumber)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $teamName,
            ':teamSchool' => $teamSchool,
            ':division' => $division,
            ':leaderName' => $leaderName,
            ':leaderPhoneNumber' => $leaderPhoneNumber,
            ':firstMemberName' => $firstMemberName,
            ':firstMemberPhoneNumber' => $firstMemberPhoneNumber,
            ':secondMemberName' => $secondMemberName,
            ':secondMemberPhoneNumber' => $secondMemberPhoneNumber
        ]);
    }

    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM teams WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetch();
    }

    public function getAllTeams()
    {
        $stmt = $this->db->prepare("SELECT t.*, u.email as user_email FROM teams t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
