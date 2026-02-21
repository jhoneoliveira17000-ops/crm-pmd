<?php
// PMDCRM/api/cliente_servicos.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    // Add Service
    $clienteId = $input['cliente_id'] ?? null;
    $plataforma = sanitize_input($input['plataforma'] ?? '');
    
    // Support multiple types (array) or single type (string)
    $tipos = $input['tipos'] ?? ($input['tipo_servico'] ? [$input['tipo_servico']] : []);

    if (!$clienteId || empty($plataforma) || empty($tipos)) {
        json_response(['error' => 'Dados incompletos'], 400);
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO client_services (cliente_id, plataforma, tipo_servico, status) VALUES (?, ?, ?, 'ativo')");
        
        foreach ($tipos as $tipo) {
            $tipo = sanitize_input($tipo);
            if (!empty($tipo)) {
                $stmt->execute([$clienteId, $plataforma, $tipo]);
            }
        }
        
        $pdo->commit();
        
        // Log
        $tiposStr = implode(', ', $tipos);
        log_activity($pdo, $clienteId, $_SESSION['user_id'], "Adicionou serviço: $plataforma ($tiposStr)");

        json_response(['success' => true]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        json_response(['error' => $e->getMessage()], 500);
    }
} elseif ($method === 'DELETE') {
    // Remove Service
    $id = $input['id'] ?? null;
    if (!$id) json_response(['error' => 'ID obrigatório'], 400);

    try {
        // Get client id for log
        $stmt = $pdo->prepare("SELECT cliente_id, plataforma FROM client_services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch();

        if ($service) {
            $pdo->prepare("DELETE FROM client_services WHERE id = ?")->execute([$id]);
            log_activity($pdo, $service['cliente_id'], $_SESSION['user_id'], "Removeu serviço: " . $service['plataforma']);
        }
        json_response(['success' => true]);
    } catch (PDOException $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
} else {
    json_response(['error' => 'Método inválido'], 405);
}

function log_activity($pdo, $cid, $uid, $msg) {
    $sql = "INSERT INTO activity_logs (cliente_id, user_id, acao) VALUES (?, ?, ?)";
    $pdo->prepare($sql)->execute([$cid, $uid, $msg]);
}
?>
