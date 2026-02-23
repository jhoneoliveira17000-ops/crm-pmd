<?php
// api/notes.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';

header('Content-Type: application/json');
require_login();

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'];

// Debug
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug_notes.txt');
error_reporting(E_ALL);

try {
    if ($method === 'GET') {
        $leadId = $_GET['lead_id'] ?? 0;
        if (!$leadId) {
            http_response_code(400);
            echo json_encode(['error' => 'Lead ID required']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT n.*, u.nome as usuario_nome 
            FROM lead_notes n 
            LEFT JOIN usuarios u ON n.user_id = u.id 
            JOIN leads l ON n.lead_id = l.id
            WHERE n.lead_id = ? AND ({get_tenant_condition('l')})
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$leadId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $leadId = $input['lead_id'];
        $note = trim($input['note']);

        if (!$leadId || empty($note)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO lead_notes (lead_id, note, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$leadId, $note, $userId]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'created_at' => date('Y-m-d H:i:s')]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
