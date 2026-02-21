<?php
// PMDCRM/src/db.php

// Carrega variáveis de ambiente do arquivo .env na raiz do projeto
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
    $host = $env['DB_HOST'] ?? 'localhost';
    $db   = $env['DB_NAME'] ?? 'pmdcrm';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
} else {
    // Fallback ou erro se o arquivo .env não existir
    // Para maior segurança, em produção o arquivo .env deve existir.
    // Aqui mantemos um fallback seguro ou lançamos erro.
    die('Arquivo .env de configuração não encontrado.');
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Para desenvolvimento:
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
