<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = trim((string) ($_POST['password'] ?? ''));

if ($username === '' || $password === '') {
    header('Location: ../driver-login?error=1');
    exit;
}

$sql = 'SELECT id_supir, nama_supir, password FROM supir WHERE username = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log('Driver login prepare failed: ' . mysqli_error($conn));
    header('Location: ../driver-login?error=1');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Driver login execute failed: ' . mysqli_error($conn));
    mysqli_stmt_close($stmt);
    header('Location: ../driver-login?error=1');
    exit;
}

$result = mysqli_stmt_get_result($stmt);
$driver = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$driver || !hash_equals((string) $driver['password'], $password)) {
    header('Location: ../driver-login?error=1');
    exit;
}

$_SESSION['driver_logged_in'] = true;
$_SESSION['driver_id'] = (int) $driver['id_supir'];
$_SESSION['driver_name'] = $driver['nama_supir'];

header('Location: ../driver');
exit;
