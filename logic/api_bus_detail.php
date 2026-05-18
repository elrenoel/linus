<?php
require_once __DIR__ . '/bus_service.php';

header('Content-Type: application/json; charset=utf-8');

$busId = isset($_GET['bus_id']) ? (int) $_GET['bus_id'] : 0;
if ($busId <= 0) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'bus_id wajib diisi.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$bus = bus_fetch_by_id(bus_get_conn(), $busId);
if (!$bus) {
    http_response_code(404);
    echo json_encode([
        'ok' => false,
        'error' => 'Bus tidak ditemukan.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok' => true,
    'bus' => $bus,
], JSON_UNESCAPED_UNICODE);
