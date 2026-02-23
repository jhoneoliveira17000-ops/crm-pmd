<?php
require_once '../src/db.php';
require_once '../src/auth.php';
require_login(); // Only logged-in users can connect their calendar

// Fetch from global config (user_id IS NULL)
$stmt = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_id' AND user_id IS NULL");
$clientId = $stmt->fetchColumn();

if (empty($clientId)) {
    http_response_code(500);
    echo "Erro: GOOGLE_CLIENT_ID não está configurado no sistema. Contate o administrador.";
    exit;
}

// Redirect URI must match the one configured in Google Cloud Console
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$redirectUri = $protocol . $domainName . dirname($_SERVER['PHP_SELF']) . '/google_callback.php';
$redirectUri = str_replace('//google', '/google', $redirectUri); // Clean URL

$scope = urlencode('https://www.googleapis.com/auth/calendar.events');
$authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" .
    "client_id=" . $clientId .
    "&redirect_uri=" . urlencode($redirectUri) .
    "&response_type=code" .
    "&scope=" . $scope .
    "&access_type=offline" .
    "&prompt=consent"; // Force consent to ensure we get a refresh_token

header('Location: ' . $authUrl);
exit;
?>
