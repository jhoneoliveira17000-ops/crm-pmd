<?php
// PMDCRM/api/cliente_risco.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id'], $input['status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE clientes SET status_risco = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
        
        // Log it
        $sql = "INSERT INTO activity_logs (cliente_id, user_id, acao) VALUES (?, ?, ?)";
        $pdo->prepare($sql)->execute([$input['id'], $_SESSION['user_id'], "Alterou risco para " . $input['status']]);
        
        json_response(['success' => true]);
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
}
?>
