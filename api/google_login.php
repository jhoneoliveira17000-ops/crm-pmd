<?php
// PMDCRM/api/google_login.php
require_once __DIR__ . '/../src/db.php';

// Fetch Google OAuth Credentials from global config (Not scoped to tenant as this is system-wide)
$stmt = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_id' AND user_id IS NULL");
$clientId = $stmt->fetchColumn();

if (!$clientId) {
    // Attempt fallback from env or display error
    die("Google Client ID não configurado no sistema (Painel Super Admin).");
}

// Ensure HTTPS is used for redirect URI in production, but allow HTTP for localhost
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$redirectUri = $protocol . $domainName . dirname(dirname($_SERVER['PHP_SELF'])) . '/api/google_callback.php';

// Clean redirect URI path (remove any double slashes)
$redirectUri = str_replace('//api', '/api', $redirectUri);

$scope = "email profile openid https://www.googleapis.com/auth/calendar";

$authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'offline', // Request refresh token
    'prompt' => 'consent'       // Force consent to always get refresh token
]);

header("Location: $authUrl");
exit;
?>
