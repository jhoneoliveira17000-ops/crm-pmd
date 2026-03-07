<?php
// PMDCRM/api/admin_logs.php — Activity Logs API
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
if (!is_admin()) json_response(['error' => 'Acesso negado'], 403);

try {
    $actionFilter = $_GET['action_filter'] ?? '';
    $where = '';
    $params = [];
    
    if ($actionFilter) {
        $where = 'WHERE al.action = ?';
        $params[] = $actionFilter;
    }

    $stmt = $pdo->prepare("
        SELECT al.*, u.nome as admin_nome 
        FROM activity_logs al 
        LEFT JOIN usuarios u ON al.user_id = u.id 
        {$where}
        ORDER BY al.created_at DESC 
        LIMIT 100
    ");
    $stmt->execute($params);
    json_response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);

} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
