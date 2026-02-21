<?php
// PMDCRM/api/delete_despesa.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id'])) {
    json_response(['error' => 'ID da despesa não informado'], 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM despesas WHERE id = ?");
    $stmt->execute([$data['id']]);

    json_response(['message' => 'Despesa excluída com sucesso']);
} catch (PDOException $e) {
    json_response(['error' => 'Erro ao excluir despesa'], 500);
}
?>
