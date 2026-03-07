<?php
// PMDCRM/api/admin_users.php — Full CRUD for tenant management
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if (!is_admin()) {
    json_response(['error' => 'Acesso negado. Apenas administradores.'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    // GET - List all users with plan info
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';
        
        if ($action === 'list') {
            $stmt = $pdo->query("
                SELECT u.id, u.nome, u.email, u.role, u.status, u.plan_id, u.created_at,
                       p.name as plan_name,
                       (SELECT COUNT(*) FROM clientes WHERE user_id = u.id) as total_clients,
                       (SELECT COUNT(*) FROM leads WHERE user_id = u.id) as total_leads
                FROM usuarios u
                LEFT JOIN plans p ON u.plan_id = p.id
                ORDER BY u.id DESC
            ");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $users]);
        }
        
        if ($action === 'get' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("
                SELECT u.*, p.name as plan_name, p.max_clients, p.max_leads
                FROM usuarios u
                LEFT JOIN plans p ON u.plan_id = p.id
                WHERE u.id = ?
            ");
            $stmt->execute([(int)$_GET['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) json_response(['error' => 'Usuário não encontrado'], 404);
            unset($user['senha_hash'], $user['senha']);
            json_response(['success' => true, 'data' => $user]);
        }
    }

    // POST - Create new user
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? 'create';

        if ($action === 'create') {
            if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
                json_response(['error' => 'Nome, email e senha são obrigatórios'], 400);
            }

            // Check if email exists
            $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $check->execute([$data['email']]);
            if ($check->fetch()) {
                json_response(['error' => 'Email já cadastrado'], 400);
            }

            $hash = password_hash($data['senha'], PASSWORD_DEFAULT);
            $role = $data['role'] ?? 'gestor';
            $planId = $data['plan_id'] ?? 1;

            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role, plan_id, status) VALUES (?, ?, ?, ?, ?, 'ativo')");
            $stmt->execute([
                sanitize_input($data['nome']),
                filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                $hash,
                $role,
                (int)$planId
            ]);

            // Log
            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_created', 'Criou usuário: ' . $data['email'], $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Usuário criado com sucesso', 'id' => $pdo->lastInsertId()]);
        }

        if ($action === 'toggle_status') {
            $newStatus = $data['status'] ?? 'inativo';
            $allowed = ['ativo', 'inativo', 'suspenso'];
            if (!in_array($newStatus, $allowed)) json_response(['error' => 'Status inválido'], 400);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET status = ? WHERE id = ? AND role != 'admin'");
            $stmt->execute([$newStatus, (int)$data['id']]);

            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_status_changed', "Status do user #{$data['id']} → {$newStatus}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Status atualizado']);
        }

        if ($action === 'change_role') {
            $newRole = $data['role'] ?? 'gestor';
            $allowed = ['admin', 'gestor'];
            if (!in_array($newRole, $allowed)) json_response(['error' => 'Role inválida'], 400);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, (int)$data['id']]);

            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_role_changed', "Role do user #{$data['id']} → {$newRole}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Role atualizada']);
        }

        if ($action === 'change_plan') {
            $stmt = $pdo->prepare("UPDATE usuarios SET plan_id = ? WHERE id = ?");
            $stmt->execute([(int)$data['plan_id'], (int)$data['id']]);

            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_plan_changed', "Plano do user #{$data['id']} → plan #{$data['plan_id']}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Plano atualizado']);
        }

        if ($action === 'reset_password') {
            if (empty($data['new_password'])) json_response(['error' => 'Nova senha obrigatória'], 400);
            
            $hash = password_hash($data['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
            $stmt->execute([$hash, (int)$data['id']]);

            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_password_reset', "Resetou senha do user #{$data['id']}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Senha resetada']);
        }

        if ($action === 'update') {
            $fields = [];
            $params = [];
            
            if (!empty($data['nome'])) { $fields[] = 'nome = ?'; $params[] = sanitize_input($data['nome']); }
            if (!empty($data['email'])) { $fields[] = 'email = ?'; $params[] = filter_var($data['email'], FILTER_SANITIZE_EMAIL); }
            
            if (!empty($fields)) {
                $params[] = (int)$data['id'];
                $pdo->prepare("UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
            }

            $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
                ->execute([get_current_user_id(), 'user_updated', "Editou user #{$data['id']}", $_SERVER['REMOTE_ADDR'] ?? '']);

            json_response(['success' => true, 'message' => 'Usuário atualizado']);
        }
    }

    // DELETE
    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);
        
        if ($id === (int)get_current_user_id()) {
            json_response(['error' => 'Você não pode deletar sua própria conta'], 400);
        }

        // Check if admin
        $check = $pdo->prepare("SELECT role FROM usuarios WHERE id = ?");
        $check->execute([$id]);
        $target = $check->fetch();
        if ($target && $target['role'] === 'admin') {
            json_response(['error' => 'Não é possível deletar um administrador'], 400);
        }

        $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND role != 'admin'")->execute([$id]);

        $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)")
            ->execute([get_current_user_id(), 'user_deleted', "Deletou user #{$id}", $_SERVER['REMOTE_ADDR'] ?? '']);

        json_response(['success' => true, 'message' => 'Usuário removido']);
    }

} catch (Exception $e) {
    error_log("Admin Users API Error: " . $e->getMessage());
    json_response(['error' => 'Erro: ' . $e->getMessage()], 500);
}
