<?php
require_once 'auth_check.php'; // Pastikan user (reseller) login

// Hapus hanya token API XL dari sesi, bukan sesi login utama
unset($_SESSION['xl_api_tokens']);
unset($_SESSION['customer_phone_number']);

// Kembalikan ke halaman myxl, yang akan menampilkan form input nomor lagi
header("Location: myxl.php");
exit();
?>