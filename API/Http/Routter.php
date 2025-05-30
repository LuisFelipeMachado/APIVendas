<?php

namespace Http;

use Controllers\UserController;
use Controllers\ProductController;
use Controllers\SaleController;
use Common\Auth;
use Common\Config;

require_once __DIR__ . '/../Common/config.php';
require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/ProductController.php';
require_once __DIR__ . '/../Controllers/SaleController.php';
require_once __DIR__ . '/../Common/auth.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path = explode("/", trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/"));
$path[0] = $path[0] ?? '';

$userController = new UserController();
$productController = new ProductController();
$saleController = new SaleController();
$auth = new Auth(Config::getConnection());

if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}

switch ($path[0]) {
    case 'login':
    if ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($auth->login($data['email'], $data['password']));
        }
        break;
    
    case 'users':
      if ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($userController->register($data));
        } elseif ($method === 'GET') {
            echo json_encode($userController->index());
        } elseif ($method === 'DELETE' && isset($path[1])) {
            echo json_encode($userController->delete($path[1]));
        }    

        break;

    case 'products':
    $auth->protectRoute();
        if ($method === 'GET') {
            echo json_encode($productController->index());
        } elseif ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($productController->store($data));
        } elseif ($method === 'PUT' && isset($path[1])) {
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($productController->update($path[1], $data));
        } elseif ($method === 'DELETE' && isset($path[1])) {
            echo json_encode($productController->delete('produtos',  $path[1]));
        }
        break;
        
    case 'sales':
        $auth->protectRoute();
        if ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($saleController->store($data['user_id'], $data['products']));
        } elseif ($method === 'GET' && isset($path[1])) {
            echo json_encode($saleController->show($path[1]));
        } elseif ($method === 'GET') {
            echo json_encode($saleController->index());
        }
        break;
    
    default:
        http_response_code(404);
        echo json_encode(["error" => "Rota não encontrada"]);
        break;
}