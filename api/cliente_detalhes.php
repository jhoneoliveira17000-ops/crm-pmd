<?php
// PMDCRM/api/cliente_detalhes.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if (!isset($_GET['id'])) {
    json_response(['error' => 'ID do cliente obrigatório'], 400);
}

$clienteId = (int)$_GET['id'];

try {
    // 1. Basic Info
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        json_response(['error' => 'Cliente não encontrado'], 404);
    }

    // 2. Services
    $stmt = $pdo->prepare("SELECT * FROM client_services WHERE cliente_id = ? ORDER BY created_at DESC");
    $stmt->execute([$clienteId]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Notes (Join with users for name if possible, or just id)
    // Assuming 'users' table exists and has 'nome'
    $stmt = $pdo->prepare("
        SELECT n.*, u.nome as autor 
        FROM client_notes n 
        LEFT JOIN users u ON n.user_id = u.id 
        WHERE n.cliente_id = ? 
        ORDER BY n.created_at DESC
    ");
    $stmt->execute([$clienteId]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Links
    $stmt = $pdo->prepare("SELECT * FROM client_links WHERE cliente_id = ? ORDER BY created_at DESC");
    $stmt->execute([$clienteId]);
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Activity Logs (Timeline)
    $stmt = $pdo->prepare("
        SELECT l.*, u.nome as usuario
        FROM activity_logs l
        LEFT JOIN users u ON l.user_id = u.id
        WHERE l.cliente_id = ?
        ORDER BY l.created_at DESC
    ");
    $stmt->execute([$clienteId]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'cliente' => $cliente,
        'services' => $services,
        'notes' => $notes,
        'links' => $links,
        'logs' => $logs
    ]);

} catch (PDOException $e) {
    json_response(['error' => 'Erro ao carregar dados: ' . $e->getMessage()], 500);
}
?>
