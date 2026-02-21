<?php
// PMDCRM/api/index.php
// Prevent directory listing
header('Content-Type: application/json');
http_response_code(403);
echo json_encode(['error' => 'Forbidden']);
exit;
?>
