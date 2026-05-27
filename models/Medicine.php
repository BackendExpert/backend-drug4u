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

    public function getMedicineByName(string $name)
    {
        $stmt = $this->conn->prepare("SELECT * FROM medicines WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function FetchAllMedicine()
    {
        $stmt = $this->conn->prepare("SELECT * FROM medicines");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function FetchAllMedicineByID(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM medicines WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function UpdateMedicine(
        $price,
        $quantity,
        $expiry_date,
        $age_restriction,
        int $id
    ) {
        $fields = [];
        $params = [];

        if ($price !== null) {
            $fields[] = "price = ?";
            $params[] = $price;
        }

        if ($quantity !== null) {
            $fields[] = "quantity = ?";
            $params[] = $quantity;
        }

        if ($expiry_date !== null) {
            $fields[] = "expiry_date = ?";
            $params[] = $expiry_date;
        }

        if ($age_restriction !== null) {
            $fields[] = "age_restriction = ?";
            $params[] = $age_restriction;
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;

        $sql = "UPDATE medicines SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($params);
    }

    public function DeleteMedicine(int $id)
    {
        $stmt = $this->conn->prepare("DELETE FROM medicines WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function MedicinePurchase(int $id, int $quantity)
    {
        $stmt = $this->conn->prepare("
        UPDATE medicines 
        SET quantity = quantity - ?
        WHERE id = ? AND quantity >= ?
    ");

        return $stmt->execute([$quantity, $id, $quantity]);
    }

    public function MyPurchase(int $userid, string $medicine, int $quantity)
    {
        $stmt = $this->conn->prepare("INSERT INTO my_purchase (user_id, medicne, quantity) VALUES(?, ?, ?)");
        return $stmt->execute([$userid, $medicine, $quantity]);
    }

    public function FetchMyPurchase(int $userid)
    {
        $stmt = $this->conn->prepare("SELECT * FROM my_purchase WHERE user_id = ?");
        $stmt->execute([$userid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
