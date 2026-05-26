<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../controllers/MedicineController.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController;
$user = new UserController;
$customer = new CustomerController;
$medicine = new MedicineController;

// Auth Controller

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
} 


// User Controller

elseif ($uri == '/api/users/fetch-all' && $method == 'GET') {
    $user->FetchAllUsers();
}

// Customer Data

elseif ($uri == '/api/customer/update-customer-data' && $method == 'PATCH') {
    $customer->UpdateCustomerData();
}

elseif ($uri == '/api/customer/get-my-customer-data' && $method == 'GET') {
    $customer->GetMyCustomerData();
}

elseif ($uri == '/api/customer/fetch-all-customers' && $method == 'GET') {
    $customer->GetAllCustomers();
}

elseif (preg_match('#^/api/customer/fetch-customer/(\d+)$#', $uri, $matches) && $method == 'GET') {
    $customer->GetCustomerData($matches[1]);
}

elseif (preg_match('#^/api/customer/update-pharmacist/(\d+)$#', $uri, $matches) && $method == 'PATCH') {
    $customer->UpdateCustomerDataPharmacist($matches[1]);
}

// Medicine Data

elseif ($uri == '/api/medicine/create' && $method == 'POST') {
    $medicine->CreateMedicine();
}

// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }

// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }


// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }


// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }


// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }

// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }

// elseif ($uri == '/api/reset-password' && $method == 'POST') {
//     $auth->reset_password();
// }



else {
    echo json_encode(["message" => "Route not found"]);
}
