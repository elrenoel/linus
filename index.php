<?php
session_start();

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if (!function_exists('app_url')) {
    function app_url(string $path): string
    {
        $basePath = $GLOBALS['basePath'] ?? '';
        return $basePath . $path;
    }
}

function redirect_to(string $path): void
{
    header('Location: ' . app_url($path));
    exit;
}

$pageContent = [
    'dashboard' => 'components/map.php',
    'info_bus' => 'components/info_bus.php',
    'feedback' => 'components/feedback.php'
];

$routeMap = [
    '/' => 'root',
    '/login' => 'login',
    '/register' => 'register',
    '/dashboard' => 'dashboard',
    '/bus-info' => 'info_bus',
    '/feedback' => 'feedback',
    '/logout' => 'logout'
];

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = '/' . trim($path, '/');
if ($path === '//') {
    $path = '/';
}

$routeKey = $routeMap[$path] ?? null;
$isLoggedIn = !empty($_SESSION['is_logged_in']);

if ($routeKey === 'logout') {
    require __DIR__ . '/logic/auth_logout.php';
    exit;
}

if ($routeKey === 'root' || $routeKey === null) {
    redirect_to($isLoggedIn ? '/dashboard' : '/login');
}

if (!$isLoggedIn) {
    if ($routeKey !== 'login' && $routeKey !== 'register') {
        redirect_to('/login');
    }
} else {
    if ($routeKey === 'login' || $routeKey === 'register') {
        redirect_to('/dashboard');
    }
}

$authViews = [
    'login' => 'pages/login.php',
    'register' => 'pages/register.php'
];
$authView = $routeKey === 'register' ? 'register' : 'login';

$currentPage = $routeKey ?? 'dashboard';
?>


<!DOCTYPE html>
<html lang="id" class="h-screen">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linus Express - Universitas Sumatera Utara</title>
    <link rel="stylesheet" href="src/output.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');

        body {
            font-family: 'DM Sans', sans-serif;
        }
    </style>
</head>

<body class="h-full flex flex-col overflow-hidden">
    <?php if ($isLoggedIn) : ?>
        <?php include 'components/navbar.php'; ?>

        <div class="flex flex-1 min-h-0">
            <?php include 'components/sidebar.php'; ?>

            <div class="h-full flex-1 w-full overflow-auto">
                <?php include $pageContent[$currentPage]; ?>
            </div>
        </div>
    <?php else : ?>
        <?php include $authViews[$authView]; ?>
    <?php endif; ?>
</body>

</html>