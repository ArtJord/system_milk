<?php

require_once __DIR__ . '/config/db.php';  
require_once __DIR__ . '/model/vaca.php';
require_once __DIR__ . '/controllers/vacacontroller.php';

//teste de rotas e Method Post


$requestedUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

// Verificando se a requisição é para a rota /vaca e se é um método POST
if ($requestedMethod == 'POST' && $requestedUrl == '/vaca') {
    
    $controller = new VacaController($pdo); 
    $controller->create();  
} else {
    
    http_response_code(404);
    echo json_encode(["message" => "Rota não encontrada"]);
}

$requestedPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$pathItems = explode("/", trim($requestedPath, "/"));

if (count($pathItems) >= 1) {
    $requestedPath = "/" . $pathItems[0];
    if (count($pathItems) > 1) {
        $requestedPath .= "/" . $pathItems[1]; 
    }
} else {
    $requestedPath = "/"; 
}


