<?php
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);
session_start();
require_once 'db_connect.php';
require_once 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: login_form.php?status=login_failed");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role, phone_number FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['phone_number'] = $user['phone_number'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php"); // Anda perlu membuat halaman ini
            } else {
                header("Location: index.php"); // Arahkan ke dashboard user
            }
            exit();
        } else {
            // Login gagal
            header("Location: login_form.php?status=login_failed");
            exit();
        }
    } catch (PDOException $e) {
        die("Error saat login: " . $e->getMessage());
    }
}
?>