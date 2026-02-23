<?php
// PMDCRM/api/google_callback.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

session_start();

$code = $_GET['code'] ?? null;
if (!$code) {
    die("Acesso negado ou erro ao retornar do Google.");
}

// Fetch Google OAuth Credentials from global config
$stmtClient = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_id' AND user_id IS NULL");
$clientId = $stmtClient->fetchColumn();

$stmtSecret = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_secret' AND user_id IS NULL");
$clientSecret = $stmtSecret->fetchColumn();

if (empty($clientId) || empty($clientSecret)) {
    die("Erro Crítico: GOOGLE_CLIENT_ID ou GOOGLE_CLIENT_SECRET não configurados no painel Super Admin.");
}

// Redirect URI needs to match exactly what Google configured
$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$protocol = $isSecure ? 'https://' : 'http://';

$domainName = $_SERVER['HTTP_HOST'];
$redirectUri = $protocol . $domainName . dirname($_SERVER['PHP_SELF']) . '/google_callback.php';
$redirectUri = str_replace('//google', '/google', $redirectUri); // Safety cleanup

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
$expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

// Get User Profile Data
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$chInfo = curl_init($userInfoUrl);
curl_setopt($chInfo, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chInfo, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
$userInfoResponse = curl_exec($chInfo);
curl_close($chInfo);

$userInfo = json_decode($userInfoResponse, true);

if (!isset($userInfo['email'])) {
    die("Falha ao obter o perfil do Google.");
}

$email = $userInfo['email'];
$nome = $userInfo['name'] ?? 'Usuário Google';
$foto = $userInfo['picture'] ?? '';
$googleId = $userInfo['id'];

try {
    $pdo->beginTransaction();

    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        // SCENARIO 1: User is already logged in (Account Linking from Settings/Agenda)
        // Just verify/update the tokens for this user.
        $stmtCheck = $pdo->prepare("SELECT id FROM user_integrations WHERE user_id = ? AND provider = 'google'");
        $stmtCheck->execute([$userId]);
        $existing = $stmtCheck->fetchColumn();

        if ($existing) {
            if ($refreshToken) {
                $stmtUpdate = $pdo->prepare("UPDATE user_integrations SET access_token = ?, refresh_token = ?, expires_at = ?, identifier = ? WHERE id = ?");
                $stmtUpdate->execute([$accessToken, $refreshToken, $expiresAt, $googleId, $existing]);
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE user_integrations SET access_token = ?, expires_at = ?, identifier = ? WHERE id = ?");
                $stmtUpdate->execute([$accessToken, $expiresAt, $googleId, $existing]);
            }
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO user_integrations (user_id, provider, access_token, refresh_token, expires_at, identifier) VALUES (?, 'google', ?, ?, ?, ?)");
            $stmtInsert->execute([$userId, $accessToken, $refreshToken, $expiresAt, $googleId]);
        }
        $pdo->commit();
        header("Location: ../agenda.php?sync=success");
        exit;

    } else {
        // SCENARIO 2: User is NOT logged in (SSO Login or Signup)
        $stmt = $pdo->prepare("SELECT id, nome, role, foto_perfil FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // User exists, login
            $userId = $user['id'];
            
            if (empty($user['foto_perfil']) && !empty($foto)) {
                $stmtUpdatePic = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
                $stmtUpdatePic->execute([$foto, $userId]);
                $user['foto_perfil'] = $foto;
            }
        } else {
            // User doesn't exist, create Tenant
            $webhookToken = bin2hex(random_bytes(16));
            $randomPass = bin2hex(random_bytes(10));
            $hashObj = password_hash($randomPass, PASSWORD_DEFAULT);
            
            $stmtInsertUser = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role, foto_perfil, webhook_token) VALUES (?, ?, ?, 'user', ?, ?)");
            $stmtInsertUser->execute([$nome, $email, $hashObj, $foto, $webhookToken]);
            $userId = $pdo->lastInsertId();
            
            // Seed default Kanban Stages
            $stmtSeed = $pdo->prepare("INSERT INTO kanban_stages (nome, cor, ordem, user_id) VALUES 
                ('Novo Lead', 'gray', 1, ?),
                ('Em Negociação', 'blue', 2, ?),
                ('Aguardando Visita', 'yellow', 3, ?),
                ('Fechado', 'green', 4, ?)");
            $stmtSeed->execute([$userId, $userId, $userId, $userId]);
            
            $user = [
                'id' => $userId,
                'nome' => $nome,
                'role' => 'user',
                'foto_perfil' => $foto
            ];
        }

        // Upsert Google Integration tokens
        $stmtCheckInt = $pdo->prepare("SELECT id FROM user_integrations WHERE user_id = ? AND provider = 'google'");
        $stmtCheckInt->execute([$userId]);
        $existingIntId = $stmtCheckInt->fetchColumn();

        if ($existingIntId) {
            if ($refreshToken) {
                $stmtUpsert = $pdo->prepare("UPDATE user_integrations SET access_token = ?, refresh_token = ?, expires_at = ?, identifier = ? WHERE id = ?");
                $stmtUpsert->execute([$accessToken, $refreshToken, $expiresAt, $googleId, $existingIntId]);
            } else {
                $stmtUpsert = $pdo->prepare("UPDATE user_integrations SET access_token = ?, expires_at = ?, identifier = ? WHERE id = ?");
                $stmtUpsert->execute([$accessToken, $expiresAt, $googleId, $existingIntId]);
            }
        } else {
            $stmtUpsert = $pdo->prepare("INSERT INTO user_integrations (user_id, provider, identifier, access_token, refresh_token, expires_at) VALUES (?, 'google', ?, ?, ?, ?)");
            $stmtUpsert->execute([$userId, $googleId, $accessToken, $refreshToken, $expiresAt]);
        }

        $pdo->commit();

        // Start Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_foto'] = $user['foto_perfil'] ?? '';
        
        // Redirect to Dashboard
        header("Location: ../dashboard.php");
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erro interno: " . $e->getMessage());
}
?>
