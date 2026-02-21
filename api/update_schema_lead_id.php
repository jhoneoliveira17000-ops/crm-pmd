<?php
// api/update_schema_lead_id.php
require_once __DIR__ . '/../src/db.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM clientes LIKE 'lead_id'");
    if ($stmt->rowCount() == 0) {
        // Add column
        $pdo->exec("ALTER TABLE clientes ADD COLUMN lead_id INT NULL DEFAULT NULL");
        echo "Column 'lead_id' added successfully.<br>";
        
        // Add foreign key constraint for integrity (optional but good)
        // $pdo->exec("ALTER TABLE clientes ADD CONSTRAINT fk_cliente_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL");
        // echo "Foreign key added.<br>";
    } else {
        echo "Column 'lead_id' already exists.<br>";
    }
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage();
}
?>
