<?php
// Ganti dengan detail database Anda
$db_host = 'localhost';
$db_name = 'hoky4323_hokage_ppob';
$db_user = 'hoky4323_hokage_pulsa';
$db_pass = 'Gxy3&+a3-Y4+{4&|';

try {
    // Menggunakan PDO untuk koneksi yang lebih aman
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jangan tampilkan error detail di production
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>