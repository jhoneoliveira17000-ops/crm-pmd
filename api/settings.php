<?php
// api/settings.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');

require_login();

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Fetch All Public Settings
        // Be careful not to expose sensitive server secrets if added in future
        // Fetch All Public Settings for this Tenant
        $tenantScope = get_tenant_condition();
        $stmt = $pdo->query("SELECT key_name, value FROM config WHERE {$tenantScope}");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Fetch User Webhook Token
        $userId = get_current_user_id();
        $stmtToken = $pdo->prepare("SELECT webhook_token FROM usuarios WHERE id = ?");
        $stmtToken->execute([$userId]);
        $token = $stmtToken->fetchColumn();
        $settings['webhook_token'] = $token;
        
        // Return structured object
        json_response(['success' => true, 'data' => $settings]);
        
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // If coming from FormData (file upload), input might be null, so check $_POST
        if (!$input && !empty($_POST['settings'])) {
            $input = ['settings' => json_decode($_POST['settings'], true)];
        }

        if (!isset($input['settings']) || !is_array($input['settings'])) {
            // If only file is being uploaded without settings JSON (unlikely but possible)
            if (!isset($_FILES['company_logo_file'])) {
                 json_response(['error' => 'Formato inválido'], 400);
            }
            $input = ['settings' => []]; // Initialize empty to avoid loops error
        }

        $pdo->beginTransaction();
        
        $allowedKeys = [
            'meta_verify_token',
            'meta_page_access_token',
            'meta_page_id',
            'whatsapp_default_msg',
            'whatsapp_templates_json',
            'company_logo',
            'theme_color'
        ];

        $tenantId = get_tenant_id();

        // Handle File Upload
        if (isset($_FILES['company_logo_file']) && $_FILES['company_logo_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['company_logo_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            
            if (in_array($ext, $allowed)) {
                $uploadDir = __DIR__ . '/../assets/uploads/logos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $filename = 'logo_' . time() . '.' . $ext;
                $dest = $uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $url = 'assets/uploads/logos/' . $filename;
                    $stmt = $pdo->prepare("INSERT INTO config (key_name, value, user_id) VALUES ('company_logo', ?, ?) ON DUPLICATE KEY UPDATE value = ?");
                    $stmt->execute([$url, $tenantId, $url]);
                }
            }
        }

        foreach ($input['settings'] as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                $val = $value;
                // Don't htmlspecialchar JSON fields, just strip tags to be safe but keep structure
                if ($key === 'whatsapp_templates_json') {
                    $val = strip_tags($value); 
                } else {
                    $val = sanitize_input($value);
                }
                $stmt = $pdo->prepare("INSERT INTO config (key_name, value, user_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$key, $val, $tenantId, $val]);
            }
        }
        
        $pdo->commit();
        json_response(['success' => true, 'message' => 'Configurações salvas.']);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Settings API Error: " . $e->getMessage());
    json_response(['error' => 'Erro ao salvar configurações'], 500);
}
