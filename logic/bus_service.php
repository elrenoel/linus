<?php
require_once __DIR__ . '/../config/db.php';

if (!function_exists('bus_compute_status')) {
    function bus_compute_status(?string $recordedAt): array
    {
        if (!$recordedAt) {
            return [
                'status_key' => 'tidak_aktif',
                'status_label' => 'Tidak Aktif',
                'last_seen_sec' => null,
            ];
        }

        $now = new DateTimeImmutable('now');
        $recorded = new DateTimeImmutable($recordedAt);
        $diffSeconds = abs($now->getTimestamp() - $recorded->getTimestamp());

        if ($diffSeconds <= 60) {
            return [
                'status_key' => 'sedang_berjalan',
                'status_label' => 'Sedang Berjalan',
                'last_seen_sec' => $diffSeconds,
            ];
        }

        if ($diffSeconds <= 300) {
            return [
                'status_key' => 'menuju_halte',
                'status_label' => 'Menuju Halte',
                'last_seen_sec' => $diffSeconds,
            ];
        }

        return [
            'status_key' => 'sedang_berhenti',
            'status_label' => 'Sedang Berhenti',
            'last_seen_sec' => $diffSeconds,
        ];
    }
}

if (!function_exists('bus_get_conn')) {
    function bus_get_conn(): mysqli
    {
        $conn = $GLOBALS['conn'] ?? null;
        if (!$conn instanceof mysqli) {
            throw new RuntimeException('Database connection is not available.');
        }
        return $conn;
    }
}

if (!function_exists('bus_fetch_all')) {
    function bus_fetch_all(mysqli $conn): array
    {
        $sql = '
            SELECT
                b.id_bus,
                b.nama_bus,
                NULL AS plat,
                s.nama_supir,
                o.lokasi AS tujuan,
                bl.lat,
                bl.lng,
                bl.recorded_at
            FROM bus b
            LEFT JOIN (
                SELECT o1.*
                FROM operasional o1
                INNER JOIN (
                    SELECT id_bus, MAX(id_operasional) AS max_id
                    FROM operasional
                    GROUP BY id_bus
                ) o2 ON o1.id_bus = o2.id_bus AND o1.id_operasional = o2.max_id
            ) o ON o.id_bus = b.id_bus
            LEFT JOIN supir s ON s.id_supir = o.id_supir
            LEFT JOIN bus_location bl ON bl.id_bus = b.id_bus
            ORDER BY b.id_bus ASC
        ';

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            error_log('Bus list query failed: ' . mysqli_error($conn));
            return [];
        }

        $buses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $status = bus_compute_status($row['recorded_at'] ?? null);
            $lat = $row['lat'] !== null ? (float) $row['lat'] : null;
            $lng = $row['lng'] !== null ? (float) $row['lng'] : null;

            $buses[] = [
                'id' => (int) $row['id_bus'],
                'label' => $row['nama_bus'] ?: 'Bus Linus',
                'plat' => $row['plat'] ?? '',
                'supir' => $row['nama_supir'] ?: 'Belum ditentukan',
                'tujuan' => $row['tujuan'] ?: '-',
                'status_key' => $status['status_key'],
                'status_label' => $status['status_label'],
                'last_seen_sec' => $status['last_seen_sec'],
                'location' => ($lat !== null && $lng !== null) ? [
                    'lat' => $lat,
                    'lng' => $lng,
                ] : null,
                'recorded_at' => $row['recorded_at'],
            ];
        }

        mysqli_free_result($result);
        return $buses;
    }
}

if (!function_exists('bus_fetch_by_id')) {
    function bus_fetch_by_id(mysqli $conn, int $busId): ?array
    {
        $sql = '
            SELECT
                b.id_bus,
                b.nama_bus,
                NULL AS plat,
                s.nama_supir,
                o.lokasi AS tujuan,
                bl.lat,
                bl.lng,
                bl.recorded_at
            FROM bus b
            LEFT JOIN (
                SELECT o1.*
                FROM operasional o1
                INNER JOIN (
                    SELECT id_bus, MAX(id_operasional) AS max_id
                    FROM operasional
                    GROUP BY id_bus
                ) o2 ON o1.id_bus = o2.id_bus AND o1.id_operasional = o2.max_id
            ) o ON o.id_bus = b.id_bus
            LEFT JOIN supir s ON s.id_supir = o.id_supir
            LEFT JOIN bus_location bl ON bl.id_bus = b.id_bus
            WHERE b.id_bus = ?
            LIMIT 1
        ';

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('Bus detail prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $busId);
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Bus detail execute failed: ' . mysqli_error($conn));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$row) {
            return null;
        }

        $status = bus_compute_status($row['recorded_at'] ?? null);
        $lat = $row['lat'] !== null ? (float) $row['lat'] : null;
        $lng = $row['lng'] !== null ? (float) $row['lng'] : null;

        return [
            'id' => (int) $row['id_bus'],
            'label' => $row['nama_bus'] ?: 'Bus Linus',
            'plat' => $row['plat'] ?? '',
            'supir' => $row['nama_supir'] ?: 'Belum ditentukan',
            'tujuan' => $row['tujuan'] ?: '-',
            'status_key' => $status['status_key'],
            'status_label' => $status['status_label'],
            'last_seen_sec' => $status['last_seen_sec'],
            'location' => ($lat !== null && $lng !== null) ? [
                'lat' => $lat,
                'lng' => $lng,
            ] : null,
            'recorded_at' => $row['recorded_at'],
        ];
    }
}
