<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';


class UserController
{
    public static function FetchAllUsers()
    {
        $user = AuthMiddleware::handle();


        if ($user['role'] !== 'admin' && $user['role'] !== 'super_admin') {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            return;
        }

        $userModel = new User;

        $users = $userModel->fethcall();

        if (empty($users)) {
            echo json_encode([
                "success" => false,
                "message" => "No users found"
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "result" => $users
        ]);
    }

    public static function UpdateUserRoles()
    {
        $user = AuthMiddleware::handle();

        if ($user['role'] !== 'admin' && $user['role'] !== 'super_admin') {
            http_response_code(403);
            echo json_encode([
                "success" => false,
                "message" => "Forbidden"
            ]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['role']) || !isset($data['email'])) {
            echo json_encode([
                "success" => false,
                "message" => "All fields are required"
            ]);
            return;
        }

        $userModel = new User();

        $checkUser = $userModel->findByEmail($data['email']);

        if (!$checkUser) {
            echo json_encode([
                "success" => false,
                "message" => "User not found"
            ]);
            return;
        }

        $updateUserRole = $userModel->UpdateRole(
            $data['email'],
            $data['role']
        );

        if ($updateUserRole) {
            echo json_encode([
                "success" => true,
                "message" => "User role updated successfully"
            ]);
            return;
        }

        echo json_encode([
            "success" => false,
            "message" => "Failed to update user role"
        ]);
    }
}
