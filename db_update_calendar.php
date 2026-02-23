<?php
require_once __DIR__ . '/src/db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS `user_integrations` (
      `id` int NOT NULL AUTO_INCREMENT,
      `user_id` int NOT NULL,
      `provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
      `refresh_token` text COLLATE utf8mb4_unicode_ci,
      `expires_at` timestamp NULL DEFAULT NULL,
      `calendar_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_provider` (`user_id`,`provider`),
      CONSTRAINT `fk_user_integrations_user_id` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "SUCCESS: user_integrations table verified/created in TiDB.\n";
    
    // Add OAuth Config Keys if they don't exist
    $pdo->exec("INSERT IGNORE INTO config (key_name, value) VALUES ('google_client_id', '')");
    $pdo->exec("INSERT IGNORE INTO config (key_name, value) VALUES ('google_client_secret', '')");
    echo "SUCCESS: OAuth config keys verified.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
