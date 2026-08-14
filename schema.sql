CREATE DATABASE IF NOT EXISTS automationweek_9 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE automationweek_9;

CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    teamSchool VARCHAR(255) NOT NULL,
    division VARCHAR(100) NOT NULL,
    leaderName VARCHAR(255) NOT NULL,
    leaderPhoneNumber VARCHAR(100) NOT NULL,
    leaderGender VARCHAR(20) DEFAULT NULL,
    firstMemberName VARCHAR(255) DEFAULT '',
    firstMemberPhoneNumber VARCHAR(100) DEFAULT '',
    firstMemberGender VARCHAR(20) DEFAULT NULL,
    secondMemberName VARCHAR(255) DEFAULT '',
    secondMemberPhoneNumber VARCHAR(100) DEFAULT '',
    secondMemberGender VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teamId INT NOT NULL,
    proofImage VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    status VARCHAR(50) DEFAULT 'pending',
    submittedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verifiedAt TIMESTAMP NULL DEFAULT NULL,
    verifiedBy INT NULL,
    note TEXT NULL,
    FOREIGN KEY (teamId) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (verifiedBy) REFERENCES accounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL UNIQUE,
    type ENUM('file', 'youtube_link', 'application') NOT NULL DEFAULT 'file',
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS password_resets;

CREATE TABLE IF NOT EXISTS password_resets (
    account_id INT PRIMARY KEY,
    token_hash CHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_documentation_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    member_number INT NOT NULL,
    upload_type VARCHAR(100) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    student_card VARCHAR(255) NULL,
    ig_follow VARCHAR(255) NULL,
    twibbon VARCHAR(255) NULL,
    original_student_card VARCHAR(255) NULL,
    original_ig_follow VARCHAR(255) NULL,
    original_twibbon VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed an admin user (Password is 'password')
INSERT INTO accounts (name, email, password, role)
VALUES ('Admin', 'admin@mail.com', '$2y$12$0SLglUc0aZWmC6Q46E8XE.Wwe43O2afPTnAeMCFwG7Apa9IlJ5YnK', 'admin')
ON DUPLICATE KEY UPDATE id=id;
