<?php
require_once 'src/db.php';

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($tables);

    if (in_array('users', $tables)) {
        echo "\nColumns in users:\n";
        $stmt = $pdo->query("DESCRIBE users");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if (in_array('usuarios', $tables)) {
        echo "\nColumns in usuarios:\n";
        $stmt = $pdo->query("DESCRIBE usuarios");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
