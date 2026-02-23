<?php
// api/history.php
require_once '../src/auth.php';
require_once '../src/db.php';
require_once '../src/utils.php';

header('Content-Type: application/json');
require_login();

$leadId = $_GET['lead_id'] ?? 0;

if (!$leadId) {
    json_response(['error' => 'Lead ID required'], 400);
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            lh.*, 
            u.nome as usuario_nome,
            ks_de.nome as de_estagio,
            ks_para.nome as para_estagio
        FROM lead_history lh
        LEFT JOIN usuarios u ON lh.usuario_id = u.id
        LEFT JOIN kanban_stages ks_de ON lh.de_estagio_id = ks_de.id
        LEFT JOIN kanban_stages ks_para ON lh.para_estagio_id = ks_para.id
        JOIN leads l ON lh.lead_id = l.id
        WHERE lh.lead_id = ? AND ({get_tenant_condition('l')})
        ORDER BY lh.data_movimentacao DESC
    ");
    $stmt->execute([$leadId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($history);

} catch (Exception $e) {
    error_log("History API Error: " . $e->getMessage());
    json_response(['error' => 'Erro ao buscar histórico'], 500);
}
