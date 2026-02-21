<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/session.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$id = $data['id'] ?? null;
$ads_account_id = $data['ads_account_id'] ?? null;
$whatsapp_template = $data['whatsapp_template'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE clientes SET ads_account_id = ?, whatsapp_template = ? WHERE id = ?");
    $stmt->execute([$ads_account_id, $whatsapp_template, $id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error', 'details' => $e->getMessage()]);
}
