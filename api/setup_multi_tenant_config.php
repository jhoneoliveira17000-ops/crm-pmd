<?php
require_once __DIR__ . '/../src/db.php';

try {
    // Drop the old unique key if exists
    $pdo->exec("ALTER TABLE config DROP INDEX key_name");
    
    // Add the new unique key
    $pdo->exec("ALTER TABLE config ADD UNIQUE KEY unique_key_user (key_name, user_id)");

    echo "Config unique key updated!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
