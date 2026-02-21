<?php
// tests/test_kanban_ux.php
require_once __DIR__ . '/../src/db.php';
session_start();
$_SESSION['user_id'] = 1; // Mock Admin

function post($action, $data) {
    global $pdo;
    $_POST = array_merge(['action' => $action], $data);
    
    // Mock input stream
    $input = array_merge(['action' => $action], $data);
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n" .
                         "Cookie: PHPSESSID=" . session_id() . "\r\n",
            'method'  => 'POST',
            'content' => json_encode($input)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost:8000/api/kanban.php', false, $context);
    return json_decode($result, true);
}

function get_stages() {
    $options = [
        'http' => [
             'header'  => "Cookie: PHPSESSID=" . session_id() . "\r\n",
             'method'  => 'GET'
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost:8000/api/kanban.php', false, $context);
    return json_decode($result, true)['stages'];
}

echo "1. Creating Test Stages...\n";
$s1 = post('create_stage', ['nome' => 'UX Test 1'])['id'];
$s2 = post('create_stage', ['nome' => 'UX Test 2'])['id'];
$s3 = post('create_stage', ['nome' => 'UX Test 3'])['id'];
echo "Created: $s1, $s2, $s3\n";

echo "2. Testing Color Update...\n";
$res = post('update_stage_color', ['id' => $s1, 'cor' => '#FF0000']);
echo "Color Update: " . ($res['success'] ? 'OK' : 'FAIL') . "\n";

$stages = get_stages();
$checked = false;
foreach ($stages as $s) {
    if ($s['id'] == $s1 && $s['cor'] == '#FF0000') {
        echo "✅ Color Verified!\n";
        $checked = true;
    }
}
if (!$checked) echo "❌ Color Failed!\n";

echo "3. Testing Reorder...\n";
// Reverse order
$order = [
    ['id' => $s3, 'ordem' => 0],
    ['id' => $s2, 'ordem' => 1],
    ['id' => $s1, 'ordem' => 2]
];
$res = post('update_stage_order', ['order' => $order]);
echo "Order Update: " . ($res['success'] ? 'OK' : 'FAIL') . "\n";

$stages = get_stages();
// Check if s3 is first (ignoring other existing stages, just check relative order or specific index if we knew it... 
// easier: check if s3.ordem < s2.ordem < s1.ordem in the DB, but GET returns ordered list.
// Let's just print names in order
echo "Current Order:\n";
foreach ($stages as $s) {
    if (in_array($s['id'], [$s1, $s2, $s3])) {
        echo "- {$s['nome']} (ID: {$s['id']})\n";
    }
}

echo "4. Cleaning up...\n";
post('delete_stage', ['id' => $s1]);
post('delete_stage', ['id' => $s2]);
post('delete_stage', ['id' => $s3]);
echo "Done.\n";
