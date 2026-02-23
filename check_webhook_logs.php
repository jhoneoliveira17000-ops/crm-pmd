<?php
require_once __DIR__ . '/src/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM facebook_leads ORDER BY created_at DESC LIMIT 5");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($logs)) {
        echo "NO LOGS FOUND IN facebook_leads TABLE.\n";
    } else {
        foreach ($logs as $log) {
            echo "--- LOG ID: {$log['id']} | DATE: {$log['created_at']} ---\n";
            echo json_encode(json_decode($log['payload_json']), JSON_PRETTY_PRINT) . "\n\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
