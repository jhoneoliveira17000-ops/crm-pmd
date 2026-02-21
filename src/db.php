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
    // Fallback ou erro se o arquivo .env não existir
    // Para maior segurança, em produção o arquivo .env deve existir.
    // Aqui mantemos um fallback seguro ou lançamos erro.
    die('Arquivo .env de configuração não encontrado.');
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
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
    // Para desenvolvimento:
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
