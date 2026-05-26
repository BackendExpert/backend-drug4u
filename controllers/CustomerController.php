<?php

require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CustomerController
{

    public static function UpdateCustomerData()
    {
        $user = AuthMiddleware::handle();

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            $user['role'] !== 'admin' &&
            $user['role'] !== 'super_admin' &&
            $user['role'] !== 'customer'
        ) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            return;
        }

        $customerModel = new Customer();

        $email = $user['email'];
        $currentUserId = $customerModel->getUserIdByField($email);

        if (!$currentUserId) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "No user found"
            ]);
            return;
        }

        $success = $customerModel->updateCustomerData(
            $currentUserId,
            $data['fullname'] ?? null,
            $data['address'] ?? null,
            $data['date_of_birth'] ?? null,
            isset($data['age']) && $data['age'] !== '' ? (int)$data['age'] : null
        );

        if (!$success) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Update failed"
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Your customer data has been updated successfully"
        ]);
    }

    public static function GetMyCustomerData()
    {
        $user = AuthMiddleware::handle();
        $customerModel = new Customer();

        $email = $user['email'];
        $currentUserId = $customerModel->getUserIdByField($email);

        $myCustomerData = $customerModel->getMyCustomerData($currentUserId);

        echo json_encode([
            "success" => true,
            "result" => $myCustomerData
        ]);
    }

    public static function GetAllCustomers()
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

        $customerModel = new Customer();

        $allCustomers = $customerModel->getAllCustomers();

        echo json_encode([
            "success" => true,
            "result" => $allCustomers
        ]);
    }

    public static function GetCustomerData(int $id)
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

        $customerModel = new Customer();

        $Customer = $customerModel->getCustomer($id);

        echo json_encode([
            "success" => true,
            "result" => $Customer
        ]);
    }

    public static function UpdateCustomerDataPharmacist(int $id)
    {
        $user = AuthMiddleware::handle();

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            $user['role'] !== 'admin' &&
            $user['role'] !== 'super_admin' &&
            $user['role'] !== 'pharmacist'
        ) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            return;
        }

        $allergies = [];
        $medical_conditions = [];

        if (!empty($data['allergies'])) {
            $allergies = array_values(array_filter(array_map('trim', explode(',', $data['allergies']))));
        }

        if (!empty($data['medical_conditions'])) {
            $medical_conditions = array_values(array_filter(array_map('trim', explode(',', $data['medical_conditions']))));
        }

        $customerModel = new Customer();

        $result = $customerModel->updateCustomerDataPharmacist(
            $id,
            $allergies,
            $medical_conditions
        );

        echo json_encode([
            "success" => true,
            "message" => "Customer data updated successfully",
            "result" => $result
        ]);
    }
}
