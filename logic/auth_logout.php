<?php
session_start();

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$GLOBALS['basePath'] = $basePath;
if (!function_exists('app_url')) {
    function app_url(string $path): string
    {
        $basePath = $GLOBALS['basePath'] ?? '';
        return $basePath . $path;
    }
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
header('Location: ' . app_url('/login'));
exit;
