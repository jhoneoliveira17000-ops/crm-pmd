<?php
// PMDCRM/src/db.php

// Carrega variáveis de ambiente do arquivo .env na raiz do projeto
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? 3306;
    $db   = $env['DB_NAME'] ?? 'pmdcrm';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
    $ssl_ca = $env['DB_SSL_CA'] ?? null;
} else {
    // Check system environment variables (Railway / Cloud deployment)
    $host = trim(getenv('DB_HOST') ?: 'localhost', ' "\'');
    $port = trim(getenv('DB_PORT') ?: '3306', ' "\'');
    $db   = trim(getenv('DB_NAME') ?: 'pmdcrm', ' "\'');
    $user = trim(getenv('DB_USER') ?: 'root', ' "\'');
    $pass = trim(getenv('DB_PASS') ?: '', ' "\'');
    $ssl_ca = trim(getenv('DB_SSL_CA') ?: '', ' "\'');
    
    if ($host === 'localhost' && empty($pass)) {
        die(json_encode(['error' => 'Acesso negado: Variaveis de ambiente nao encontradas no Servidor.']));
    }
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true
];

// Auto-detect correct CA Cert path if the one mapped in .env fails (Mac vs Linux/Railway differences)
if ($ssl_ca && !file_exists($ssl_ca)) {
    $commonPaths = [
        '/etc/ssl/certs/ca-certificates.crt', // Debian/Ubuntu/Alpine (Railway Nixpacks)
        '/etc/pki/tls/certs/ca-bundle.crt',   // CentOS/RedHat
        '/etc/ssl/ca-bundle.pem',             // SUSE
        '/etc/ssl/cert.pem'                   // MacOS
    ];
    foreach ($commonPaths as $path) {
        if (file_exists($path)) {
            $ssl_ca = $path;
            break;
        }
    }
}

if (!empty($ssl_ca) && file_exists($ssl_ca)) {
    $attrSSL = defined('Pdo\Mysql::ATTR_SSL_CA') ? Pdo\Mysql::ATTR_SSL_CA : PDO::MYSQL_ATTR_SSL_CA;
    $attrVerify = defined('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT') ? Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT : PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;
    
    $options[$attrSSL] = $ssl_ca;
    $options[$attrVerify] = false;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['error' => 'DB Connection Failed: ' . $e->getMessage()]);
        exit;
    }
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
