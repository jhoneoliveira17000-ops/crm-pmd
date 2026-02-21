<?php
// PMDCRM/api/debug_structure.php
header('Content-Type: text/plain');
require_once __DIR__ . '/../src/db.php';

echo "=== DIAGNÓSTICO DE ESTRUTURA DO BANCO DE DADOS ===\n\n";

try {
    // 1. Listar Tabelas
    echo "1. Tabelas Existentes:\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo " - $table\n";
    }
    echo "\n";

    // 2. Verificar estrutura de 'users'
    echo "2. Estrutura da tabela 'users':\n";
    if (in_array('users', $tables)) {
        $cols = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo "   {$col['Field']} ({$col['Type']})\n";
        }
    } else {
        echo "   [ERRO] Tabela 'users' NÃO ENCONTRADA!\n";
    }
    echo "\n";
    
    // 3. Verificar estrutura de 'clientes'
    echo "3. Estrutura da tabela 'clientes':\n";
    $cols = $pdo->query("DESCRIBE clientes")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "   {$col['Field']} ({$col['Type']})\n";
    }
    echo "\n";

     // 4. Verificar estrutura de 'client_services'
    echo "4. Estrutura da tabela 'client_services':\n";
    if (in_array('client_services', $tables)) {
        $cols = $pdo->query("DESCRIBE client_services")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo "   {$col['Field']} ({$col['Type']})\n";
        }
    } else {
        echo "   [ERRO] Tabela 'client_services' NÃO ENCONTRADA!\n";
    }
    echo "\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
?>
