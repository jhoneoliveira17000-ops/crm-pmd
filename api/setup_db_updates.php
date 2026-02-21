<?php
// PMDCRM/api/setup_db_updates.php
require_once __DIR__ . '/../src/db.php';

echo "Iniciando verificação e atualização do banco de dados...\n";

try {
    // 1. Add lead_id to clientes if not exists
    echo "Verificando coluna lead_id em clientes...\n";
    try {
        $pdo->query("SELECT lead_id FROM clientes LIMIT 1");
        echo " - Coluna lead_id já existe.\n";
    } catch (Exception $e) {
        echo " - Coluna lead_id ausente. Adicionando...\n";
        $pdo->exec("ALTER TABLE clientes ADD COLUMN lead_id INT NULL");
        echo " - Coluna lead_id adicionada.\n";
    }

    // 2. Create client_services if not exists
    echo "Verificando tabela client_services...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS client_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        nome_servico VARCHAR(255) NOT NULL,
        descricao TEXT,
        status VARCHAR(50) DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");
    echo " - Tabela client_services verificada.\n";

    // 3. Create client_notes if not exists
    echo "Verificando tabela client_notes...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS client_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        user_id INT NULL,
        note TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");
    echo " - Tabela client_notes verificada.\n";

    // 4. Create client_links if not exists
    echo "Verificando tabela client_links...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS client_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        url TEXT NOT NULL,
        tipo VARCHAR(50) DEFAULT 'outro',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");
    echo " - Tabela client_links verificada.\n";

    // 5. Create activity_logs if not exists (general logs)
    echo "Verificando tabela activity_logs...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NULL,
        lead_id INT NULL,
        user_id INT NULL,
        acao VARCHAR(255) NOT NULL,
        detalhes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo " - Tabela activity_logs verificada.\n";

    // 6. Ensure activity_logs has cliente_id if it was created before without it
    try {
        $pdo->query("SELECT cliente_id FROM activity_logs LIMIT 1");
    } catch (Exception $e) {
         echo " - Coluna cliente_id ausente em activity_logs. Adicionando...\n";
         $pdo->exec("ALTER TABLE activity_logs ADD COLUMN cliente_id INT NULL");
    }

    echo "Atualização de banco de dados concluída com sucesso!\n";

} catch (PDOException $e) {
    echo "ERRO FATAL: " . $e->getMessage() . "\n";
}
?>
