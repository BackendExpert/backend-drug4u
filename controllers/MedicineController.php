<?php

require_once __DIR__ . '/../models/Medicine.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class MedicineController
{
    public static function CreateMedicine()
    {
        $user = AuthMiddleware::handle();
        if (
            $user['role'] !== 'admin' &&
            $user['role'] !== 'super_admin' &&
            $user['role'] !== 'pharmacist'
        ) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data['name']) ||
            !isset($data['price']) ||
            !isset($data['quantity']) ||
            !isset($data['expiry_date']) ||
            !isset($data['age_restriction'])
        ) {
            echo json_encode([
                "success" => false,
                "message" => "All fields required"
            ]);
            return;
        }

        $medicineModel = new Medicine();

        if ($medicineModel->getMedicineByName($data['name'])) {
            echo json_encode([
                "success" => false,
                "message" => "This Medicine Already exists"
            ]);
            return;
        }

        $medicineModel->CreateMedicine(
            $data['name'],
            $data['price'],
            $data['quantity'],
            $data['expiry_date'],
            $data['age_restriction'],
        );

        echo json_encode([
            "success" => true,
            "message" => "Medicine Added Successfully"
        ]);
    }

    public static function FetchAllMedicien()
    {
        $user = AuthMiddleware::handle();
        if (
            $user['role'] !== 'admin' &&
            $user['role'] !== 'super_admin' &&
            $user['role'] !== 'pharmacist' &&
            $user['role'] !== 'customer'
        ) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            return;
        }

        $medicineModel = new Medicine();

        $medicine = $medicineModel->FetchAllMedicine();

        echo json_encode([
            "success" => true,
            "result" => $medicine
        ]);
    }


}
