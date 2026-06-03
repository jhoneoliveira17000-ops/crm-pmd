<?php
// PMDCRM/src/helpers.php

// Ensure session is started using the custom database handler
require_once __DIR__ . '/session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escapes HTML output in PHP templates.
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generates or retrieves the current CSRF token for the session.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates a given CSRF token against the session token.
 */
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Outputs a hidden input field with the CSRF token.
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" id="csrf_token_field" value="' . generate_csrf_token() . '">';
}

/**
 * Verifies CSRF token for mutating requests (POST, PUT, DELETE).
 * Returns JSON error for API requests and dies for form requests.
 */
function verify_csrf_or_exit() {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        // Exclude webhook API since it uses token authentication, not session auth
        if (strpos($_SERVER['REQUEST_URI'] ?? '', 'api/webhook.php') !== false) {
            return;
        }

        $token = '';
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } elseif (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        } else {
            // Check in JSON input if request has content type JSON
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['csrf_token'])) {
                    $token = $input['csrf_token'];
                }
            }
        }

        if (!validate_csrf_token($token)) {
            $isApi = (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) || 
                     (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            
            if ($isApi) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['error' => 'Acesso inválido: Token CSRF inválido ou ausente.']);
                exit;
            } else {
                http_response_code(403);
                die('Acesso inválido: Token CSRF inválido ou ausente.');
            }
        }
    }
}

// Automatically enforce CSRF verification globally for POST, PUT, DELETE requests
verify_csrf_or_exit();
