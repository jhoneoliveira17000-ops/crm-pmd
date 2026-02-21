<?php
// api/kanban.php
require_once '../src/auth.php';
require_once '../src/db.php';

// Debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug_kanban.txt');
error_reporting(E_ALL);

header('Content-Type: application/json');
require_login();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        // ... (GET logic unchanged) ...
        // 1. Stages (Map columns to API expectation)
        $stmtStations = $pdo->query("SELECT id, nome as name, ordem, cor as color FROM kanban_stages ORDER BY ordem ASC");
        $stages = $stmtStations->fetchAll(PDO::FETCH_ASSOC);

        // 2. Leads
        // 2. Leads (with Facebook Data)
        $stmtLeads = $pdo->query("
            SELECT l.*, fl.payload_json as facebook_data 
            FROM leads l 
            LEFT JOIN facebook_leads fl ON l.id = fl.lead_id 
            ORDER BY l.created_at DESC
        ");
        $leads = $stmtLeads->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['stages' => $stages, 'leads' => $leads]);

    } elseif ($method === 'POST') {
        $action = $input['action'] ?? '';

        if ($action === 'save_lead') {
            // Upsert Lead (Create or Update)
            $id = !empty($input['id']) ? $input['id'] : null;
            $nome = $input['nome'];
            $valor = $input['valor'] ?? 0;
            $telefone = $input['contato'] ?? ''; // Map contato to telefone
            $statusId = $input['etapa_id'] ?? 1;
            $origem = $input['origem'] ?? 'Manual';
            
            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE leads SET nome = ?, valor_estimado = ?, telefone = ?, status_id = ?, origem = ? WHERE id = ?");
                $stmt->execute([$nome, $valor, $telefone, $statusId, $origem, $id]);
                 echo json_encode(['success' => true, 'message' => 'Lead atualizado']);
            } else {
                // Create
                $stmt = $pdo->prepare("INSERT INTO leads (nome, telefone, valor_estimado, status_id, origem) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $telefone, $valor, $statusId, $origem]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            }

        } elseif ($action === 'move_lead') {
            // Move Lead
            $leadId = $input['lead_id'];
            $newStageId = $input['stage_id'];
            
            // Get old stage for history
            $stmtOld = $pdo->prepare("SELECT status_id FROM leads WHERE id = ?");
            $stmtOld->execute([$leadId]);
            $oldStageId = $stmtOld->fetchColumn();

            // Update
            $stmt = $pdo->prepare("UPDATE leads SET status_id = ? WHERE id = ?");
            $stmt->execute([$newStageId, $leadId]);

            // Log History
            if ($oldStageId != $newStageId) {
                $stmtHist = $pdo->prepare("INSERT INTO lead_history (lead_id, de_estagio_id, para_estagio_id, usuario_id) VALUES (?, ?, ?, ?)");
                $stmtHist->execute([$leadId, $oldStageId, $newStageId, $_SESSION['user_id']]);
            }

            echo json_encode(['success' => true]);
        
        } elseif ($action === 'get_history') {
            $leadId = $input['lead_id'] ?? $_GET['lead_id'] ?? null;
            if (!$leadId) {
                echo json_encode([]);
                exit;
            }

            // Fetch history with user names and stage names
            $stmt = $pdo->prepare("
                SELECT lh.*, u.nome as usuario_nome,
                       ks_de.nome as de_estagio_nome,
                       ks_para.nome as para_estagio_nome
                FROM lead_history lh
                LEFT JOIN usuarios u ON lh.usuario_id = u.id
                LEFT JOIN kanban_stages ks_de ON lh.de_estagio_id = ks_de.id
                LEFT JOIN kanban_stages ks_para ON lh.para_estagio_id = ks_para.id
                WHERE lh.lead_id = ?
                ORDER BY lh.created_at DESC
            ");
            $stmt->execute([$leadId]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

        } elseif ($action === 'add_stage') {
            $nome = trim($input['nome']);
            $cor = $input['cor'] ?? '#cbd5e1';
            
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome obrigatório']);
                exit;
            }
            
            $stmt = $pdo->query("SELECT MAX(ordem) FROM kanban_stages");
            $maxOrder = $stmt->fetchColumn() ?: 0;
            
            $stmt = $pdo->prepare("INSERT INTO kanban_stages (nome, ordem, cor) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $maxOrder + 1, $cor]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

        } elseif ($action === 'rename_stage') {
            $stageId = $input['stage_id'];
            $newName = trim($input['nome']);
            
            if (!$stageId || empty($newName)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE kanban_stages SET nome = ? WHERE id = ?");
            $stmt->execute([$newName, $stageId]);
            
            echo json_encode(['success' => true]);

        } elseif ($action === 'delete_stage') {
             $id = $input['id'];
             
             // Check if has leads
             $stmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE status_id = ?");
             $stmt->execute([$id]);
             if ($stmt->fetchColumn() > 0) {
                 http_response_code(400);
                 echo json_encode(['error' => 'Não é possível excluir etapa com leads. Mova-os primeiro.']);
                 exit;
             }
             
             $stmt = $pdo->prepare("DELETE FROM kanban_stages WHERE id = ?");
             $stmt->execute([$id]);
             echo json_encode(['success' => true]);

        } elseif ($action === 'update_stage_order') {
            $order = $input['order']; // [{id: 1, ordem: 0}, ...]
            
            if (!is_array($order)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data format']);
                exit;
            }

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE kanban_stages SET ordem = ? WHERE id = ?");
                foreach ($order as $item) {
                    $stmt->execute([$item['ordem'], $item['id']]);
                }
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } elseif ($action === 'update_stage_color') {
            $id = $input['id'];
            $cor = $input['cor'];
            
            if (!$id || !$cor) {
                 http_response_code(400);
                 echo json_encode(['error' => 'ID and Color required']);
                 exit;
            }

            $stmt = $pdo->prepare("UPDATE kanban_stages SET cor = ? WHERE id = ?");
            $stmt->execute([$cor, $id]);
            echo json_encode(['success' => true]);
        
        } elseif ($action === 'convert_lead') {
            // Convert Lead to Client
            $leadId = $input['lead_id'];
            
            // Fetch Lead Data
            $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
            $stmt->execute([$leadId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lead) {
                http_response_code(404);
                echo json_encode(['error' => 'Lead não encontrado (ID: ' . $leadId . ')']);
                exit;
            }
            
            try {
                $pdo->beginTransaction();
                
                // Insert into Clients
                $stmtClient = $pdo->prepare("INSERT INTO clientes (
                    nome_empresa, nome_responsavel, telefone, valor_mensal, 
                    canal_aquisicao, lead_id, status_contrato, data_entrada
                ) VALUES (?, ?, ?, ?, ?, ?, 'ativo', NOW())");
                
                $stmtClient->execute([
                    $lead['nome'],              // nome_empresa
                    $lead['nome'],              // nome_responsavel (same for now)
                    $lead['telefone'],
                    $lead['valor_estimado'],
                    $lead['origem'],
                    $lead['id']
                ]);
                
                $clientId = $pdo->lastInsertId();
                
                // Update Lead Status (e.g., move to a 'Converted' stage if exists, or just mark somehow)
                // For now, let's look for a stage named 'Fechado' or 'Ganhou'
                $stmtStage = $pdo->prepare("SELECT id FROM kanban_stages WHERE nome LIKE '%Fechado%' OR nome LIKE '%Ganhou%' OR nome LIKE '%Venda%' LIMIT 1");
                $stmtStage->execute();
                $wonStageId = $stmtStage->fetchColumn();
                
                if ($wonStageId) {
                    $stmtUpdate = $pdo->prepare("UPDATE leads SET status_id = ? WHERE id = ?");
                    $stmtUpdate->execute([$wonStageId, $leadId]);
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'client_id' => $clientId, 'message' => 'Lead convertido em cliente!']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Erro ao converter lead: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao converter: ' . $e->getMessage()]);
            }
        }

    } elseif ($method === 'DELETE') {
        $id = $input['id'];
        
        try {
            $pdo->beginTransaction();
            // Delete dependent records first to prevent foreign key constraint errors
            $pdo->prepare("DELETE FROM lead_history WHERE lead_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM lead_notes WHERE lead_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM facebook_leads WHERE lead_id = ?")->execute([$id]);
            // Attempt to nullify any client references if a foreign key exists to avoid block
            $pdo->prepare("UPDATE clientes SET lead_id = NULL WHERE lead_id = ?")->execute([$id]);
            
            // Delete the main lead record
            $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
            $stmt->execute([$id]);
            
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Delete Lead Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir lead: ' . $e->getMessage()]);
        }
    }

} catch (Exception $e) {
    error_log("Kanban API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
