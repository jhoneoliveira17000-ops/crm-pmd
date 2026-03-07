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

if ($file['size'] > 10 * 1024 * 1024) { // 10MB
    json_response(['error' => 'Arquivo muito grande. Máximo de 10MB.'], 400);
}

// Comprime imagem com GD para caber no limite de 6MB do TiDB
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$src = null;

switch ($file['type']) {
    case 'image/png': $src = @imagecreatefrompng($file['tmp_name']); break;
    case 'image/jpeg': $src = @imagecreatefromjpeg($file['tmp_name']); break;
    case 'image/webp': $src = @imagecreatefromwebp($file['tmp_name']); break;
    case 'image/gif': $src = @imagecreatefromgif($file['tmp_name']); break;
}

if ($src) {
    // Redimensiona se muito grande (max 400px para foto de perfil)
    $origW = imagesx($src);
    $origH = imagesy($src);
    $maxW = 400;
    
    if ($origW > $maxW) {
        $newW = $maxW;
        $newH = intval($origH * ($maxW / $origW));
        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);
        $src = $resized;
    }
    
    // Salva como JPEG comprimido (qualidade 80)
    ob_start();
    imagejpeg($src, null, 80);
    $compressedData = ob_get_clean();
    imagedestroy($src);
    
    $base64 = base64_encode($compressedData);
    $dataUri = 'data:image/jpeg;base64,' . $base64;
} else {
    // Fallback: sem compressão
    $imageData = file_get_contents($file['tmp_name']);
    $base64 = base64_encode($imageData);
    $dataUri = 'data:' . $file['type'] . ';base64,' . $base64;
}

json_response(['url' => $dataUri]);
?>
