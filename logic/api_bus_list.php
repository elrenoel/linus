<?php
require_once __DIR__ . '/bus_service.php';

header('Content-Type: application/json; charset=utf-8');

$buses = bus_fetch_all(bus_get_conn());

echo json_encode([
    'ok' => true,
    'buses' => $buses,
], JSON_UNESCAPED_UNICODE);
