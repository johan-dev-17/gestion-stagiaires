<?php
// Helper API commun
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml_handler.php';

function apiInit() {
    header('Content-Type: application/json; charset=utf-8');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non authentifié']);
        exit;
    }
}

function apiInput() {
    $raw = file_get_contents('php://input');
    if (!$raw) return $_POST;
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function apiSuccess($data = []) {
    echo json_encode(['success' => true] + $data);
    exit;
}

function apiError($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}
