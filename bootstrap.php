<?php

declare(strict_types=1);

$origin = getenv('CORS_ALLOW_ORIGIN') ?: '*'; 
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

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


$allowOrigin  = $_ENV['CORS_ALLOW_ORIGIN']  ?? 'http://localhost:5173';
$allowMethods = $_ENV['CORS_ALLOW_METHODS'] ?? 'GET, POST, PUT, DELETE, OPTIONS';
$allowHeaders = $_ENV['CORS_ALLOW_HEADERS'] ?? 'Content-Type, Authorization';

if (php_sapi_name() !== 'cli' && (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS')) {
    http_response_code(204);
    exit;
}
