<?php

require_once __DIR__ . '/../config/database.php';

class CreateNotification
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create(
        string $type_notification,
        string $notification
    ) {

        $stmt = $this->conn->prepare("
            INSERT INTO notifications (
                type_notification,
                notification
            ) VALUES (?, ?)
        ");

        return $stmt->execute([
            $type_notification,
            $notification
        ]);
    }

    public function fetchAll()
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM notifications
            ORDER BY id DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM notifications
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}
