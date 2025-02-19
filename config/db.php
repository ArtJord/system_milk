<?php
$host = 'localhost';
$port = '5432';
$dbname = 'db_leiteria';
$user = 'postgres';
$password = '123';

try {
    
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
     //echo "Sucesso no banco de dados!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
