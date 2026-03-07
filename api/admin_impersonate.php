<?php
// PMDCRM/api/admin_impersonate.php — Impersonation system
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if (!is_admin() && !isset($_SESSION['original_admin_id'])) {
    json_response(['error' => 'Acesso negado'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? 'impersonate';

        if ($action === 'impersonate' || isset($data['target_user_id'])) {
            $targetId = (int)$data['target_user_id'];
            
            // Fetch target user
            $stmt = $pdo->prepare("SELECT id, nome, email, role, foto_perfil FROM usuarios WHERE id = ?");
            $stmt->execute([$targetId]);
            $target = $stmt->fetch();
            
            if (!$target) json_response(['error' => 'Usuário não encontrado'], 404);

            // Save original admin session
            $_SESSION['original_admin_id'] = $_SESSION['user_id'];
            $_SESSION['original_admin_nome'] = $_SESSION['user_nome'];
            $_SESSION['original_admin_role'] = $_SESSION['user_role'];

            // Switch to target user
            $_SESSION['user_id'] = $target['id'];
            $_SESSION['user_nome'] = $target['nome'];
            $_SESSION['user_role'] = $target['role'];
            $_SESSION['user_foto'] = $target['foto_perfil'] ?: '';
            $_SESSION['is_impersonating'] = true;

            // Log
            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([$_SESSION['original_admin_id'], 'impersonate_start', "Impersonou user #{$targetId} ({$target['nome']})", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Impersonação iniciada']);
        }

        if ($action === 'exit') {
            if (!isset($_SESSION['original_admin_id'])) {
                json_response(['error' => 'Não está impersonando'], 400);
            }

            $impersonatedId = $_SESSION['user_id'];

            // Restore original admin session
            $_SESSION['user_id'] = $_SESSION['original_admin_id'];
            $_SESSION['user_nome'] = $_SESSION['original_admin_nome'];
            $_SESSION['user_role'] = $_SESSION['original_admin_role'];
            
            // Reload admin photo
            $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $_SESSION['user_foto'] = $stmt->fetchColumn() ?: '';

            unset($_SESSION['original_admin_id'], $_SESSION['original_admin_nome'], $_SESSION['original_admin_role'], $_SESSION['is_impersonating']);

            // Log
            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([$_SESSION['user_id'], 'impersonate_end', "Saiu da impersonação do user #{$impersonatedId}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Impersonação encerrada']);
        }
    }
} catch (Exception $e) {
    error_log("Impersonate Error: " . $e->getMessage());
    json_response(['error' => 'Erro: ' . $e->getMessage()], 500);
}
