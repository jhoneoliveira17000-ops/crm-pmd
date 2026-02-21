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
    json_response(['message' => 'Usuário registrado com sucesso'], 201);
} catch (PDOException $e) {
    json_response(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
}
?>
