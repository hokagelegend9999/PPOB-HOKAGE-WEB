<?php
require_once 'admin_auth_check.php'; // Pastikan hanya admin yang bisa akses
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id_to_topup = $_POST['user_id'];
    $amount = (int)$_POST['amount'];
    $admin_username = $_SESSION['username']; // Untuk catatan

    // Validasi sederhana
    if (empty($user_id_to_topup) || empty($amount) || $amount <= 0) {
        header("Location: admin_dashboard.php?error=Data tidak valid.");
        exit();
    }

    // Gunakan database transaction untuk memastikan integritas data
    // Jika salah satu query gagal, semua akan dibatalkan (rollback)
    $pdo->beginTransaction();

    try {
        // 1. Tambah saldo ke tabel users
        $stmt_update_saldo = $pdo->prepare(
            "UPDATE users SET saldo = saldo + ? WHERE id = ?"
        );
        $stmt_update_saldo->execute([$amount, $user_id_to_topup]);

        // 2. Catat transaksi sebagai 'completed'
        $transaction_id = 'ADMINTOPUP-' . $user_id_to_topup . '-' . time();
        $description = "Top up manual oleh admin ($admin_username)";
        
        $stmt_insert_trx = $pdo->prepare(
            "INSERT INTO transactions (user_id, transaction_id, type, description, amount, status) 
             VALUES (?, ?, 'topup', ?, ?, 'completed')"
        );
        $stmt_insert_trx->execute([$user_id_to_topup, $transaction_id, $description, $amount]);

        // Jika semua berhasil, simpan perubahan
        $pdo->commit();

        header("Location: admin_dashboard.php?message=Saldo berhasil ditambahkan.");

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        $pdo->rollBack();
        header("Location: admin_dashboard.php?error=Gagal menambahkan saldo: " . $e->getMessage());
    }
    exit();
}