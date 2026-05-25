<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController;

if ($uri == '/api/register' && $method == 'POST') {
    $auth->register();
} elseif ($uri == '/api/login' && $method == 'POST') {
    $auth->login();
} elseif ($uri == '/api/profile' && $method == 'GET') {
    // $auth->profile();
} elseif ($uri == '/api/forgot-password' && $method == 'POST') {
    $auth->forgetPassword();
} elseif ($uri == '/api/reset-password' && $method == 'POST') {
    $auth->reset_password();
} else {
    echo json_encode(["message" => "Route not found"]);
}
