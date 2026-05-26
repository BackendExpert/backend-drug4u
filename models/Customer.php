<?php

require_once __DIR__ . '/../config/database.php';


class Customer
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getUserIdByField(string $value): ?int
    {
        $stmt = $this->conn->prepare("
        SELECT id 
        FROM user 
        WHERE username = ? OR email = ?
        LIMIT 1
    ");

        $stmt->execute([$value, $value]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    public function updateCustomerData(
        int $user_id,
        ?string $fullname,
        ?string $address,
        ?string $date_of_birth,
        ?int $age
    ) {

        $check = $this->conn->prepare("
        SELECT id, date_of_birth 
        FROM customer 
        WHERE user_id = ?
    ");

        $check->execute([$user_id]);
        $customer = $check->fetch(PDO::FETCH_ASSOC);

        if ($customer) {

            $stmt = $this->conn->prepare("
            UPDATE customer 
            SET 
                full_name = COALESCE(?, full_name),
                address = COALESCE(?, address),
                age = COALESCE(?, age)
            WHERE user_id = ?
        ");

            return $stmt->execute([
                $fullname,
                $address,
                $age,
                $user_id
            ]);
        } else {

            if (empty($date_of_birth)) {
                return false;
            }

            $stmt = $this->conn->prepare("
            INSERT INTO customer (
                user_id,
                full_name,
                address,
                date_of_birth,
                age
            ) VALUES (?, ?, ?, ?, ?)
        ");

            return $stmt->execute([
                $user_id,
                $fullname,
                $address,
                $date_of_birth,
                $age
            ]);
        }
    }

    public function getMyCustomerData(string $user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    public function getCustomers(int $user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    public function getAllCustomers()
    {
        $stmt = $this->conn->prepare("SELECT * FROM customer");
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    public function updateCustomerDataPharmacist(
        int $user_id,
        array $allergies,
        array $medical_conditions
    ) {

        $allergiesJson = json_encode($allergies);
        $medicalConditionsJson = json_encode($medical_conditions);

        $stmt = $this->conn->prepare("
        UPDATE customer 
        SET 
            allergies = ?, 
            medical_conditions = ?
        WHERE user_id = ?
    ");

        return $stmt->execute([
            $allergiesJson,
            $medicalConditionsJson,
            $user_id
        ]);
    }
}
