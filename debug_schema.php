<?php
require_once 'src/db.php';
$stmt = $pdo->query("SELECT * FROM clientes LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r(array_keys($row));
?>
