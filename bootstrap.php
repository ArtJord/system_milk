<?php
// bootstrap.php
declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

// Carrega .env
if (class_exists(Dotenv::class)) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

date_default_timezone_set($_ENV['APP_TZ'] ?? 'America/Campo_Grande');

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

// CORS (ajuste em produção)
$allowOrigin  = $_ENV['CORS_ALLOW_ORIGIN']  ?? '*';
$allowMethods = $_ENV['CORS_ALLOW_METHODS'] ?? 'GET, POST, PUT, DELETE, OPTIONS';
$allowHeaders = $_ENV['CORS_ALLOW_HEADERS'] ?? 'Content-Type, Authorization';

header("Access-Control-Allow-Origin: {$allowOrigin}");
header("Access-Control-Allow-Methods: {$allowMethods}");
header("Access-Control-Allow-Headers: {$allowHeaders}");
header("Content-Type: application/json; charset=utf-8");

// Evita aviso no CLI
if (php_sapi_name() !== 'cli' && (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS')) {
    http_response_code(204);
    exit;
}
