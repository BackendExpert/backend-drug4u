<?php

require_once __DIR__ . '/../models/Medicine.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../utils/CreateNotification.php';

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

        $allmedicine = $medicineModel->FetchAllMedicine();

        echo json_encode([
            "success" => true,
            "result" => $allmedicine
        ]);
    }


    public static function FetchMedicienByID(int $id)
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

        $medicineByID = $medicineModel->FetchAllMedicineByID($id);


        echo json_encode([
            "success" => true,
            "result" => $medicineByID
        ]);
    }

    public static function UpdateMedicineDate(int $id)
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

        $price = $data['price'] !== "" ? (float)$data['price'] : null;
        $quantity = $data['quantity'] !== "" ? (int)$data['quantity'] : null;
        $expiry_date = $data['expiry_date'] !== "" ? $data['expiry_date'] : null;
        $age_restriction = $data['age_restriction'] !== "" ? (int)$data['age_restriction'] : null;

        $medicineModel = new Medicine();

        $updated = $medicineModel->UpdateMedicine(
            $price,
            $quantity,
            $expiry_date,
            $age_restriction,
            $id
        );

        if ($updated) {
            echo json_encode([
                "success" => true,
                "message" => "Medicine Updated Successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Update Failed"
            ]);
        }
    }

    public static function MedicinePurchase(int $id)
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

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['quantity'])) {
            echo json_encode([
                "success" => false,
                "message" => "Quantity is Required"
            ]);
            return;
        }

        $medicineModel = new Medicine();
        $customerModel = new Customer();
        $userModel = new User();
        $notificationCreate = new CreateNotification();

        $email = $user['email'];
        $currentUserId = $customerModel->getUserIdByField($email);

        $customerData = $customerModel->getMyCustomerData($currentUserId);
        $medicine = $medicineModel->FetchAllMedicineByID($id);

        $allergies = json_decode($customerData['allergies'], true);

        if (
            is_array($allergies) &&
            in_array($medicine['name'], $allergies)
        ) {
            echo json_encode([
                "success" => false,
                "message" => "You cannot purchase this medicine because you have allergies to this medicine"
            ]);
            return;
        }

        if (!$medicine) {
            echo json_encode([
                "success" => false,
                "message" => "Medicine not found"
            ]);
            return;
        }

        if ((int)$medicine['quantity'] <= 25) {
            echo json_encode([
                "success" => false,
                "message" => "Not enough stock to allow purchase"
            ]);
            return;
        }

        if ((int)$medicine['age_restriction'] === 1) {
            if ((int)$customerData['age'] < 18) {
                echo json_encode([
                    "success" => false,
                    "message" => "You cannot purchase this medicine because you are under 18+"
                ]);
                return;
            }
        }

        $update_medicine = $medicineModel->MedicinePurchase(
            $id,
            $data['quantity']
        );

        $updatemyPurchase = $medicineModel->MyPurchase(
            $currentUserId,
            $medicine['name'],
            $data['quantity']
        );

        $newQuantity = (int)$medicine['quantity'] - (int)$data['quantity'];

        if ($newQuantity <= 25) {

            $notificationCreate->create(
                "Low Stock Medicine",
                $medicine['name'] . " stock is running low. Current quantity: " . $newQuantity
            );

            $html = EmailTemplate::Notification(
                "Low Stock Medicine",
                "The medicine <b>" . $medicine['name'] . "</b> is running low in stock. Current quantity: <b>" . $newQuantity . "</b>."
            );

            $allusers = $userModel->fethcall();

            foreach ($allusers as $adminUser) {

                if (
                    $adminUser['role'] === 'super_admin' ||
                    $adminUser['role'] === 'pharmacist'
                ) {

                    Mailer::send(
                        $adminUser['email'],
                        "Low Stock Medicine Alert",
                        $html
                    );
                }
            }
        }

        $notificationCreate->create(
            "Customer Purchase Medicine",
            $medicine['name'] . "Purchased by " . $email
        );

        echo json_encode([
            "success" => true,
            "message" => "Medicine Purchase Successfully"
        ]);
    }

    public static function FetchMyPurchase()
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
        $customerModel = new Customer();

        $email = $user['email'];
        $currentUserId = $customerModel->getUserIdByField($email);

        $getmyPurchase = $medicineModel->FetchMyPurchase($currentUserId);

        echo json_encode([
            "success" => true,
            "result" => $getmyPurchase
        ]);
    }

    public static function FetchAllNotifications()
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

        $notificationCreate = new CreateNotification();

        $allnotifications = $notificationCreate->fetchAll();

        echo json_encode([
            "success" => true,
            "result" => $allnotifications
        ]);
    }
}
