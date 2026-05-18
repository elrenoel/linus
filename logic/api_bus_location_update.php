<?php
require_once __DIR__ . '/bus_service.php';

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$payload = [];
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

if (empty($payload)) {
    $payload = $_POST;
}

$busId = isset($payload['bus_id']) ? (int) $payload['bus_id'] : 0;
$lat = isset($payload['lat']) ? (float) $payload['lat'] : null;
$lng = isset($payload['lng']) ? (float) $payload['lng'] : null;
$speed = isset($payload['speed_kmh']) ? (float) $payload['speed_kmh'] : null;
$heading = isset($payload['heading_deg']) ? (int) $payload['heading_deg'] : null;
$accuracy = isset($payload['accuracy_m']) ? (float) $payload['accuracy_m'] : null;

if ($busId <= 0 || $lat === null || $lng === null) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'bus_id, lat, lng wajib diisi.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'error' => 'Koordinat tidak valid.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$conn = bus_get_conn();

$busCheck = mysqli_prepare($conn, 'SELECT id_bus FROM bus WHERE id_bus = ? LIMIT 1');
if (!$busCheck) {
    error_log('Bus check prepare failed: ' . mysqli_error($conn));
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Gagal memvalidasi bus.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

mysqli_stmt_bind_param($busCheck, 'i', $busId);
mysqli_stmt_execute($busCheck);
$busResult = mysqli_stmt_get_result($busCheck);
$busRow = $busResult ? mysqli_fetch_assoc($busResult) : null;
mysqli_stmt_close($busCheck);

if (!$busRow) {
    http_response_code(404);
    echo json_encode([
        'ok' => false,
        'error' => 'Bus tidak ditemukan.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

mysqli_begin_transaction($conn);
try {
    $sqlLatest = '
        INSERT INTO bus_location (id_bus, lat, lng, speed_kmh, heading_deg, accuracy_m, recorded_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            lat = VALUES(lat),
            lng = VALUES(lng),
            speed_kmh = VALUES(speed_kmh),
            heading_deg = VALUES(heading_deg),
            accuracy_m = VALUES(accuracy_m),
            recorded_at = VALUES(recorded_at)
    ';
    $stmtLatest = mysqli_prepare($conn, $sqlLatest);
    if (!$stmtLatest) {
        throw new RuntimeException('Gagal menyiapkan update lokasi terbaru.');
    }

    mysqli_stmt_bind_param(
        $stmtLatest,
        'idddid',
        $busId,
        $lat,
        $lng,
        $speed,
        $heading,
        $accuracy,
    );

    if (!mysqli_stmt_execute($stmtLatest)) {
        mysqli_stmt_close($stmtLatest);
        throw new RuntimeException('Gagal menyimpan lokasi terbaru.');
    }
    mysqli_stmt_close($stmtLatest);

    $sqlLog = '
        INSERT INTO bus_location_log (id_bus, lat, lng, speed_kmh, heading_deg, accuracy_m, recorded_at, source)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)
    ';
    $stmtLog = mysqli_prepare($conn, $sqlLog);
    if (!$stmtLog) {
        throw new RuntimeException('Gagal menyiapkan log lokasi.');
    }

    $source = isset($payload['source']) ? (string) $payload['source'] : 'gps';
    if (!in_array($source, ['gps', 'manual', 'sim'], true)) {
        $source = 'gps';
    }

    mysqli_stmt_bind_param(
        $stmtLog,
        'idddids',
        $busId,
        $lat,
        $lng,
        $speed,
        $heading,
        $accuracy,
        $source,
    );

    if (!mysqli_stmt_execute($stmtLog)) {
        mysqli_stmt_close($stmtLog);
        throw new RuntimeException('Gagal menyimpan log lokasi.');
    }
    mysqli_stmt_close($stmtLog);

    mysqli_commit($conn);

    echo json_encode([
        'ok' => true,
        'bus_id' => $busId,
        'recorded_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
    mysqli_rollback($conn);
    error_log('Bus location update failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Gagal menyimpan lokasi bus.',
    ], JSON_UNESCAPED_UNICODE);
}
