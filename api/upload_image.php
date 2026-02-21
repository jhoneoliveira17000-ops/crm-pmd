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
$uploadDir = __DIR__ . '/../assets/uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Validações
if (!in_array($file['type'], $allowedTypes)) {
    json_response(['error' => 'Tipo de arquivo não permitido. Apenas JPG, PNG, GIF e WebP.'], 400);
}

if ($file['size'] > 5 * 1024 * 1024) { // 5MB
    json_response(['error' => 'Arquivo muito grande. Máximo de 5MB.'], 400);
}

// Garante que o diretório existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gera nome único
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_') . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $publicUrl = 'assets/uploads/' . $filename; // URL relativa para salvar no banco
    json_response(['url' => $publicUrl]);
} else {
    json_response(['error' => 'Falha ao salvar o arquivo'], 500);
}
?>
