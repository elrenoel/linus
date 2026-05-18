<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

unset($_SESSION['driver_logged_in'], $_SESSION['driver_id'], $_SESSION['driver_name']);

header('Location: ../driver-login');
exit;
