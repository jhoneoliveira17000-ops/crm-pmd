<?php
require_once 'src/db.php';

try {
    $stmt = $pdo->query("DESCRIBE client_links");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
