<?php
require 'src/db.php';

try {
    $pass = password_hash('admin', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO usuarios (id, nome, email, senha, cargo) VALUES (1, "Admin", "admin@pmdcrm.com", ?, "admin") ON DUPLICATE KEY UPDATE senha = VALUES(senha)');
    $stmt->execute([$pass]);
    echo "User 'Admin' (ID 1) seeded successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
