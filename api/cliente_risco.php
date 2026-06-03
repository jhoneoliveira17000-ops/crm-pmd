<?php
// PMDCRM/api/cliente_risco.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id'], $input['status'])) {
    try {
        $tenantScope = get_tenant_condition();
        $stmt = $pdo->prepare("UPDATE clientes SET status_risco = ? WHERE id = ? AND ({$tenantScope})");
        $stmt->execute([$input['status'], $input['id']]);
        
        if ($stmt->rowCount() > 0) {
            // Log it
            $sql = "INSERT INTO activity_logs (cliente_id, user_id, acao) VALUES (?, ?, ?)";
            $pdo->prepare($sql)->execute([$input['id'], $_SESSION['user_id'], "Alterou risco para " . $input['status']]);
        }
        
        json_response(['success' => true]);
    } catch (Exception $e) {
        error_log("Erro ao atualizar status de risco: " . $e->getMessage());
        json_response(['error' => 'Erro interno ao atualizar status de risco.'], 500);
    }
}
?>
