<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AuthController
{
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data['username']) ||
            !isset($data['email']) ||
            !isset($data['password'])
        ) {
            echo json_encode([
                "success" => false,
                "message" => "All fields required"
            ]);
            return;
        }

        $userModel = new User;

        if ($userModel->findByEmail($data['email'])) {
            echo json_encode([
                "success" => false,
                "message" => "User Already exists"
            ]);
            return;
        }

        $userModel->create(
            $data['username'],
            $data['email'],
            $data['password']
        );
        echo json_encode([
            "success" => true,
            "message" => "User Created Successfully"
        ]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data['email']) ||
            !isset($data['password'])
        ) {
            echo json_encode([
                "success" => false,
                "message" => "All fields required"
            ]);
            return;
        }

        $userModel = new User;

        $user = $userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            echo json_encode(["message" => "Invalid credentials"]);
            return;
        }

        $token = JWT::generate([
            "sub" => $user['id'],
            "email" => $user['email'],
            "username" => $user['username'],
            "role" => $user['role'],
            "exp" => time() + 3600
        ]);

        echo json_encode([
            "success" => true,
            "message" => "User Created Successfully",
            "token" => $token
        ]);
    }

    public function profile()
    {
        $user = AuthMiddleware::handle();

        echo json_encode([
            "message" => "Protected route",
            "user" => $user
        ]);
    }
}
