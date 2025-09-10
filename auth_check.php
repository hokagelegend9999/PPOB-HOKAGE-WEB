<?php
// Memulai atau melanjutkan sesi yang sudah ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah variabel sesi 'user_id' ada dan tidak kosong.
// Variabel ini kita buat saat login berhasil di login_handler.php
if (!isset($_SESSION['user_id'])) {
    // Jika tidak ada sesi, paksa redirect ke halaman login
    header("Location: login_form.php");
    exit(); // Pastikan script berhenti dieksekusi setelah redirect
}
?>