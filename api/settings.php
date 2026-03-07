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

        // Handle File Upload (base64 para persistência em containers Docker)
        // Logo é salvo FORA da transação para evitar limites de tamanho de transação
        $logoDataUri = null;
        if (isset($_FILES['company_logo_file']) && $_FILES['company_logo_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['company_logo_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];
            
            if (in_array($ext, $allowed)) {
                // SVGs são texto — salva direto sem compressão
                if ($ext === 'svg') {
                    $imageData = file_get_contents($file['tmp_name']);
                    $base64 = base64_encode($imageData);
                    $logoDataUri = 'data:image/svg+xml;base64,' . $base64;
                } else {
                    // Comprime imagem com GD para caber no limite de 6MB do TiDB
                    $src = null;
                    switch ($ext) {
                        case 'png': $src = @imagecreatefrompng($file['tmp_name']); break;
                        case 'jpg': case 'jpeg': $src = @imagecreatefromjpeg($file['tmp_name']); break;
                        case 'webp': $src = @imagecreatefromwebp($file['tmp_name']); break;
                        case 'gif': $src = @imagecreatefromgif($file['tmp_name']); break;
                    }
                    
                    if ($src) {
                        // Redimensiona se muito grande (max 800px de largura para logo)
                        $origW = imagesx($src);
                        $origH = imagesy($src);
                        $maxW = 800;
                        
                        if ($origW > $maxW) {
                            $newW = $maxW;
                            $newH = intval($origH * ($maxW / $origW));
                            $resized = imagecreatetruecolor($newW, $newH);
                            // Preserva transparência para PNG
                            imagealphablending($resized, false);
                            imagesavealpha($resized, true);
                            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                            imagedestroy($src);
                            $src = $resized;
                        }
                        
                        // Salva como JPEG comprimido (qualidade 80 — bom visual, tamanho pequeno)
                        ob_start();
                        imagejpeg($src, null, 80);
                        $compressedData = ob_get_clean();
                        imagedestroy($src);
                        
                        $base64 = base64_encode($compressedData);
                        $logoDataUri = 'data:image/jpeg;base64,' . $base64;
                    } else {
                        // Fallback: salva sem compressão se GD falhar
                        $imageData = file_get_contents($file['tmp_name']);
                        $base64 = base64_encode($imageData);
                        $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
                        $mime = $mimeTypes[$ext] ?? 'image/png';
                        $logoDataUri = 'data:' . $mime . ';base64,' . $base64;
                    }
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

        // Salva logo DEPOIS do commit (fora da transação para evitar limites)
        if ($logoDataUri) {
            // Verifica se já existe
            $check = $pdo->prepare("SELECT id FROM config WHERE key_name = 'company_logo' AND user_id = ?");
            $check->execute([$tenantId]);
            
            if ($check->fetch()) {
                $stmt = $pdo->prepare("UPDATE config SET value = ? WHERE key_name = 'company_logo' AND user_id = ?");
                $stmt->execute([$logoDataUri, $tenantId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO config (key_name, value, user_id) VALUES ('company_logo', ?, ?)");
                $stmt->execute([$logoDataUri, $tenantId]);
            }
        }

        json_response(['success' => true, 'message' => 'Configurações salvas.']);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Settings API Error: " . $e->getMessage());
    json_response(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()], 500);
}
