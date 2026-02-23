<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

// MUST BE VALIDATED AS ADMIN
if (!is_admin()) {
    json_response(['error' => 'Acesso negado. Apenas administradores.'], 403);
}

try {
    // 1. Total Tenants (Active Users)
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $total_tenants = $stmt->fetchColumn() ?? 0;

    // 2. Total Leads (Global)
    $stmt = $pdo->query("SELECT COUNT(*) FROM leads");
    $total_leads = $stmt->fetchColumn() ?? 0;

    // 3. Global MRR (Active Contracts from all tenants)
    // MRR = Sum of valor_mensal of all active contracts globally
    $sqlMRR = "
        SELECT SUM(valor_mensal) as mrr 
        FROM clientes 
        WHERE status_contrato = 'ativo'
    ";
    $stmt = $pdo->query($sqlMRR);
    $global_mrr = $stmt->fetch()['mrr'] ?? 0;

    // 4. Total Clients (Global)
    $stmt = $pdo->query("SELECT COUNT(*) FROM clientes WHERE status_contrato = 'ativo'");
    $total_clients = $stmt->fetchColumn() ?? 0;

    // 5. System Costs (Total Despesas Global - current month)
    $start = date('Y-m-01');
    $end = date('Y-m-t');
    $stmt = $pdo->prepare("SELECT SUM(valor) as total_custos FROM despesas WHERE data_despesa BETWEEN ? AND ?");
    $stmt->execute([$start, $end]);
    $global_costs = $stmt->fetch()['total_custos'] ?? 0;

    // 6. Recent Tenants (Last 5 users registered, assuming id is sequential)
    $stmt = $pdo->query("SELECT id, nome, email, created_at FROM usuarios ORDER BY id DESC LIMIT 5");
    $recent_tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'data' => [
            'total_tenants' => (int)$total_tenants,
            'total_leads' => (int)$total_leads,
            'global_mrr' => (float)$global_mrr,
            'total_clients' => (int)$total_clients,
            'global_costs' => (float)$global_costs,
            'recent_tenants' => $recent_tenants
        ]
    ]);

} catch (PDOException $e) {
    json_response(['error' => 'Erro ao carregar métricas globais: ' . $e->getMessage()], 500);
}
?>
