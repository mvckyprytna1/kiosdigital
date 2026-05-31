<?php
/**
 * Database Configuration
 * KiosDigital PPOB - PHP Native
 */

$host = "localhost";
$user = "DB_USER_HERE"; // Sesuaikan dengan hosting
$pass = "DB_PASS_HERE"; // Sesuaikan dengan hosting
$db   = "DB_NAME_HERE"; // Sesuaikan dengan hosting

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global App Config
$app_url = "https://yourdomain.com"; // Sesuaikan dengan domain
date_default_timezone_set('Asia/Jakarta');
?>
