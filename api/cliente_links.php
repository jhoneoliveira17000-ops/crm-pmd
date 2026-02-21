<?php
// PMDCRM/api/cliente_links.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $clienteId = $input['cliente_id'] ?? null;
    $titulo = sanitize_input($input['titulo'] ?? '');
    $url = sanitize_input($input['url'] ?? '');
    
    if (!$clienteId || empty($titulo) || empty($url)) {
        json_response(['error' => 'Dados incompletos'], 400);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO client_links (cliente_id, titulo, url) VALUES (?, ?, ?)");
        $stmt->execute([$clienteId, $titulo, $url]);
        
        log_activity($pdo, $clienteId, $_SESSION['user_id'], "Adicionou link: $titulo");

        json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
} elseif ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $clienteId = $input['cliente_id'] ?? null;
        $titulo = sanitize_input($input['titulo'] ?? '');
        $url = sanitize_input($input['url'] ?? '');
        $isPinned = !empty($input['is_pinned']) ? 1 : 0;
        
        if (!$clienteId || empty($titulo) || empty($url)) {
            json_response(['error' => 'Dados incompletos'], 400);
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO client_links (cliente_id, titulo, url, is_pinned) VALUES (?, ?, ?, ?)");
            $stmt->execute([$clienteId, $titulo, $url, $isPinned]);
            
            log_activity($pdo, $clienteId, $_SESSION['user_id'], "Adicionou link: $titulo");

            json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            json_response(['error' => $e->getMessage()], 500);
        }
    } elseif ($action === 'toggle_pin') {
        $id = $input['id'] ?? null;
        $clienteId = $input['cliente_id'] ?? null; // For logging
        
        if (!$id) json_response(['error' => 'ID obrigatório'], 400);

        try {
            // Toggle
            $stmt = $pdo->prepare("UPDATE client_links SET is_pinned = NOT is_pinned WHERE id = ?");
            $stmt->execute([$id]);
            
            log_activity($pdo, $clienteId, $_SESSION['user_id'], "Alterou destaque do link #$id");
            json_response(['success' => true]);
        } catch (PDOException $e) {
            json_response(['error' => $e->getMessage()], 500);
        }
    }

} elseif ($method === 'DELETE') {
    $id = $input['id'] ?? null;
    if (!$id) json_response(['error' => 'ID obrigatório'], 400);

    try {
        $pdo->prepare("DELETE FROM client_links WHERE id = ?")->execute([$id]);
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
