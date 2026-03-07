<?php
// PMDCRM/api/admin_plans.php — Plans API
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();
if (!is_admin()) json_response(['error' => 'Acesso negado'], 403);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
        json_response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? 'update';

        if ($action === 'update' && isset($data['id'])) {
            $fields = [];
            $params = [];
            foreach (['name', 'max_clients', 'max_leads', 'max_integrations', 'price'] as $field) {
                if (isset($data[$field])) { $fields[] = "$field = ?"; $params[] = $data[$field]; }
            }
            if (!empty($fields)) {
                $params[] = (int)$data['id'];
                $pdo->prepare("UPDATE plans SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
            }
            json_response(['success' => true, 'message' => 'Plano atualizado']);
        }

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO plans (name, max_clients, max_leads, max_integrations, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['max_clients'] ?? 10, $data['max_leads'] ?? 100, $data['max_integrations'] ?? 1, $data['price'] ?? 0]);
            json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        }
    }
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
