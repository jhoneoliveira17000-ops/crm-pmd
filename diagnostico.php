<?php
// diagnostico.php - Upload para a pasta PMDCRM (junto com index.php e src/)

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO PMDCRM ===\n\n";

// 1. Verifica versão do PHP
echo "1. Versão do PHP: " . phpversion() . "\n";

// 2. Verifica arquivo .env
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "2. Arquivo .env: ENCONTRADO\n";
    $env = parse_ini_file($envPath);
    if ($env) {
        echo "   - Leitura do .env: SUCESSO\n";
        echo "   - DB_HOST: " . ($env['DB_HOST'] ?? 'Não definido') . "\n";
        echo "   - DB_NAME: " . ($env['DB_NAME'] ?? 'Não definido') . "\n";
        echo "   - DB_USER: " . ($env['DB_USER'] ?? 'Não definido') . "\n";
        // Não mostrar senha
    } else {
        echo "   - Leitura do .env: FALHA (Arquivo inválido ou permissão negada)\n";
    }
} else {
    echo "2. Arquivo .env: NÃO ENCONTRADO\n";
    echo "   ERRO CRÍTICO: Você precisa enviar o arquivo .env para esta pasta!\n";
    exit;
}

// 3. Teste de Conexão com Banco de Dados
echo "\n3. Teste de Conexão MySQL:\n";
try {
    $host = $env['DB_HOST'] ?? 'localhost';
    $db   = $env['DB_NAME'] ?? 'pmdcrm';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    echo "   - Conexão: SUCESSO!\n";
} catch (PDOException $e) {
    echo "   - Conexão: FALHA\n";
    echo "   - Erro: " . $e->getMessage() . "\n";
    echo "\n   DICAS:\n";
    echo "   a) Verifique se o banco de dados '$db' foi criado no painel da hospedagem.\n";
    echo "   b) Verifique se o usuário '$user' tem permissão no banco.\n";
    echo "   c) Verifique se a senha está correta no arquivo .env.\n";
    echo "   d) Em hospedagens gratuitas, o 'DB_HOST' raramente é 'localhost'. Verifique o valor correto no painel (ex: sql123.unaux.com).\n";
}
?>
