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

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username == '' || $email == '' || $password == '') {
    header('Location: ' . app_url('/register?err=empty'));
    exit;
}

$checkSql = 'SELECT id_user FROM user WHERE email = ? OR username = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);
if (!$checkStmt) {
    error_log('Register prepare failed: ' . mysqli_error($conn));
    header('Location: ' . app_url('/register?err=server'));
    exit;
}

mysqli_stmt_bind_param($checkStmt, 'ss', $email, $username);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$exists = $checkResult ? mysqli_fetch_assoc($checkResult) : null;
mysqli_stmt_close($checkStmt);

if ($exists) {
    header('Location: ' . app_url('/register?err=exists'));
    exit;
}

$insertSql = 'INSERT INTO user (username, email, password) VALUES (?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);
if (!$insertStmt) {
    error_log('Register insert prepare failed: ' . mysqli_error($conn));
    header('Location: ' . app_url('/register?err=server'));
    exit;
}

mysqli_stmt_bind_param($insertStmt, 'sss', $username, $email, $password);
$insertOk = mysqli_stmt_execute($insertStmt);
$newId = mysqli_insert_id($conn);
mysqli_stmt_close($insertStmt);

if (!$insertOk) {
    error_log('Register insert failed: ' . mysqli_error($conn));
    header('Location: ' . app_url('/register?err=server'));
    exit;
}

$_SESSION['is_logged_in'] = true;
$_SESSION['user_id'] = $newId;
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;

header('Location: ' . app_url('/dashboard'));
exit;
