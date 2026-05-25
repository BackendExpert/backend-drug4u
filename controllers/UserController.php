<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';


class UserController
{
    public static function FetchALlUsers()
    {
        $user = AuthMiddleware::handle();


        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
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
            "data" => $users
        ]);
    }
}
