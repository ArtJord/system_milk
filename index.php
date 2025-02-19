<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/model/Vaca.php';
require_once __DIR__ . '/controllers/VacaController.php';
require_once __DIR__ . '/Routes.php';

// Inicializando o roteador
$router = new Routes();
$vacaController = new VacaController($pdo);

// Registrando rotas
$router->add("POST", "/vaca", [$vacaController, 'create']);
$router->add("GET", "/vacas", [$vacaController, 'findAll']);
$router->add("GET", "/vaca/{id}", [$vacaController, 'findById']);
$router->add("PUT", "/vaca/{id}", [$vacaController, 'update']);
$router->add("DELETE", "/vaca/{id}", [$vacaController, 'delete']);

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

$router->dispatch($requestedPath);
