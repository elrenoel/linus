<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$GLOBALS['basePath'] = $basePath;
if (!function_exists('app_url')) {
    function app_url(string $path): string
    {
        $basePath = $GLOBALS['basePath'] ?? '';
        return $basePath . $path;
    }
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email == '' || $password == '') {
    header('Location: ' . app_url('/login?err=empty'));
    exit;
}

$sql = 'SELECT id_user, username, email, password FROM user WHERE email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log('Login prepare failed: ' . mysqli_error($conn));
    header('Location: ' . app_url('/login?err=server'));
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$user || $user['password'] !== $password) {
    header('Location: ' . app_url('/login?err=invalid'));
    exit;
}

$_SESSION['is_logged_in'] = true;
$_SESSION['user_id'] = $user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];

header('Location: ' . app_url('/dashboard'));
exit;
