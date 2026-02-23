<?php
require_once '../src/auth.php';
require_login();

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// Helper function to get valid access token (refreshes if needed)
function getValidAccessToken($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT access_token, refresh_token, expires_at FROM user_integrations WHERE user_id = ? AND provider = 'google'");
    $stmt->execute([$userId]);
    $integration = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$integration) {
        return null; // Not connected
    }

    // Check expiration (Adding a 5-minute buffer)
    $expiresAtTime = strtotime($integration['expires_at']);
    if (($expiresAtTime - 300) < time()) {
        if (empty($integration['refresh_token'])) {
            return null; // Expired and no way to refresh
        }
        
        // Refresh Token
        $stmtClient = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_id'");
        $clientId = $stmtClient->fetchColumn();
        $stmtSecret = $pdo->query("SELECT value FROM config WHERE key_name = 'google_client_secret'");
        $clientSecret = $stmtSecret->fetchColumn();

        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration['refresh_token'],
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (isset($tokenData['access_token'])) {
            $newAccessToken = $tokenData['access_token'];
            $newExpiresIn = $tokenData['expires_in'] ?? 3600;
            $newExpiresAt = date('Y-m-d H:i:s', time() + $newExpiresIn);

            $stmtUpdate = $pdo->prepare("UPDATE user_integrations SET access_token = ?, expires_at = ? WHERE user_id = ? AND provider = 'google'");
            $stmtUpdate->execute([$newAccessToken, $newExpiresAt, $userId]);
            
            return $newAccessToken;
        } else {
            return null; // Refresh failed
        }
    }

    return $integration['access_token'];
}

$accessToken = getValidAccessToken($pdo, $userId);

if (!$accessToken) {
    http_response_code(401);
    echo json_encode(['error' => 'not_connected', 'message' => 'Usuário não conectou a conta do Google ou o token expirou.']);
    exit;
}

// Function to make Google API Calls
function callGoogleApi($url, $accessToken, $method = 'GET', $data = null) {
    $ch = curl_init($url);
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['status' => $httpCode, 'data' => json_decode($response, true)];
}

// FullCalendar inputs (ISO format strings)
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$calendarId = 'primary'; // We sync the user's primary calendar

if ($method === 'GET') {
    // Fetch Events
    if (!$start || !$end) {
        $start = date('Y-m-d\T00:00:00P', strtotime('-1 month'));
        $end = date('Y-m-d\T23:59:59P', strtotime('+2 months'));
    }
    
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events?timeMin=" . urlencode($start) . "&timeMax=" . urlencode($end) . "&singleEvents=true&orderBy=startTime";
    $apiResponse = callGoogleApi($url, $accessToken);
    
    if ($apiResponse['status'] === 200) {
        $events = [];
        foreach ($apiResponse['data']['items'] ?? [] as $item) {
            $events[] = [
                'id' => $item['id'],
                'title' => $item['summary'] ?? '(Sem Título)',
                'start' => $item['start']['dateTime'] ?? $item['start']['date'],
                'end' => $item['end']['dateTime'] ?? $item['end']['date'],
                'allDay' => isset($item['start']['date']),
                'url' => $item['htmlLink'] ?? '',
                'extendedProps' => [
                    'description' => $item['description'] ?? ''
                ]
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($events);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'google_api_error', 'details' => $apiResponse]);
    }
} elseif ($method === 'POST') {
    // Create Event
    $input = json_decode(file_get_contents('php://input'), true);
    
    $eventData = [
        'summary' => $input['title'] ?? 'Novo Evento CRM',
        'description' => $input['description'] ?? '',
        'start' => [
            'dateTime' => $input['start'],
            'timeZone' => 'America/Sao_Paulo' // Default timezone
        ],
        'end' => [
            'dateTime' => $input['end'],
            'timeZone' => 'America/Sao_Paulo'
        ]
    ];
    
    // Handle All day events correctly
    if (!empty($input['allDay']) && $input['allDay'] === true) {
        $eventData['start'] = ['date' => substr($input['start'], 0, 10)];
        $eventData['end'] = ['date' => substr($input['end'], 0, 10)]; // Consider +1 day logic in frontend
    }

    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events";
    $apiResponse = callGoogleApi($url, $accessToken, 'POST', $eventData);
    
    header('Content-Type: application/json');
    echo json_encode($apiResponse);

} elseif ($method === 'PUT') {
    // Update Event (Drag-and-drop or resize)
    $input = json_decode(file_get_contents('php://input'), true);
    $eventId = $input['id'] ?? null;
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing event ID']);
        exit;
    }

    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}";
    
    // We should ideally fetch the current event and patch it, but for simplicity we will do a targeted PATCH style update via PUT replacing required fields. 
    // To be safe with Google API, we can use PATCH method.
    $patchData = [];
    if (isset($input['start'])) $patchData['start'] = ['dateTime' => $input['start']];
    if (isset($input['end'])) $patchData['end'] = ['dateTime' => $input['end']];
    if (isset($input['title'])) $patchData['summary'] = $input['title'];
    if (isset($input['allDay']) && $input['allDay']) {
         $patchData['start'] = ['date' => substr($input['start'], 0, 10)];
         $patchData['end'] = ['date' => substr($input['end'], 0, 10)];
    }

    $apiResponse = callGoogleApi($url, $accessToken, 'PATCH', $patchData);
    header('Content-Type: application/json');
    echo json_encode($apiResponse);
    
} elseif ($method === 'DELETE') {
    // Delete Event
    $eventId = $_GET['id'] ?? null;
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing event ID']);
        exit;
    }
    
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}";
    $apiResponse = callGoogleApi($url, $accessToken, 'DELETE');
    header('Content-Type: application/json');
    echo json_encode($apiResponse);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
