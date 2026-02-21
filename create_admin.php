<?php
// create_admin.php
// Script para criar ou resetar o usuário administrador

require_once 'src/db.php';

header('Content-Type: text/plain');

$email = 'admin@pmdcrm.com';
$senha = 'admin123';
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Verifica se já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Atualiza senha
        $stmt = $pdo->prepare("UPDATE users SET senha_hash = ? WHERE email = ?");
        $stmt->execute([$senhaHash, $email]);
        echo "SUCESSO: Senha do usuário '$email' atualizada para '$senha'.";
    } else {
        // Cria novo
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', $email, $senhaHash, 'admin']);
        echo "SUCESSO: Usuário '$email' criado com a senha '$senha'.";
    }

} catch (PDOException $e) {
    echo "ERRO: " . $e->getMessage();
}
?>
