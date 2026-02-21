<?php
// PMDCRM/api/login.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'], $data['senha'])) {
    json_response(['error' => 'Dados incompletos'], 400);
}

$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$senha = $data['senha'];

try {
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash, role, foto_perfil FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_foto'] = $user['foto_perfil'];
        
        json_response([
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $email,
                'role' => $user['role']
            ]
        ]);
    } else {
        json_response(['error' => 'Credenciais inválidas'], 401);
    }
} catch (PDOException $e) {
    json_response(['error' => 'Erro no servidor'], 500);
}
?>
