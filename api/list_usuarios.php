<?php
// PMDCRM/api/list_usuarios.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_admin();

try {
    $stmt = $pdo->query("SELECT id, nome, email, role, created_at FROM users ORDER BY created_at DESC");
    json_response($stmt->fetchAll());
} catch (PDOException $e) {
    json_response(['error' => 'Erro ao listar usuários'], 500);
}
?>
