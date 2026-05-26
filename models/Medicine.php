<?php

require_once __DIR__ . '/../config/database.php';

class Medicine
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getMedicineByID(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM medicines WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function CreateMedicine(
        string $name,
        float $price,
        int $quantity,
        string $expiry_date,
        bool $age_restriction
    ) {
        $stmt = $this->conn->prepare("INSERT INTO medicines(name, price, quantity, expiry_date, age_restriction) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $name,
            $price,
            $quantity,
            $expiry_date,
            $age_restriction
        ]);
    }

    public function UpdateMedicine(
        float $price,
        int $quantity,
        string $expiry_date,
        bool $age_restriction,
        int $id
    ) {
        $stmt = $this->conn->prepare("
                        UPDATE medicines 
                        SET price = ?, quantity = ?, expiry_date = ?, age_restriction = ?
                        WHERE id = ?            
        ");

        return $stmt->execute([
            $price,
            $quantity,
            $expiry_date,
            $age_restriction,
            $id
        ]);
    }

    public function DeleteMedicine(int $id) {
        $stmt = $this->conn->prepare("DELETE FROM medicines WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
