<?php
require_once __DIR__ . '/../src/db.php';

header('Content-Type: application/json');

try {
    // 1. Add webhook_token to usuarios
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS webhook_token VARCHAR(64) DEFAULT NULL");
    
    // Generate existing tokens for current users
    $stmt = $pdo->query("SELECT id FROM usuarios WHERE webhook_token IS NULL");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        $token = bin2hex(random_bytes(16));
        $update = $pdo->prepare("UPDATE usuarios SET webhook_token = ? WHERE id = ?");
        $update->execute([$token, $u['id']]);
    }
    
    // 2. Add user_id to business tables
    $tables = [
        'clientes', 'leads', 'kanban_stages', 'despesas', 'financeiro_recorrente',
        'client_links', 'client_notes', 'client_services', 'config', 'facebook_leads',
        'lead_history', 'lead_notes'
    ];
    
    // Get the ID of the first admin/gestor to backward-compatible assign existing records
    $adminStmt = $pdo->query("SELECT id FROM usuarios ORDER BY id ASC LIMIT 1");
    $firstUserId = $adminStmt->fetchColumn() ?: 1;

    foreach ($tables as $table) {
        // Add column if not exists
        try {
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL");
        } catch(PDOException $e) {
            // Ignore duplicate column error if already ran
        }
        
        // Update existing records to belong to the first user
        $pdo->exec("UPDATE {$table} SET user_id = {$firstUserId} WHERE user_id IS NULL");
        
        // At this point we can add the Foreign Key constraint. 
        // We drop it first to be safe if running multiple times natively.
        try {
            // Check if constraint exists before dropping (MySQL specific)
            $check = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND CONSTRAINT_NAME = 'fk_{$table}_user'");
            if ($check->rowCount() > 0) {
                $pdo->exec("ALTER TABLE {$table} DROP FOREIGN KEY fk_{$table}_user");
            }
            
            $pdo->exec("ALTER TABLE {$table} ADD CONSTRAINT fk_{$table}_user FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE");
        } catch(PDOException $e) {
             error_log("Constraint issue on {$table}: " . $e->getMessage());
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Migração Multi-Tenant concluida! Tabelas atualizadas com user_id e webhook_token.']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
