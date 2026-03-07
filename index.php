<?php
// PMDCRM Front Controller (index.php)
// Required for Cloud hosting (Nginx/Railway) fallback routing

// 1. Get the requested path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Remove leading/trailing slashes and sanitize
$route = trim($requestUri, '/');

// 3. Define the actual PHP file mapping
$routes = [
    '' => 'landing.php',
    'login' => 'login_page.php',
    'register' => 'register.php',
    'dashboard' => 'dashboard.php',
    'agenda' => 'agenda.php',
    'clientes' => 'clientes.php',
    'cliente_dashboard' => 'cliente_dashboard.php',
    'crm_kanban' => 'crm_kanban.php',
    'financeiro' => 'financeiro.php',
    'configuracoes' => 'configuracoes.php',
    'usuarios' => 'usuarios.php',
    'perfil' => 'perfil.php',
    'admin_dashboard' => 'admin_dashboard.php',
    'termos_servico' => 'termos_servico.php',
    'politica_privacidade' => 'politica_privacidade.php',
    'logout' => 'src/logout.php'
];

// 4. Check if route exists in our mapping
if (array_key_exists($route, $routes)) {
    $fileToInclude = $routes[$route];
    
    // Inject proper server variables so internal logic works
    $_SERVER['SCRIPT_NAME'] = '/' . $fileToInclude;
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/' . $fileToInclude;
    
    require_once __DIR__ . '/' . $fileToInclude;
    exit;
}

// 5. Fallback for physical files (like API or JS)
if (file_exists(__DIR__ . '/' . $route) && !is_dir(__DIR__ . '/' . $route)) {
    return false; // Let the web server handle it directly
}

// 6. 404 Not Found
http_response_code(404);
echo "404 - Página não encontrada.";
exit;
?>
