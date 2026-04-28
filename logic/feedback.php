<?php
require_once __DIR__ . '/../config/db.php';

if (!function_exists('feedback_fetch_reviews')) {
    function feedback_fetch_reviews(mysqli $conn, string $ratingFilter, string $sort): array
    {
        $normalizedSort = $sort;
        if (!in_array($normalizedSort, ['latest', 'highest', 'lowest'], true)) {
            $normalizedSort = 'latest';
        }

        $orderBy = 'date DESC';
        if ($normalizedSort === 'highest') {
            $orderBy = 'rating DESC, date DESC';
        } elseif ($normalizedSort === 'lowest') {
            $orderBy = 'rating ASC, date DESC';
        }

        $ratingValue = null;
        if ($ratingFilter !== 'all') {
            $parsedRating = (int) $ratingFilter;
            if ($parsedRating >= 1 && $parsedRating <= 5) {
                $ratingValue = $parsedRating;
            }
        }

        $sql = 'SELECT id_feedback, username, comment, rating, date, bus_label, route_label FROM feedback';
        if ($ratingValue !== null) {
            $sql .= ' WHERE rating = ?';
        }
        $sql .= ' ORDER BY ' . $orderBy;

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('Feedback list prepare failed: ' . mysqli_error($conn));
            return [];
        }

        if ($ratingValue !== null) {
            mysqli_stmt_bind_param($stmt, 'i', $ratingValue);
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Feedback list execute failed: ' . mysqli_error($conn));
            mysqli_stmt_close($stmt);
            return [];
        }

        $result = mysqli_stmt_get_result($stmt);
        $reviews = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reviews[] = $row;
            }
        }
        mysqli_stmt_close($stmt);

        return $reviews;
    }
}

if (!function_exists('feedback_insert_review')) {
    function feedback_insert_review(mysqli $conn, array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));
        $rating = (int) ($input['rating'] ?? 5);
        $rating = max(1, min(5, $rating));
        $comment = trim((string) ($input['comment'] ?? ''));
        $busLabel = trim((string) ($input['bus_label'] ?? ''));
        $routeLabel = trim((string) ($input['route_label'] ?? ''));

        if ($username === '') {
            return [
                'ok' => false,
                'error' => 'Nama penumpang wajib diisi sebelum mengirim review.',
            ];
        }

        $sql = 'INSERT INTO feedback (username, rating, comment, bus_label, route_label) VALUES (?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('Feedback insert prepare failed: ' . mysqli_error($conn));
            return [
                'ok' => false,
                'error' => 'Gagal menyiapkan penyimpanan review. Silakan coba lagi.',
            ];
        }

        mysqli_stmt_bind_param($stmt, 'sisss', $username, $rating, $comment, $busLabel, $routeLabel);
        $ok = mysqli_stmt_execute($stmt);
        if (!$ok) {
            error_log('Feedback insert failed: ' . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        return [
            'ok' => $ok,
            'error' => $ok ? '' : 'Gagal menyimpan review. Silakan coba lagi.',
        ];
    }
}
