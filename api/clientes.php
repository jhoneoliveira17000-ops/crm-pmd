<?php
// PMDCRM/api/clientes.php

// Debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug_error.txt');
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../src/db.php';
    require_once __DIR__ . '/../src/auth.php';
    require_once __DIR__ . '/../src/utils.php';

    require_login();

    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST; // Fallback for FormData

    // === GET ===
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM clientes ORDER BY created_at DESC");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($clientes);
        exit;
    }

    // === DELETE ===
    if ($method === 'DELETE') {
        require_login(); // Allow any logged user to delete
        
        if (empty($input['id'])) {
            json_response(['error' => 'ID obrigatório'], 400);
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([(int)$input['id']]);
            json_response(['success' => true, 'message' => 'Cliente excluído com sucesso']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'error' => 'Erro ao excluir cliente: ' . $e->getMessage()], 500);
        }
        exit;
    }

    // === POST (CREATE or UPDATE) ===
    if ($method === 'POST') {
        
        // Map frontend fields to DB columns
        if (isset($input['link_pasta'])) $input['pasta_drive_url'] = $input['link_pasta'];
        if (isset($input['dia_vencimento'])) $input['dia_pagamento'] = $input['dia_vencimento'];
        if (isset($input['data_inicio'])) $input['data_inicio_contrato'] = $input['data_inicio'];
        if (isset($input['duracao_contrato'])) $input['periodo_meses'] = $input['duracao_contrato'];
        if (isset($input['whatsapp'])) $input['telefone'] = $input['whatsapp'];
        if (isset($input['cnpj'])) $input['cnpj'] = $input['cnpj']; // CNPJ missing in schema but let's keep it if added later

        // Determine Update (has ID) or Create (no ID)
        if (!empty($input['id'])) {
            // --- UPDATE ---
            $id = (int)$input['id'];
            $campos = [];
            $valores = [];

            $permitidos = [
                'nome_empresa', 'nome_responsavel', 'email', 'telefone', 'segmento', 
                'foto_perfil', 'pasta_drive_url', 'canal_aquisicao', 'data_entrada',
                // Contrato
                'plano_nome', 'valor_mensal', 'periodo_meses', 'data_inicio_contrato', 'data_fim_contrato', 'status_contrato',
                // Pagamento
                'dia_pagamento', 'metodo_pagamento', 'ultimo_pagamento',
                'lead_id',
                // New Fields
                'nicho', 'origem', 'endereco', 'cidade', 'estado', 'cep', 'obs',
                'instagram', 'landing_page_url', 'produto_servico'
            ];

            foreach ($permitidos as $campo) {
                if (isset($input[$campo])) {
                    $campos[] = "$campo = ?";
                    if ($campo === 'email') {
                        $valores[] = filter_var($input[$campo], FILTER_SANITIZE_EMAIL);
                    } elseif ($campo === 'valor_mensal') {
                        $valores[] = floatval($input[$campo]);
                    } elseif ($campo === 'foto_perfil') {
                        $valores[] = substr($input[$campo] ?? '', 0, 2048); // Truncate safely just in case
                    } else {
                        $valores[] = sanitize_input($input[$campo]);
                    }
                }
            }

            // Recalcular data_fim se necessário
            if (isset($input['periodo_meses']) && !isset($input['data_fim_contrato'])) {
                 // Busca data_inicio atual se não foi enviada
                if (!isset($input['data_inicio_contrato'])) {
                    $stmt = $pdo->prepare("SELECT data_inicio_contrato FROM clientes WHERE id = ?");
                    $stmt->execute([$id]);
                    $current = $stmt->fetch();
                    $start = $current['data_inicio_contrato'];
                } else {
                    $start = $input['data_inicio_contrato'];
                }
                
                if ($start) {
                    $end = date('Y-m-d', strtotime($start . ' +' . intval($input['periodo_meses']) . ' months'));
                    $campos[] = "data_fim_contrato = ?";
                    $valores[] = $end;
                }
            }

            if (empty($campos)) {
                 json_response(['success' => true, 'message' => 'Nada para atualizar']);
            }

            $valores[] = $id;
            $sql = "UPDATE clientes SET " . implode(', ', $campos) . " WHERE id = ?";

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($valores);
                json_response(['success' => true, 'message' => 'Cliente atualizado com sucesso']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()], 500);
            }

        } else {
            // --- CREATE ---
            // Validação básica
            if (empty($input['nome_empresa'])) {
                json_response(['success' => false, 'message' => 'Nome da empresa é obrigatório'], 400);
            }

            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO clientes (
                    nome_empresa, nome_responsavel, email, telefone, segmento, 
                    foto_perfil, pasta_drive_url, canal_aquisicao, data_entrada,
                    plano_nome, valor_mensal, periodo_meses, 
                    data_inicio_contrato, data_fim_contrato, status_contrato,
                    dia_pagamento, metodo_pagamento,
                    instagram, landing_page_url, produto_servico, lead_id,
                    nicho, origem, endereco, cidade, estado, obs
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $data_entrada = $input['data_entrada'] ?? date('Y-m-d');
                $data_inicio = $input['data_inicio_contrato'] ?? $data_entrada;
                $data_fim = $input['data_fim_contrato'] ?? date('Y-m-d', strtotime($data_inicio . ' +' . intval($input['periodo_meses'] ?? 12) . ' months'));

                // Log payload for debug
                // file_put_contents(__DIR__ . '/../debug_insert.txt', print_r($input, true));

                $stmt->execute([
                    sanitize_input($input['nome_empresa']),
                    sanitize_input($input['nome_responsavel'] ?? ''),
                    filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL),
                    sanitize_input($input['telefone'] ?? ''), // Mapped from whatsapp
                    sanitize_input($input['segmento'] ?? ''),
                    // Foto Perfil
                    substr($input['foto_perfil'] ?? '', 0, 2048),
                    sanitize_input($input['pasta_drive_url'] ?? ''), // Mapped from link_pasta
                    sanitize_input($input['canal_aquisicao'] ?? ''),
                    $data_entrada,
                    
                    // Contrato
                    sanitize_input($input['plano_nome'] ?? 'Padrão'),
                    floatval($input['valor_mensal'] ?? 0),
                    intval($input['periodo_meses'] ?? 12),
                    $data_inicio,
                    $data_fim,
                    $input['status_contrato'] ?? 'ativo',
                    
                    // Pagamento
                    intval($input['dia_pagamento'] ?? 5), // Mapped from dia_vencimento
                    sanitize_input($input['metodo_pagamento'] ?? 'boleto'),
                    
                    // Outros
                    sanitize_input($input['instagram'] ?? ''),
                    sanitize_input($input['landing_page_url'] ?? ''),
                    sanitize_input($input['produto_servico'] ?? ''),
                    !empty($input['lead_id']) ? intval($input['lead_id']) : null,
                    
                    // New fields
                    sanitize_input($input['nicho'] ?? ''),
                    sanitize_input($input['origem'] ?? ''),
                    sanitize_input($input['endereco'] ?? ''),
                    sanitize_input($input['cidade'] ?? ''),
                    sanitize_input($input['estado'] ?? ''),
                    sanitize_input($input['obs'] ?? '')
                ]);

                $clienteId = $pdo->lastInsertId();
                $pdo->commit();
                json_response(['success' => true, 'message' => 'Cliente criado com sucesso', 'id' => $clienteId], 201);

            } catch (PDOException $e) {
                $pdo->rollBack();
                file_put_contents(__DIR__ . '/../debug_insert.txt', "PDO Error: " . $e->getMessage() . "\n", FILE_APPEND);
                error_log("Erro ao criar cliente PDO: " . $e->getMessage()); 
                json_response(['success' => false, 'message' => 'Erro ao criar: ' . $e->getMessage()], 500);
            } catch (Exception $e) {
                $pdo->rollBack();
                file_put_contents(__DIR__ . '/../debug_insert.txt', "General Error: " . $e->getMessage() . "\n", FILE_APPEND);
                json_response(['success' => false, 'message' => 'Erro desconhecido: ' . $e->getMessage()], 500);
            }
        }
        exit;
    }

    // Fallback method not allowed
    json_response(['success' => false, 'error' => 'Método inválido'], 405);

} catch (Throwable $t) {
    error_log("Erro Fatal API Clientes: " . $t->getMessage() . " in " . $t->getFile() . ":" . $t->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno do servidor: ' . $t->getMessage()]);
}
?>
