<?php
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

echo json_encode([
  'ok'   => true,
  'time' => date('c'),
  'env'  => $_ENV['APP_ENV'] ?? null,
]);
