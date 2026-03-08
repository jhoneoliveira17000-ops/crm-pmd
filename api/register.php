<?php
// PMDCRM/api/register.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['nome'], $data['email'], $data['senha'])) {
    json_response(['error' => 'Dados incompletos'], 400);
}

$nome = sanitize_input($data['nome']);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$senha = $data['senha'];
$role = isset($data['role']) && $data['role'] === 'admin' ? 'admin' : 'gestor'; // Simples regra para demo

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'Email inválido'], 400);
}

// Verifica se email já existe
// Verifica se email já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_response(['error' => 'Email já cadastrado'], 409);
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash, $role]);
    $userId = $pdo->lastInsertId();

    // Configuração inicial do Kanban para o novo usuário
    $stmtSeed = $pdo->prepare("INSERT INTO kanban_stages (nome, cor, ordem, user_id) VALUES 
        ('Novo Lead', 'gray', 1, ?),
        ('Em Negociação', 'blue', 2, ?),
        ('Aguardando Visita', 'yellow', 3, ?),
        ('Fechado', 'green', 4, ?)");
    $stmtSeed->execute([$userId, $userId, $userId, $userId]);
    // Auto-login: set session
    require_once __DIR__ . '/../src/auth.php';
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_nome'] = $nome;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_foto'] = '';

    json_response([
        'message' => 'Cadastro realizado com sucesso',
        'user' => [
            'id' => $userId,
            'nome' => $nome,
            'email' => $email,
            'role' => $role
        ]
    ], 201);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        json_response(['error' => 'Email já cadastrado'], 409);
    }
    json_response(['error' => 'Erro interno ao registrar.'], 500);
}
?>
