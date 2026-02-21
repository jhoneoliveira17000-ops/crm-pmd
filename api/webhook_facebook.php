<?php
// api/webhook_facebook.php
require_once '../src/db.php';

// Facebook Webhook Verification
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verifyToken = 'SEU_TOKEN_SECRETO'; // Configurar no .env
    $hubMode = $_GET['hub_mode'] ?? '';
    $hubToken = $_GET['hub_verify_token'] ?? '';
    $hubChallenge = $_GET['hub_challenge'] ?? '';

    if ($hubMode === 'subscribe' && $hubToken === $verifyToken) {
        echo $hubChallenge;
        exit;
    }
}

// Receive Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data) {
        // Log raw data
        $stmtLog = $pdo->prepare("INSERT INTO facebook_leads (payload_json) VALUES (?)");
        $stmtLog->execute([$input]);

        // Get Access Token
        $stmtConfig = $pdo->query("SELECT value FROM config WHERE key_name = 'meta_page_access_token'");
        $accessToken = $stmtConfig->fetchColumn();

        if ($accessToken || (isset($data['is_simulation']) && $data['is_simulation'])) {
            // Check for simulation
            if (isset($data['is_simulation']) && $data['is_simulation']) {
                $nome = $data['lead_data']['full_name'] ?? 'Lead Simulado';
                $email = $data['lead_data']['email'] ?? 'teste@exemplo.com';
                $telefone = $data['lead_data']['phone_number'] ?? '11999999999';
                
                $stmtInsert = $pdo->prepare("INSERT INTO leads (nome, email, telefone, valor_estimado, status_id, origem) VALUES (?, ?, ?, 0, 1, 'Facebook Simulado')");
                if($stmtInsert->execute([$nome, $email, $telefone])) {
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
                            
                            // Map Fields (You might need to adjust field names based on your form)
                            // Usually: full_name, phone_number, email
                            $nome = $leadData['full_name'] ?? 'Lead Facebook';
                            $email = $leadData['email'] ?? '';
                            $rawPhone = $leadData['phone_number'] ?? '';
                            
                            // Sanitize Phone (Remove non-digits)
                            $telefone = preg_replace('/\D/', '', $rawPhone);
                            
                            // Insert into Kanban (Stage 1)
                            $stmtInsert = $pdo->prepare("INSERT INTO leads (nome, email, telefone, valor_estimado, status_id, origem) VALUES (?, ?, ?, 0, 1, 'Facebook')");
                            $stmtInsert->execute([$nome, $email, $telefone]);
                        }
                    }
                }
            }
        }
    }
    
    http_response_code(200);
}
?>
