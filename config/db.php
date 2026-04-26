<?php

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'linus';
$dbPort = 3307;

$conn = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if (!$conn) {
	error_log('Database connection failed: ' . mysqli_connect_error());
	http_response_code(500);
	exit('Terjadi gangguan koneksi database. Silakan coba lagi.');
}

if (!mysqli_set_charset($conn, 'utf8mb4')) {
	error_log('Failed to set database charset: ' . mysqli_error($conn));
}

