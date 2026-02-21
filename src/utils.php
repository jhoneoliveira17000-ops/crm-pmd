<?php
// PMDCRM/src/utils.php

// Ensure errors don't leak into JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function json_response($data, $status = 200) {
    if (ob_get_length()) ob_clean(); // Clear any unwanted output before JSON
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
