<?php
require_once 'auth_check.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];

    // --- PROSES UPLOAD FOTO PROFIL ---
    $new_photo_filename = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $photo = $_FILES['profile_picture'];
        $upload_dir = 'uploads/avatars/';
        
        // Validasi file (ukuran, tipe)
        $max_size = 2 * 1024 * 1024; // 2 MB
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if ($photo['size'] > $max_size) {
            header("Location: pengaturan.php?error=Ukuran file terlalu besar (maks 2MB)."); exit();
        }
        if (!in_array($photo['type'], $allowed_types)) {
            header("Location: pengaturan.php?error=Tipe file tidak valid (hanya JPG, PNG, GIF)."); exit();
        }

        // Buat nama file yang unik untuk menghindari duplikat/overwrite
        $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
        $new_photo_filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
        $destination = $upload_dir . $new_photo_filename;

        // Ambil nama foto lama untuk dihapus nanti
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $old_photo = $stmt->fetchColumn();

        // Pindahkan file yang diupload
        if (move_uploaded_file($photo['tmp_name'], $destination)) {
            // Hapus foto lama jika ada dan bukan default avatar
            if ($old_photo && file_exists($upload_dir . $old_photo)) {
                unlink($upload_dir . $old_photo);
            }
        } else {
            header("Location: pengaturan.php?error=Gagal mengupload foto."); exit();
        }
    }

    // --- PROSES UPDATE DATA TEKS ---
    try {
        // Cek duplikasi email & nomor hp (kecuali untuk user saat ini)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR phone_number = ?) AND id != ?");
        $stmt->execute([$email, $phone_number, $user_id]);
        if ($stmt->fetch()) {
            header("Location: pengaturan.php?error=Email atau No. HP sudah digunakan akun lain."); exit();
        }

        // Bangun query SQL secara dinamis
        $sql_parts = ["email = ?", "phone_number = ?"];
        $params = [$email, $phone_number];

        if ($new_photo_filename) {
            $sql_parts[] = "profile_picture = ?";
            $params[] = $new_photo_filename;
        }
        if (!empty($password)) {
            $sql_parts[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
        $params[] = $user_id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Perbarui nomor telepon di sesi jika berubah
        $_SESSION['phone_number'] = $phone_number;

        header("Location: pengaturan.php?status=success");
        exit();

    } catch (PDOException $e) {
        header("Location: pengaturan.php?error=Terjadi kesalahan database.");
        exit();
    }
}
?>