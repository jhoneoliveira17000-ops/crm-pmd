<?php
// api/lead_meta.php
require_once '../src/auth.php';
require_once '../src/db.php';
require_once '../src/utils.php';

require_login();

$leadId = $_GET['id'] ?? 0;

if (!$leadId) {
    json_response(['error' => 'ID required'], 400);
}

try {
    // 1. Check if lead exists
    $stmt = $pdo->prepare("SELECT id, nome, email, telefone FROM leads WHERE id = ?");
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        json_response(['error' => 'Lead not found'], 404);
    }

    // 2. Fetch Facebook Lead Data
    $stmtFB = $pdo->prepare("SELECT * FROM facebook_leads WHERE lead_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmtFB->execute([$leadId]);
    $fbData = $stmtFB->fetch(PDO::FETCH_ASSOC);

    // 3. Parse JSON payload if exists
    $metaData = [];
    if ($fbData && !empty($fbData['payload_json'])) {
        $json = json_decode($fbData['payload_json'], true);
        if ($json) {
            // Extract useful fields from typical Meta payload
            // Structure varies, but usually: field_data => [{name: 'email', values: ['...']}, ...]
            // And: camapign_name, ad_name, etc.
            
            $metaData = [
                'form_id' => $fbData['form_id'],
                'campaign_name' => $json['campaign_name'] ?? 'N/A',
                'adset_name' => $json['adset_name'] ?? 'N/A',
                'ad_name' => $json['ad_name'] ?? 'N/A',
                'platform' => $json['platform'] ?? 'fb',
                'created_time' => $json['created_time'] ?? $fbData['created_at'],
                'extra_data' => []
            ];

            // Extract custom questions
            if (isset($json['field_data']) && is_array($json['field_data'])) {
                foreach ($json['field_data'] as $field) {
                    // Skip standard fields if we already show them (email, full_name, phone_number)
                    // But maybe show all for completeness in "Meta Data" tab
                    $metaData['extra_data'][] = [
                        'label' => $field['name'],
                        'value' => implode(', ', $field['values'])
                    ];
                }
            }
        }
    }

    json_response([
        'success' => true,
        'lead' => $lead,
        'meta' => $metaData
    ]);

} catch (Exception $e) {
    error_log("Lead Meta API Error: " . $e->getMessage());
    json_response(['error' => 'Server error'], 500);
}
?>
