<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login DAN rolenya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, tendang ke halaman login atau halaman utama
    header("Location: index.php");
    exit();
}
?>