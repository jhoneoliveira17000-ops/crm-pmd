<?php
require_once '../src/auth.php';
require_login();

$userId = $_SESSION['user_id'];

$stmtClient = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_id'");
$clientId = $stmtClient->fetchColumn();

$stmtSecret = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_secret'");
$clientSecret = $stmtSecret->fetchColumn();

if (empty($clientId) || empty($clientSecret)) {
    die("Erro Crítico: GOOGLE_CLIENT_ID ou GOOGLE_CLIENT_SECRET não configurados no painel.");
}

$code = $_GET['code'] ?? null;
if (!$code) {
    die("Acesso negado ou erro ao retornar do Google.");
}

// Redirect URI
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$redirectUri = $protocol . $domainName . '/api/google_callback.php';

// Exchange code for tokens
$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = [
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);

if (isset($tokenData['error'])) {
    die("Erro ao trocar código por token: " . htmlspecialchars($tokenData['error_description'] ?? $tokenData['error']));
}

$accessToken = $tokenData['access_token'];
$refreshToken = $tokenData['refresh_token'] ?? null;
$expiresIn = $tokenData['expires_in'] ?? 3600;

// Save to DB
$expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

try {
    // Check if user already has an integration
    $stmtCheck = $pdo->prepare("SELECT id FROM user_integrations WHERE user_id = ? AND provider = 'google'");
    $stmtCheck->execute([$userId]);
    $existing = $stmtCheck->fetchColumn();

    if ($existing) {
        // Update existing tokens. Note: Google only sends refresh_token on FIRST authorization (when prompt=consent).
        if ($refreshToken) {
            $stmtUpdate = $pdo->prepare("UPDATE user_integrations SET access_token = ?, refresh_token = ?, expires_at = ? WHERE id = ?");
            $stmtUpdate->execute([$accessToken, $refreshToken, $expiresAt, $existing]);
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE user_integrations SET access_token = ?, expires_at = ? WHERE id = ?");
            $stmtUpdate->execute([$accessToken, $expiresAt, $existing]);
        }
    } else {
        // Insert new integration
        $stmtInsert = $pdo->prepare("INSERT INTO user_integrations (user_id, provider, access_token, refresh_token, expires_at) VALUES (?, 'google', ?, ?, ?)");
        $stmtInsert->execute([$userId, $accessToken, $refreshToken, $expiresAt]);
    }
    
    // Success, redirect to agenda
    header("Location: ../agenda.php?sync=success");
    exit;
} catch (PDOException $e) {
    die("Erro no Banco de Dados ao salvar Tokens: " . $e->getMessage());
}
?>
