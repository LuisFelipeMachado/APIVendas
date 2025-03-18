<?php

namespace Http;

use Controllers\UserController;
use Controllers\ProductController;
use Controllers\SaleController;
use Common\Auth;

require __DIR__ . '/../vendor/autoload.php';
require_once '../Common/config.php';
require_once '../Controllers/UserController.php';
require_once '../Controllers/ProductController.php';
require_once '../Controllers/SaleController.php';
require_once '../Common/auth.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path = explode("/", trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/"));
$path[0] = $path[0] ?? '';

$userController = new UserController();
$productController = new ProductController();
$saleController = new SaleController();
$auth = new Auth(Config::getConnection());

 #Roteamento das requisições
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
            echo json_encode($productController->delete($path[1]));
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