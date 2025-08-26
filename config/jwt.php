<?php
// config/jwt.php - parâmetros do JWT vindos do .env
declare(strict_types=1);

return [
    'secret'     => $_ENV['JWT_SECRET']    ?? 'CHANGE-ME',
    'issuer'     => $_ENV['JWT_ISSUER']    ?? 'system_milk',
    'audience'   => $_ENV['JWT_AUDIENCE']  ?? 'system_milk',
    'expires_in' => (int) ($_ENV['JWT_EXPIRES_IN'] ?? 60*60*4), // 4 horas
];
