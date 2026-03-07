<?php
// PMDCRM/api/upload_image.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método inválido'], 405);
}

if (!isset($_FILES['file'])) {
    json_response(['error' => 'Nenhum arquivo enviado'], 400);
}

$file = $_FILES['file'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Validações
if (!in_array($file['type'], $allowedTypes)) {
    json_response(['error' => 'Tipo de arquivo não permitido. Apenas JPG, PNG, GIF e WebP.'], 400);
}

if ($file['size'] > 5 * 1024 * 1024) { // 5MB
    json_response(['error' => 'Arquivo muito grande. Máximo de 5MB.'], 400);
}

// Converte para base64 data URI (persiste no banco, compatível com Docker/containers)
$imageData = file_get_contents($file['tmp_name']);
$base64 = base64_encode($imageData);
$dataUri = 'data:' . $file['type'] . ';base64,' . $base64;

json_response(['url' => $dataUri]);
?>
