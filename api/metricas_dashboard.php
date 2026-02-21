<?php
// PMDCRM/api/metricas_dashboard.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

// Filtros de período opcional
$inicio = $_GET['inicio'] ?? date('Y-m-01'); // Início do mês atual
$fim = $_GET['fim'] ?? date('Y-m-t');       // Fim do mês atual

try {
    // 1. MRR Total 
    // Regra: Contrato ativo se:
    // - status_contrato = 'ativo' (ou ignorar status se for previsão futura baseada em datas?)
    // - Para previsão de fluxo, vamos considerar:
    // data_inicio_contrato <= FimDoMesSelecionado
    // E (data_fim_contrato >= InicioDoMesSelecionado OU data_fim_contrato IS NULL)
    // E status_contrato != 'cancelado' (ou cancelado DEPOIS do inicio do mes)
    
    // Para simplificar e ser robusto:
    // MRR = Soma de contratos onde:
    // (data_inicio_contrato <= ?) AND 
    // (data_fim_contrato IS NULL OR data_fim_contrato >= ?) AND
    // (status_contrato = 'ativo' OR (status_contrato = 'cancelado' AND data_cancelamento > ?))
    
    $sqlMRR = "
        SELECT SUM(valor_mensal) as mrr 
        FROM clientes 
        WHERE 
            data_inicio_contrato <= ? 
            AND (data_fim_contrato IS NULL OR data_fim_contrato >= ?)
            AND (
                status_contrato = 'ativo' 
                OR (status_contrato = 'cancelado' AND data_cancelamento >= ?)
            )
    ";
    
    $stmt = $pdo->prepare($sqlMRR);
    $stmt->execute([$fim, $inicio, $inicio]);
    $mrr = $stmt->fetch()['mrr'] ?? 0;

    // 2. Custos Totais (todas as despesas do período)
    $stmt = $pdo->prepare("SELECT SUM(valor) as total_custos FROM despesas WHERE data_despesa BETWEEN ? AND ?");
    $stmt->execute([$inicio, $fim]);
    $total_custos = $stmt->fetch()['total_custos'] ?? 0;

    // Lucro = Faturamento - Custos
    $lucro = $mrr - $total_custos;

    // 3. Churn
    // Ativos no início do período
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as ativos_inicio 
        FROM clientes 
        WHERE 
            data_inicio_contrato < ? 
            AND (data_fim_contrato IS NULL OR data_fim_contrato >= ?)
            AND (data_cancelamento IS NULL OR data_cancelamento >= ?)
    ");
    $stmt->execute([$inicio, $inicio, $inicio]);
    $ativos_inicio = $stmt->fetch()['ativos_inicio'] ?? 0;

    // Cancelados no período
    $stmt = $pdo->prepare("SELECT COUNT(*) as cancelados FROM clientes WHERE data_cancelamento BETWEEN ? AND ?");
    $stmt->execute([$inicio, $fim]);
    $cancelados = $stmt->fetch()['cancelados'] ?? 0;

    $churn_rate = $ativos_inicio > 0 ? ($cancelados / $ativos_inicio) * 100 : 0;

    // 4. LTV
    // Ticket Médio de hoje (ou do período selecionado)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_ativos 
        FROM clientes 
        WHERE 
            data_inicio_contrato <= ? 
            AND (data_fim_contrato IS NULL OR data_fim_contrato >= ?)
            AND (status_contrato = 'ativo' OR (status_contrato = 'cancelado' AND data_cancelamento >= ?))
    ");
    $stmt->execute([$fim, $inicio, $inicio]);
    $total_ativos_periodo = $stmt->fetch()['total_ativos'] ?? 0;
    
    $ticket_medio = $total_ativos_periodo > 0 ? $mrr / $total_ativos_periodo : 0;
    // Tempo médio de retenção
    if ($churn_rate > 0) {
        $tempo_retencao = 1 / ($churn_rate / 100); 
    } else {
        $tempo_retencao = 12; 
    }
    // 5. LTV (Lifetime Value)
    $ltv = $churn_rate > 0 ? ($mrr / ($churn_rate / 100)) : 0;

    // 6. Clientes Ativos (total count)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes WHERE status_contrato = 'ativo'");
    $stmt->execute();
    $clientes_ativos = $stmt->fetch()['total'] ?? 0;

    // 7. Ticket Médio (MRR / Clientes Ativos)
    $ticket_medio = $clientes_ativos > 0 ? $mrr / $clientes_ativos : 0;

    // 8. Novos Clientes este Mês (mês atual, não o período selecionado)
    $primeiroDiaMesAtual = date('Y-m-01');
    $ultimoDiaMesAtual = date('Y-m-t');
    $stmt = $pdo->prepare("SELECT COUNT(*) as novos FROM clientes WHERE data_entrada BETWEEN ? AND ?");
    $stmt->execute([$primeiroDiaMesAtual, $ultimoDiaMesAtual]);
    $novos_mes_atual = $stmt->fetch()['novos'] ?? 0;

    // --- DADOS PARA GRÁFICOS (Últimos 6 meses) ---
    
    $history_labels = [];
    $history_mrr = [];
    $history_custos = [];
    $history_lucro = [];
    $history_novos = [];
    $history_cancelados = [];

    for ($i = 5; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        $label = date('M', strtotime($month_start)); // Jan, Fev...
        
        $history_labels[] = $label;

        // MRR histórico
        $stmt = $pdo->prepare($sqlMRR);
        $stmt->execute([$month_end, $month_start, $month_start]);
        $m = $stmt->fetch()['mrr'] ?? 0;
        $history_mrr[] = (float)$m;

        // Custos históricos
        $stmt = $pdo->prepare("SELECT SUM(valor) as total FROM despesas WHERE data_despesa BETWEEN ? AND ?");
        $stmt->execute([$month_start, $month_end]);
        $c = $stmt->fetch()['total'] ?? 0;
        $history_custos[] = (float)$c;

        // Lucro histórico
        $history_lucro[] = (float)($m - $c);

        // Novos clientes histórico
        $stmt = $pdo->prepare("SELECT COUNT(*) as novos FROM clientes WHERE data_entrada BETWEEN ? AND ?");
        $stmt->execute([$month_start, $month_end]);
        $n = $stmt->fetch()['novos'] ?? 0;
        $history_novos[] = (int)$n;

        // Cancelados histórico
        $stmt = $pdo->prepare("SELECT COUNT(*) as canc FROM clientes WHERE data_cancelamento BETWEEN ? AND ?");
        $stmt->execute([$month_start, $month_end]);
        $cnc = $stmt->fetch()['canc'] ?? 0;
        $history_cancelados[] = (int)$cnc;
    }

    // 9. Distribuição por Método de Pagamento (Ativos)
    $stmt = $pdo->prepare("SELECT metodo_pagamento, COUNT(*) as qtd FROM clientes WHERE status_contrato = 'ativo' GROUP BY metodo_pagamento");
    $stmt->execute();
    $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 10. Top 5 Clientes (Maior MRR)
    $stmt = $pdo->prepare("SELECT nome_empresa, valor_mensal, foto_perfil FROM clientes WHERE status_contrato = 'ativo' ORDER BY valor_mensal DESC LIMIT 5");
    $stmt->execute();
    $top_clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- CRM / KANBAN METRICS ---
    
    // 11. Total Leads
    $stmt = $pdo->query("SELECT COUNT(*) FROM leads");
    $total_leads = $stmt->fetchColumn();

    // 12. Valor em Negociação (Soma de leads em aberto, ex: status != Fechado/Perdido se tivéssemos perdido)
    // Assumindo que status_id 4 é 'Fechado' (baseado no seed). Vamos somar tudo por enquanto ou filtrar por estágio.
    // Vamos somar tudo que NÃO está no estágio final (4).
    $stmt = $pdo->query("SELECT SUM(valor_estimado) FROM leads WHERE status_id != 4");
    $pipeline_value = $stmt->fetchColumn() ?? 0;

    // 13. Conversão (Leads Fechados / Total de Leads)
    $stmt = $pdo->query("SELECT COUNT(*) FROM leads WHERE status_id = 4"); // 4 = Fechado
    $leads_fechados = $stmt->fetchColumn();
    $taxa_conversao = $total_leads > 0 ? ($leads_fechados / $total_leads) * 100 : 0;

    // 14. Funnel Data (Group by Stage)
    // Getting Stage Names for clarity
    $stmt = $pdo->query("
        SELECT s.nome, COUNT(l.id) as count, SUM(l.valor_estimado) as total_valor 
        FROM kanban_stages s 
        LEFT JOIN leads l ON s.id = l.status_id 
        GROUP BY s.id, s.nome, s.ordem 
        ORDER BY s.ordem ASC
    ");
    $funnel_data = $stmt->fetchAll(PDO::FETCH_ASSOC); // [{nome: 'Novo Lead', count: 5}, ...]

    // 15. Leads by Source
    $stmt = $pdo->query("SELECT origem, COUNT(*) as count FROM leads GROUP BY origem ORDER BY count DESC");
    $leads_by_source = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- NEW METRICS (CAC, ROI, CLOSING TIME) ---

    // 16. New Clients in Period (Selected Date Range) - for CAC
    $stmt = $pdo->prepare("SELECT COUNT(*) as novos FROM clientes WHERE data_entrada BETWEEN ? AND ?");
    $stmt->execute([$inicio, $fim]);
    $novos_periodo = $stmt->fetch()['novos'] ?? 0;

    // CAC = Total Custos / Novos Clientes
    $cac = $novos_periodo > 0 ? ($total_custos / $novos_periodo) : 0;

    // ROI = (MRR - Custos) / Custos  (Or just Revenue/Cost ratio as requested "4.2x")
    // User image shows "4.2x", implying Revenue / Cost or (Revenue-Cost)/Cost + 1.
    // Let's use Revenue / Cost ratio for "ROAS/ROI" style
    $roi = $total_custos > 0 ? ($mrr / $total_custos) : 0;

    // Tempo de Fechamento (Avg days from created_at to updated_at for Won leads)
    // Status 4 = Fechado.
    $stmt = $pdo->prepare("
        SELECT AVG(DATEDIFF(l.updated_at, l.created_at)) as avg_days
        FROM leads l
        WHERE l.status_id = 4 
    ");
    $stmt->execute();
    $avg_days = $stmt->fetch()['avg_days'] ?? 0;
    
    // Fallback if null
    $tempo_fechamento = $avg_days ? round($avg_days) : 0;

    json_response([
        'mrr' => (float)$mrr,
        'cac' => (float)$total_custos,
        'lucro' => (float)$lucro,
        'churn_rate' => (float)$churn_rate,
        'ltv' => (float)$ltv,
        'clientes_ativos' => (int)$clientes_ativos,
        'ticket_medio' => (float)$ticket_medio,

        'novos_mes_atual' => (int)$novos_mes_atual,
        // New
        'cac_real' => (float)$cac,
        'roi_medio' => (float)$roi,
        'tempo_fechamento' => (int)$tempo_fechamento,
        'novos_periodo' => (int)$novos_periodo,
        // Chart Data
        'history' => [
            'labels' => $history_labels,
            'mrr' => $history_mrr,
            'custos' => $history_custos,
            'lucro' => $history_lucro,
            'novos' => $history_novos,
            'cancelados' => $history_cancelados
        ],
        'metodos_pagamento' => $metodos,
        'top_clientes' => $top_clientes,
        'crm' => [
            'total_leads' => (int)$total_leads,
            'pipeline_value' => (float)$pipeline_value,
            'taxa_conversao' => (float)$taxa_conversao,
            'funnel' => $funnel_data,
            'leads_by_source' => $leads_by_source
        ]
    ]);

} catch (PDOException $e) {
    json_response(['error' => 'Erro ao calcular métricas: ' . $e->getMessage()], 500);
}
?>
