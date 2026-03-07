<?php
// PMDCRM/api/update_profile.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];

try {
    // Atualiza nome e foto
    if (isset($data['nome']) || isset($data['foto_perfil'])) {
        $fields = [];
        $params = [];
        
        if (!empty($data['nome'])) {
            $fields[] = "nome = ?";
            $params[] = sanitize_input($data['nome']);
            $_SESSION['user_nome'] = sanitize_input($data['nome']); // Atualiza sessão
        }
        
        if (isset($data['foto_perfil'])) {
            $fields[] = "foto_perfil = ?";
            $params[] = $data['foto_perfil']; // Não sanitizar: base64 data URI é corrompido por htmlspecialchars
            $_SESSION['user_foto'] = $data['foto_perfil'];
        }

        if (!empty($fields)) {
            $params[] = $userId;
            $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
    }

    // Atualiza senha se fornecida
    if (!empty($data['nova_senha'])) {
        $hash = password_hash($data['nova_senha'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET senha_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $userId]);
    }

    json_response(['message' => 'Perfil atualizado com sucesso']);

} catch (PDOException $e) {
    json_response(['error' => 'Erro ao atualizar perfil'], 500);
}
?>
