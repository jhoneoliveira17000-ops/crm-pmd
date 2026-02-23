<?php
// PMDCRM/src/auth.php
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }
        header('Location: index.php');
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['user_role'] !== 'admin') {
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado. Apenas administradores.']);
            exit;
        }
        header('Location: dashboard.php');
        exit;
    }
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function get_tenant_condition($tableAlias = '') {
    $prefix = $tableAlias ? $tableAlias . '.' : '';
    if (is_admin()) {
        // Admin vê dados de todos os tenants no dashboard e listagens globais.
        return "1=1";
    }
    $userId = get_current_user_id();
    return "{$prefix}user_id = " . (int)$userId;
}

function get_tenant_id() {
    return get_current_user_id();
}
