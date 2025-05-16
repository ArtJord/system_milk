<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/Routes.php';

use App\Controllers\VacaController;
use App\Controllers\LeiteController;
use App\Controllers\UsuarioController;
use App\Controllers\LucroController;
use App\Controllers\DespesaController;


$router = new Routes();
$vacaController = new VacaController($pdo);
$userCargo = 'gerente';
$leiteController = new LeiteController($pdo, $userCargo);
$usuarioController = new UsuarioController($pdo);
$despesaController = new DespesaController($pdo, $userCargo);  
$lucroController = new LucroController($pdo, $userCargo);



// Registrando rotas de vacas
$router->add("POST", "/vaca", [$vacaController, 'create']);
$router->add("GET", "/vacas", [$vacaController, 'findAll']);
$router->add("GET", "/vaca/{id}", [$vacaController, 'findById']);
$router->add("PUT", "/vaca/{id}", [$vacaController, 'update']);
$router->add("DELETE", "/vaca/{id}", [$vacaController, 'delete']);

// Registrando rotas de Leite
$router->add("POST", "/leite", [$leiteController, 'create']);
$router->add("PUT", "/editleite", [$leiteController, 'update']);
$router->add("GET", "/allleite", [$leiteController, 'getAllLeites']);
$router->add("DELETE", "/deleteleite", [$leiteController, 'delete']);
$router->add("POST", "/somaleite", [$leiteController, 'somarLeite']);

// Registrando usuario
$router->add("POST", "/usuario", [$usuarioController, 'create']);
$router->add("POST", "/login", [$usuarioController, 'login']);

// Registrando rotas de Despesas
$router->add("POST", "/despesa", [$despesaController, 'create']);
$router->add("GET", "/despesas", [$despesaController,  'findAll']);
$router->add("GET", "/despesas/{id}", [$despesaController, 'findById']);
$router->add("PUT", "/despesa/{id}", [$despesaController, 'update']);
$router->add("DELETE", "/despesa/{id}", [$despesaController, 'delete']);

// Registrando rotas de Lucros
$router->add("POST", "/lucro", [$lucroController, 'create']); 
$router->add("GET", "/lucros", [$lucroController, 'findAll']); 
$router->add("GET", "/lucro/{id}", [$lucroController, 'findById']); 
$router->add("PUT", "/lucro/{id}", [$lucroController, 'update']); 
$router->add("DELETE", "/lucro/{id}", [$lucroController, 'delete']);


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
