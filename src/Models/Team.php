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

    public function create($userId, $teamName, $division, $ketuaName, $member1Name, $member2Name)
    {
        $stmt = $this->db->prepare("
            INSERT INTO teams (user_id, name, division, leader_name, member_1_name, member_2_name) 
            VALUES (:user_id, :name, :division, :leader_name, :member_1_name, :member_2_name)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $teamName,
            ':division' => $division,
            ':leader_name' => $ketuaName,
            ':member_1_name' => $member1Name,
            ':member_2_name' => $member2Name
        ]);
    }

    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM teams WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetch();
    }
}
