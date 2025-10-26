<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}



require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';   
require_once __DIR__ . '/Routes.php';





$jwtCfg = require __DIR__ . '/config/jwt.php';


use App\Controllers\VacaController;
use App\Controllers\LeiteController;
use App\Controllers\UsuarioController;
use App\Controllers\LucroController;
use App\Controllers\DespesaController;
use App\Controllers\RelatorioController;

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userCargo = 'gerente';
$vacaController     = new VacaController($pdo);
$leiteController    = new LeiteController($pdo, $userCargo);
$usuarioController  = new UsuarioController($pdo);
$despesaController  = new DespesaController($pdo, $userCargo);
$lucroController    = new LucroController($pdo, $userCargo);
$relatorioController= new RelatorioController($pdo);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


function authOrFail(array $roles = null) {
    
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
        http_response_code(401);
        echo json_encode(['message' => 'Token ausente (Authorization: Bearer ...)']);
        exit;
    }

    $jwt = $m[1];
    $cfg = require __DIR__ . '/config/jwt.php';

    try {
        $decoded = JWT::decode($jwt, new Key($cfg['secret'], 'HS256'));
    } catch (Throwable $e) {
        http_response_code(401);
        echo json_encode(['message' => 'Token inválido ou expirado', 'detail' => $e->getMessage()]);
        exit;
    }

    $cargo = $decoded->cargo ?? 'atendente';
    if ($roles && !in_array($cargo, $roles, true)) {
        http_response_code(403);
        echo json_encode(['message' => 'Acesso negado para o cargo atual']);
        exit;
    }

    return [
        'id'    => $decoded->sub ?? null,
        'email' => $decoded->email ?? null,
        'cargo' => $cargo
    ];
}


$guard = function($handler, array $roles = null) use ($vacaController,$leiteController,$lucroController,$despesaController) {
    return function(...$args) use ($handler, $roles, $vacaController,$leiteController,$lucroController,$despesaController) {
        $user = authOrFail($roles);

      
        if (is_array($handler) && is_object($handler[0]) && method_exists($handler[0], 'setUserCargo')) {
            $handler[0]->setUserCargo($user['cargo']);
        }
        return call_user_func_array($handler, $args);
    };
};



$router = new Routes();

// Rotas utilitárias 
$router->add("GET", "/", function () {
    echo json_encode(["status" => "ok"]);
});
$router->add("GET", "/favicon.ico", function () {
    http_response_code(204);
});

// Relatórios
$router->add("GET", "/relatorio/estoque", [$relatorioController, 'getResumoEstoque']);
$router->add("GET", "/relatorio/financeiro", [$relatorioController, 'getResumoFinanceiro']);


// Vacas
$router->add("POST",  "/vaca",         $guard([$vacaController, 'create']));
$router->add("GET",   "/vacas",      [$vacaController, 'findAll']);
$router->add("GET",   "/vaca/{id}",  [$vacaController, 'findById']);
$router->add("PUT",   "/vaca/{id}",    $guard([$vacaController, 'update']));
$router->add("DELETE","/vaca/{id}",    $guard([$vacaController, 'delete'], ['gerente']));


// Leite
$router->add("POST",  "/leite",        $guard([$leiteController, 'create']));
$router->add("PUT",   "/leite/{id}",   $guard([$leiteController, 'update'])); 
$router->add("GET",    "/allleite",    [$leiteController, 'getAllLeites']);
$router->add("GET",    "/leite/{id}",  [$leiteController, 'getById']);
$router->add("DELETE","/leite/{id}",   $guard([$leiteController, 'delete'], ['gerente']));
  
$router->add("POST",   "/somaleite",   [$leiteController, 'somarLeite']);


// Usuário
//$router->add("POST", "/usuario", [$usuarioController, 'create']);

$router->add("POST", "/usuario", $guard([$usuarioController, 'create'], ['gerente','administrador']));
$router->add("POST", "/register", [$usuarioController, 'publicRegister']);

$router->add("POST", "/login", [$usuarioController, 'login']);

$router->add("GET",   "/me",             $guard([$usuarioController, 'me']));
$router->add("PATCH", "/usuario/{id}",   $guard([$usuarioController, 'updateBasic']));

$router->add("GET", "/usuarios", $guard([$usuarioController, 'getAllUsers']));


// Despesas
$router->add("POST",  "/despesa",      $guard([$despesaController, 'create']));
$router->add("GET",    "/despesas",      [$despesaController, 'getAllDespesas']);
$router->add("GET",    "/despesas/{id}", [$despesaController, 'getById']);
$router->add("PUT",   "/despesa/{id}", $guard([$despesaController, 'update']));
$router->add("DELETE","/despesa/{id}", $guard([$despesaController, 'delete'], ['gerente']));


// Lucros

$router->add('GET',    '/lucros',        [$lucroController, 'getAll']);
$router->add('GET',    '/lucro/{id}',    [$lucroController, 'getById']);
$router->add('POST',  '/lucro',        $guard([$lucroController, 'create']));
$router->add('PUT',   '/lucro/{id}',   $guard([$lucroController, 'update']));
$router->add('DELETE','/lucro/{id}',   $guard([$lucroController, 'delete'], ['gerente']));



$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';


$rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);


$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';         
$scriptDir  = rtrim(str_replace('\\', '/', dirname($scriptName)), '/'); 

$path = $rawPath;


if (strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
}

elseif ($scriptDir && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}


$path = '/' . ltrim($path, '/');


if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}


$router->dispatch($method, $path);