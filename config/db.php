<?php
declare(strict_types=1);

$host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port     = $_ENV['DB_PORT'] ?? '5432';
$dbname   = $_ENV['DB_NAME'] ?? 'db_leiteria';
$user     = $_ENV['DB_USER'] ?? 'postgres';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("pgsql:host={$host};port={$port};dbname={$dbname}", $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
        http_response_code(500);
        echo json_encode(['message' => 'DB error', 'error' => $e->getMessage()]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao conectar ao banco de dados.']);
    }
    exit;
}
