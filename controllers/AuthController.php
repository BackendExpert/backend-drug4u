<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

require_once __DIR__ . '/../utils/Mailer.php';
require_once __DIR__ . '/../utils/EmailTemplate.php';

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

        if ($userModel->findByEmailUsername($data['email'], $data['username'])) {
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
            "exp" => time() + 86400
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Login Success",
            "token" => $token
        ]);
    }

    // public function profile()
    // {
    //     $user = AuthMiddleware::handle();

    //     echo json_encode([
    //         "message" => "Protected route",
    //         "user" => $user
    //     ]);
    // }

    public function forgetPassword()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data['email'])
        ) {
            echo json_encode([
                "success" => false,
                "message" => "Email is required"
            ]);
            return;
        }

        $userModel = new User;
        $user = $userModel->findByEmail($data['email']);

        if (!$user) {
            echo json_encode([
                "success" => false,
                "message" => "User not found"
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $token_hash = password_hash($token, PASSWORD_BCRYPT);
        $expires = date("Y-m-d H:i:s", time() + 300);

        $db = (new Database())->connect();

        $stmt = $db->prepare("INSERT INTO password_resets(email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$data['email'], $token_hash, $expires]);

        $link = $_ENV['FRONTEND_URL'] . "/reset-password?token=" . $token;

        $html = EmailTemplate::layout(
            "Password Reset",
            "You requested to reset your password.",
            "Reset Password",
            $link
        );

        Mailer::send($data['email'], "Reset Password", $html);

        echo json_encode([
            "success" => true,
            "message" => "Reset link sent to email"
        ]);
    }

    public function reset_password()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['token']) || !isset($data['password'])) {
            echo json_encode(["success" => false, "message" => "Invalid request"]);
            return;
        }

        $db = (new Database())->connect();

        $stmt = $db->prepare("SELECT * FROM password_resets");
        $stmt->execute();

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $validRecord = null;

        foreach ($records as $record) {
            if (password_verify($data['token'], $record['token'])) {
                $validRecord = $record;
                break;
            }
        }

        if (!$validRecord) {
            echo json_encode(["success" => false, "message" => "Invalid token"]);
            return;
        }

        if (strtotime($validRecord['expires_at']) < time()) {
            echo json_encode(["success" => false, "message" => "Token expired"]);
            return;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $update = $db->prepare("UPDATE user SET password = ? WHERE email = ?");
        $update->execute([$hashedPassword, $validRecord['email']]);

        $delete = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $delete->execute([$validRecord['email']]);

        echo json_encode([
            "success" => true,
            "message" => "Password updated successfully"
        ]);
    }
}
