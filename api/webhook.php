<?php
// api/webhook_facebook.php
require_once '../src/db.php';

// Facebook Webhook Verification
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $webhookToken = $_GET['token'] ?? '';
    if (!$webhookToken) {
        http_response_code(400); 
        echo "Missing token parameter."; 
        exit;
    }

    $stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE webhook_token = ?");
    $stmtUser->execute([$webhookToken]);
    $userId = $stmtUser->fetchColumn();

    if (!$userId) {
        http_response_code(403);
        echo "Invalid webhook token.";
        exit;
    }

    // 1. Fetch real token from database for this specific tenant
    $stmtConfig = $pdo->prepare("SELECT value FROM config WHERE key_name = 'meta_verify_token' AND user_id = ?");
    $stmtConfig->execute([$userId]);
    $verifyToken = $stmtConfig->fetchColumn();

    $hubMode = $_GET['hub_mode'] ?? '';
    $hubToken = $_GET['hub_verify_token'] ?? '';
    $hubChallenge = $_GET['hub_challenge'] ?? '';

    if ($hubMode === 'subscribe' && $hubToken === $verifyToken) {
        // Meta expects the raw challenge string back to confirm.
        echo $hubChallenge;
        exit;
    } else {
        // Optional: Reject bad tokens explicitly to debug
        http_response_code(403);
        echo "Verification failed. Token mismatch.";
        exit;
    }
}

// Receive Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $webhookToken = $_GET['token'] ?? '';
    if (!$webhookToken) {
        http_response_code(400); 
        exit;
    }

    $stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE webhook_token = ?");
    $stmtUser->execute([$webhookToken]);
    $userId = $stmtUser->fetchColumn();

    if (!$userId) {
        http_response_code(403);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data) {
        // Log raw data
        $stmtLog = $pdo->prepare("INSERT INTO facebook_leads (payload_json, user_id) VALUES (?, ?)");
        $stmtLog->execute([$input, $userId]);

        // Get Access Token
        $stmtConfig = $pdo->prepare("SELECT value FROM config WHERE key_name = 'meta_page_access_token' AND user_id = ?");
        $stmtConfig->execute([$userId]);
        $accessToken = $stmtConfig->fetchColumn();

        if ($accessToken || (isset($data['is_simulation']) && $data['is_simulation'])) {
            // Check for simulation
            if (isset($data['is_simulation']) && $data['is_simulation']) {
                $nome = $data['lead_data']['full_name'] ?? 'Lead Simulado';
                $email = $data['lead_data']['email'] ?? 'teste@exemplo.com';
                $telefone = $data['lead_data']['phone_number'] ?? '11999999999';
                
                $stmtInsert = $pdo->prepare("INSERT INTO leads (nome, email, telefone, valor_estimado, status_id, origem, user_id) VALUES (?, ?, ?, 0, 1, 'Facebook Simulado', ?)");
                if($stmtInsert->execute([$nome, $email, $telefone, $userId])) {
                     echo json_encode(['success' => true, 'message' => 'Lead Simulado Criado']);
                     exit;
                }
            }

            foreach ($data['entry'] as $entry) {
                foreach ($entry['changes'] as $change) {
                    if ($change['field'] === 'leadgen') {
                        $leadGenId = $change['value']['leadgen_id'];
                        
                        // Call Graph API
                        $graphUrl = "https://graph.facebook.com/v18.0/{$leadGenId}?access_token={$accessToken}";
                        $leadDataJson = @file_get_contents($graphUrl);
                        
                        if ($leadDataJson) {
                            $leadData = json_decode($leadDataJson, true);
                            
                            $nome = 'Lead Facebook';
                            $email = '';
                            $rawPhone = '';
                            
                            // A API Graph da Meta retorna os campos dentro do array 'field_data'
                            if (isset($leadData['field_data']) && is_array($leadData['field_data'])) {
                                foreach ($leadData['field_data'] as $field) {
                                    $fieldName = $field['name'] ?? '';
                                    $fieldValue = $field['values'][0] ?? '';
                                    
                                    // Mapeamento flexível de formulários (Pode vir em português ou inglês)
                                    if (strpos($fieldName, 'name') !== false || strpos($fieldName, 'nome') !== false) {
                                        $nome = $fieldValue;
                                    } elseif (strpos($fieldName, 'email') !== false) {
                                        $email = $fieldValue;
                                    } elseif (strpos($fieldName, 'phone') !== false || strpos($fieldName, 'telefone') !== false) {
                                        $rawPhone = $fieldValue;
                                    }
                                }
                            }
                            
                            // Sanitize Phone (Remove non-digits)
                            $telefone = preg_replace('/\D/', '', $rawPhone);
                            
                            // Insert into Kanban (Stage 1) - Saving leadGenId to avoid duplicates and allow offline conversion tracking
                            $stmtInsert = $pdo->prepare("INSERT INTO leads (nome, email, telefone, valor_estimado, status_id, origem, lead_id, user_id) VALUES (?, ?, ?, 0, 1, 'Facebook', ?, ?)");
                            $stmtInsert->execute([$nome, $email, $telefone, $leadGenId, $userId]);
                        }
                    }
                }
            }
        }
    }
    
    http_response_code(200);
}
?>
