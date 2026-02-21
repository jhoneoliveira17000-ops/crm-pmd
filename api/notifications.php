<?php
// PMDCRM/api/notifications.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

try {
    $hoje = date('Y-m-d');
    $trintaDias = date('Y-m-d', strtotime('+30 days'));
    
    $notificacoes = [];
    
    // 1. Contratos vencendo nos próximos 30 dias
    $stmt = $pdo->prepare("
        SELECT 
            id, 
            nome_empresa, 
            data_fim_contrato as data_alerta,
            DATEDIFF(data_fim_contrato, ?) as dias_restantes,
            'contrato' as tipo
        FROM clientes 
        WHERE 
            status_contrato = 'ativo' 
            AND data_fim_contrato BETWEEN ? AND ?
        ORDER BY data_fim_contrato ASC
    ");
    
    $stmt->execute([$hoje, $hoje, $trintaDias]);
    $notificacoes = array_merge($notificacoes, $stmt->fetchAll());
    
    // 2. Pagamentos próximos (10, 5, 1 dia)
    // Calcula o próximo dia de pagamento baseado no dia_pagamento
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nome_empresa,
            dia_pagamento,
            ultimo_pagamento,
            'pagamento' as tipo
        FROM clientes 
        WHERE 
            status_contrato = 'ativo'
    ");
    
    $stmt->execute();
    $clientes = $stmt->fetchAll();
    
    foreach ($clientes as $c) {
        $diaVencimento = $c['dia_pagamento'] ?: 5;
        $ultimoPagamento = $c['ultimo_pagamento'] ? new DateTime($c['ultimo_pagamento']) : null;
        $hoje_dt = new DateTime($hoje);
        
        // Calcula próximo vencimento
        $proximoVencimento = clone $hoje_dt;
        $proximoVencimento->setDate($hoje_dt->format('Y'), $hoje_dt->format('m'), $diaVencimento);
        
        // Se já passou o dia neste mês, pega o próximo mês
        if ($proximoVencimento < $hoje_dt) {
            $proximoVencimento->modify('+1 month');
        }
        
        // Verifica se já pagou este mês
        $jaPagouEsteMes = $ultimoPagamento && 
                          $ultimoPagamento->format('Y-m') === $hoje_dt->format('Y-m');
        
        if (!$jaPagouEsteMes) {
            $diff = $hoje_dt->diff($proximoVencimento);
            $diasRestantes = (int)$diff->format('%r%a');
            
            // Notifica se estiver dentro de 10 dias
            if ($diasRestantes >= 0 && $diasRestantes <= 10) {
                $notificacoes[] = [
                    'id' => $c['id'],
                    'nome_empresa' => $c['nome_empresa'],
                    'data_alerta' => $proximoVencimento->format('Y-m-d'),
                    'dias_restantes' => $diasRestantes,
                    'tipo' => 'pagamento'
                ];
            }
        }
    }
    


    // 3. Leads Estagnados ou Novos (CRM)
    // Buscas leads em 'Novo Lead' (status_id=1) 
    $stmtLeads = $pdo->query("SELECT count(*) FROM leads WHERE status_id = 1");
    $novosLeads = $stmtLeads->fetchColumn();

    if ($novosLeads > 0) {
        $notificacoes[] = [
            'id' => 0,
            'nome_empresa' => "CRM Pipeline",
            'data_alerta' => date('Y-m-d'),
            'dias_restantes' => 0,
            'tipo' => 'lead',
            'mensagem' => "Você tem $novosLeads novos leads aguardando contato."
        ];
    }
    
    // Ordena por dias restantes (mais urgente primeiro)
    usort($notificacoes, function($a, $b) {
        return $a['dias_restantes'] - $b['dias_restantes'];
    });
    
    json_response($notificacoes);

} catch (PDOException $e) {
    json_response(['error' => 'Erro ao buscar notificações'], 500);
}
?>
