<?php

require_once __DIR__ . '/../utils/JWT.php';

class AuthMiddleware
{

    public static function handle()
    {

        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token required"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);

        $decoded = JWT::verify($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid token"]);
            exit;
        }

        return $decoded;
    }
}
