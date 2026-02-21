<?php
// PMDCRM/api/fix_foto_perfil.php
require_once __DIR__ . '/../src/db.php';

echo "Iniciando correção de schema para 'foto_perfil'...\n";

try {
    // 1. Alterar foto_perfil para TEXT
    echo "Alterando tabela clientes...\n";
    $pdo->exec("ALTER TABLE clientes MODIFY COLUMN foto_perfil TEXT NULL");
    echo " - Coluna foto_perfil alterada para TEXT.\n";

    echo "Correção concluída com sucesso!\n";

} catch (PDOException $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
?>
