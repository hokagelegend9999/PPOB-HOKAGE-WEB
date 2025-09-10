<?php
require_once 'admin_auth_check.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($email) || empty($phone_number) || empty($password)) {
        header("Location: admin_dashboard.php?error=Semua kolom wajib diisi.");
        exit();
    }

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $phone_number, $hashed_password]);
        header("Location: admin_dashboard.php?message=User baru berhasil ditambahkan.");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Kode error untuk duplicate entry
            header("Location: admin_dashboard.php?error=Username atau email sudah ada yang menggunakan.");
        } else {
            header("Location: admin_dashboard.php?error=Gagal menambahkan user.");
        }
    }
    exit();
}