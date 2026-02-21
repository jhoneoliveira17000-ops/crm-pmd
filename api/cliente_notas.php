<?php
// PMDCRM/api/cliente_notas.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    // Add Note
    $clienteId = $input['cliente_id'] ?? null;
    $conteudo = sanitize_input($input['conteudo'] ?? '');
    $tipo = sanitize_input($input['tipo'] ?? 'geral');
    $userId = $_SESSION['user_id'];

    if (!$clienteId || empty($conteudo)) {
        json_response(['error' => 'Conteúdo obrigatório'], 400);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO client_notes (cliente_id, user_id, conteudo, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$clienteId, $userId, $conteudo, $tipo]);
        
        // Log only important notes or just let note be the log itself? 
        // Let's log that a note was created for timeline visibility
        log_activity($pdo, $clienteId, $userId, "Adicionou nota ($tipo)");

        json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
} elseif ($method === 'DELETE') {
    $id = $input['id'] ?? null;
    if (!$id) json_response(['error' => 'ID obrigatório'], 400);

    try {
        $pdo->prepare("DELETE FROM client_notes WHERE id = ?")->execute([$id]);
        json_response(['success' => true]);
    } catch (PDOException $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
}

function log_activity($pdo, $cid, $uid, $msg) {
    $sql = "INSERT INTO activity_logs (cliente_id, user_id, acao) VALUES (?, ?, ?)";
    $pdo->prepare($sql)->execute([$cid, $uid, $msg]);
}
?>
