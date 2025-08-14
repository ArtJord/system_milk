<?php
// CORS e JSON (em produção restrinja a origem)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Pré-flight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';   // aqui você cria $pdo (PDO pgsql)
require_once __DIR__ . '/Routes.php';

use App\Controllers\VacaController;
use App\Controllers\LeiteController;
use App\Controllers\UsuarioController;
use App\Controllers\LucroController;
use App\Controllers\DespesaController;
use App\Controllers\RelatorioController;

// Garanta exceções no PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Controllers
$userCargo = 'gerente';
$vacaController     = new VacaController($pdo);
$leiteController    = new LeiteController($pdo, $userCargo);
$usuarioController  = new UsuarioController($pdo);
$despesaController  = new DespesaController($pdo, $userCargo);
$lucroController    = new LucroController($pdo, $userCargo);
$relatorioController= new RelatorioController($pdo);

// Router
$router = new Routes();

// Rotas utilitárias (evita 404 no navegador)
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
$router->add("POST", "/vaca",       [$vacaController, 'create']);
$router->add("GET",  "/vacas",      [$vacaController, 'findAll']);
$router->add("GET",  "/vaca/{id}",  [$vacaController, 'findById']);
$router->add("PUT",  "/vaca/{id}",  [$vacaController, 'update']);
$router->add("DELETE","}", [$vacaController, 'delete']);

// Leite
$router->add("POST",   "/leite",       [$leiteController, 'create']);
$router->add("PUT",    "/editleit/vaca/{ide",   [$leiteController, 'update']);
$router->add("GET",    "/allleite",    [$leiteController, 'getAllLeites']);
$router->add("GET",    "/leite/{id}",  [$leiteController, 'getById']);
$router->add("DELETE", "/deleteleite", [$leiteController, 'delete']);
$router->add("POST",   "/somaleite",   [$leiteController, 'somarLeite']);

// Usuário
$router->add("POST", "/usuario", [$usuarioController, 'create']);
$router->add("POST", "/login",   [$usuarioController, 'login']);

// Despesas
$router->add("POST",   "/despesa",       [$despesaController, 'create']);
$router->add("GET",    "/despesas",      [$despesaController, 'getAllDespesas']);
$router->add("GET",    "/despesas/{id}", [$despesaController, 'getById']);
$router->add("PUT",    "/despesa/{id}",  [$despesaController, 'update']);
$router->add("DELETE", "/despesa/{id}",  [$despesaController, 'delete']);

// Lucros
$router->add("POST",   "/lucro",       [$lucroController, 'create']); 
$router->add("GET",    "/lucros",      [$lucroController, 'getAllLucros']); 
$router->add("GET",    "/lucro/{id}",  [$lucroController, 'getById']); 
$router->add("PUT",    "/lucro/{id}",  [$lucroController, 'update']); 
$router->add("DELETE", "/lucro/{id}",  [$lucroController, 'delete']);

// Despacho — **sem cortar segmentos**
$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($requestedPath);
