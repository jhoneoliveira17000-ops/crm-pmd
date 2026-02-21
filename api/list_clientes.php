<?php
// PMDCRM/api/list_clientes.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

try {
    $stmt = $pdo->query("
        SELECT 
            id, 
            nome_empresa, 
            nome_responsavel, 
            email, 
            telefone, 
            segmento, 
            foto_perfil, 
            pasta_drive_url, 
            canal_aquisicao, 
            data_entrada, 
            plano_nome, 
            valor_mensal, 
            periodo_meses, 
            data_inicio_contrato, 
            data_fim_contrato, 
            status_contrato,
            dia_pagamento,
            metodo_pagamento,
            ultimo_pagamento
        FROM clientes 
        ORDER BY created_at DESC
    ");
    $clientes = $stmt->fetchAll();
    
    // Ensure UTF-8
    array_walk_recursive($clientes, function(&$item) {
        if (is_string($item)) {
            $item =  mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1'); 
            // Better: just ensure valid UTF8 if not already
            if (!mb_check_encoding($item, 'UTF-8')) {
                $item = mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1');
            }
        }
    });

    json_response($clientes);
} catch (PDOException $e) {
    json_response(['error' => 'Erro ao listar clientes'], 500);
}
?>
