<?php
require_once 'admin_auth_check.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id_to_delete = $_POST['user_id'];
    $admin_id = $_SESSION['user_id'];

    // Mencegah admin menghapus akunnya sendiri
    if ($user_id_to_delete == $admin_id) {
        header("Location: admin_dashboard.php?error=Anda tidak dapat menghapus akun Anda sendiri.");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id_to_delete]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: admin_dashboard.php?message=User berhasil dihapus.");
        } else {
            header("Location: admin_dashboard.php?error=User tidak ditemukan.");
        }
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?error=Gagal menghapus user.");
    }
    exit();
}