<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap.php';

try {
  $pdo = new PDO(
    "pgsql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}",
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
  echo "WEB OK";
} catch (Throwable $e) {
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
