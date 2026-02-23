<?php
// router.php - Used for Railway/PHP Built-in Server
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);

// Pass through static assets
if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf'])) {
    return false;
}

// Security: don't route to hidden files
if (strpos($path, '/.') === 0) {
    http_response_code(403);
    die('Forbidden');
}

// Base Routing
if ($path === '/' || $path === '') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
    require 'index.php';
    return true;
}

if ($path === '/login') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
    require 'index.php';
    return true;
}

// Dynamic route matching for extensionless pages
$file = ltrim($path, '/');
if (file_exists(__DIR__ . '/' . $file . '.php')) {
    $_SERVER['SCRIPT_NAME'] = '/' . $file . '.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/' . $file . '.php';
    require __DIR__ . '/' . $file . '.php';
    return true;
}

// If physical file actually exists (like API endpoints with explicit .php)
if (file_exists(__DIR__ . '/' . $file) && !is_dir(__DIR__ . '/' . $file)) {
    return false;
}

http_response_code(404);
echo "404 Not Found - Path: " . htmlspecialchars($path);
return true;
?>
