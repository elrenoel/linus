<?php

$page_content = [
    'dashboard' => 'components/map.php',
    'info_bus' => 'components/info_bus.php',
    'feedback' => 'components/feedback.php'
];

$currentPage = $_GET['page'] ?? 'dashboard';

if (!array_key_exists($currentPage, $page_content)) {
    $currentPage = 'dashboard';
}

$currentContent = $page_content[$currentPage];
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
    <?php include 'components/navbar.php'; ?>


    <div class="flex flex-1 min-h-0">
        <?php include 'components/sidebar.php'; ?>

        <div class="h-full flex-1 w-full overflow-auto">
            <?php include $currentContent; ?>
        </div>
    </div>
</body>

</html>