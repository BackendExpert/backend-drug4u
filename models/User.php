<?php

// require_once __DIR__ . 'config/database.php';
require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }


    public function findByEmailUsername(string $email, string $username): ?array
    {
        $stmt = $this->conn->prepare("
        SELECT * 
        FROM user 
        WHERE email = ? OR username = ?
        LIMIT 1
    ");

        $stmt->execute([$email, $username]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    public function create(string $username, string $email, string $password)
    {
        $stmt = $this->conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_BCRYPT)
        ]);
    }

    public function fethcall()
    {
        $stmt = $this->conn->prepare("SELECT 
                    id,
                    username,
                    email,
                    role,
                    created_at
                FROM user;");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
