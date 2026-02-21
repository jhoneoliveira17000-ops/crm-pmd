<?php
// tests/api_tests.php

$baseUrl = 'http://localhost:8000/api';
$cookieFile = __DIR__ . '/cookie.txt';

function makeRequest($url, $method = 'GET', $data = []) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $httpCode, 'body' => json_decode($response, true), 'raw' => $response];
}

function assertTest($name, $condition, $message = '') {
    if ($condition) {
        echo "✅ [PASS] $name\n";
    } else {
        echo "❌ [FAIL] $name: $message\n";
    }
}

echo "🧪 Running API Tests...\n\n";

// 1. Register User
$testEmail = 'api_test_' . time() . '@test.com';
$res = makeRequest($baseUrl . '/register.php', 'POST', [
    'nome' => 'API Tester',
    'email' => $testEmail,
    'senha' => '123456'
]);
assertTest('Register User', $res['code'] === 201, "Expected 201, got {$res['code']}");

// 2. Login
$res = makeRequest($baseUrl . '/login.php', 'POST', [
    'email' => $testEmail,
    'senha' => '123456'
]);
assertTest('Login', $res['code'] === 200, "Expected 200, got {$res['code']}");

// 3. Fetch Kanban Board
$res = makeRequest($baseUrl . '/kanban.php');
assertTest('Fetch Kanban', $res['code'] === 200 && isset($res['body']['stages']), "Expected 200 and stages data");

// 4. Create Stage
$stageName = 'Stage ' . time();
$res = makeRequest($baseUrl . '/kanban.php', 'POST', [
    'action' => 'create_stage',
    'nome' => $stageName
]);
assertTest('Create Stage', $res['code'] === 200 && $res['body']['success'], "Failed to create stage");
$stageId = $res['body']['id'] ?? null;

// 5. Test Notes (Create Lead first)
$res = makeRequest($baseUrl . '/kanban.php', 'POST', [
    'action' => 'create',
    'nome' => 'Lead Note Test',
    'email' => 'leadnote@test.com',
    'status_id' => 1
]);
$leadId = $res['body']['id'] ?? null;

if ($leadId) {
    $res = makeRequest($baseUrl . '/notes.php', 'POST', [
        'lead_id' => $leadId,
        'note' => 'API Test Note'
    ]);
    assertTest('Add Note', $res['code'] === 200 && $res['body']['success'], "Failed to add note: " . $res['raw']);
} else {
    assertTest('Add Note', false, "Skipped: Could not create lead");
}

// 6. Delete Stage
if ($stageId) {
    $res = makeRequest($baseUrl . '/kanban.php', 'DELETE', [
        'action' => 'delete_stage',
        'id' => $stageId
    ]);
    assertTest('Delete Stage', $res['code'] === 200 && $res['body']['success'], "Failed to delete stage");
}

echo "\nTests Completed.\n";
if (file_exists($cookieFile)) unlink($cookieFile);
