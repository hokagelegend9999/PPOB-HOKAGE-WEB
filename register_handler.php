<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($email) || empty($phone_number) || empty($password)) {
        header("Location: register_form.php?error=empty");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register_form.php?error=invalid_email");
        exit();
    }
    if (!preg_match('/^[0-9]+$/', $phone_number)) {
        header("Location: register_form.php?error=invalid_phone");
        exit();
    }

    try {
        // Cek apakah username, email, atau nomor telepon sudah ada
        $stmt = $pdo->prepare("SELECT username, email, phone_number FROM users WHERE username = ? OR email = ? OR phone_number = ?");
        $stmt->execute([$username, $email, $phone_number]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            if ($existingUser['username'] == $username) {
                header("Location: register_form.php?error=username_exists");
            } elseif ($existingUser['email'] == $email) {
                header("Location: register_form.php?error=email_exists");
            } elseif ($existingUser['phone_number'] == $phone_number) {
                header("Location: register_form.php?error=phone_exists");
            }
            exit();
        }

        // Jika semua unik, lanjutkan registrasi
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $phone_number, $hashed_password]);

        header("Location: login_form.php?status=reg_success");
        exit();

    } catch (PDOException $e) {
        header("Location: register_form.php?error=db_error");
        exit();
    }
}
?>