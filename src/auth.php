<?php
// PMDCRM/src/auth.php
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Não autorizado']);
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'Acesso negado. Apenas administradores.']);
        exit;
    }
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}


function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
