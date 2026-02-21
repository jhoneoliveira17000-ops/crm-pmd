<?php
// api/export.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';

require_login();

try {
    // Filename
    $filename = "leads_export_" . date('Y-m-d_H-i') . ".csv";

    // Headers to force download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add BOM for Excel compatibility with UTF-8
    fputs($output, "\xEF\xBB\xBF");

    // CSV Headers
    fputcsv($output, ['ID', 'Nome', 'Email', 'Telefone', 'Valor Estimado', 'Estágio', 'Origem', 'Data Criação']);

    // Fetch Data
    $stmt = $pdo->query("
        SELECT 
            l.id, 
            l.nome, 
            l.email, 
            l.telefone, 
            l.valor_estimado, 
            ks.nome as estagio,
            l.origem,
            l.created_at
        FROM leads l
        LEFT JOIN kanban_stages ks ON l.status_id = ks.id
        ORDER BY l.created_at DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format Currency if needed, or leave raw
        fputcsv($output, $row);
    }

    fclose($output);

} catch (Exception $e) {
    http_response_code(500);
    echo "Erro ao exportar dados: " . $e->getMessage();
}
