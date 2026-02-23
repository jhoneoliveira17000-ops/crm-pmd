<?php
// router.php - Used for Railway/PHP Built-in Server
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);

// Pass through static assets
if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf'])) {
    return false;
}

// Map root index specifically
if ($path === '/' || $path === '') {
    include 'index.php';
    return true;
}

// Map /login directly to index.php
if ($path === '/login') {
    include 'index.php';
    return true;
}

// Dynamic route matching for extensionless pages
$file = ltrim($path, '/');
if (file_exists($file . '.php')) {
    include $file . '.php';
    return true;
}

// Fallback logic
if (file_exists($file)) {
    return false;
} else {
    // 404 handling or redirect
    http_response_code(404);
    echo "404 Not Found";
    return true;
}
?>
