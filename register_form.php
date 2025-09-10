<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Hokage PPOB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">

    <div class="login-container">
        
        <h2>Buat Akun Baru</h2>
        <p style="margin-top:-20px; margin-bottom:20px; color:#6c757d;">Selamat datang! Silakan isi data diri Anda.</p>

        <?php
        if (isset($_GET['error'])) {
            $error_msg = '';
            switch ($_GET['error']) {
                case 'empty': $error_msg = "Semua kolom wajib diisi."; break;
                case 'invalid_email': $error_msg = "Format email tidak valid."; break;
                case 'invalid_phone': $error_msg = "Format nomor telepon tidak valid."; break;
                case 'username_exists': $error_msg = "Username sudah digunakan."; break;
                case 'email_exists': $error_msg = "Email sudah terdaftar."; break;
                case 'phone_exists': $error_msg = "Nomor telepon sudah digunakan."; break;
                default: $error_msg = "Terjadi kesalahan. Silakan coba lagi.";
            }
            // Menambahkan style untuk pesan error agar terlihat jelas
            echo '<div class="message error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px;">' . htmlspecialchars($error_msg) . '</div>';
        }
        ?>

        <form action="register_handler.php" method="POST" id="registerForm">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="input-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="input-group">
                <label for="phone_number">Nomor Telepon</label>
                <input type="tel" id="phone_number" name="phone_number" placeholder="Contoh: 08123456789" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Buat password Anda" required>
            </div>
            <button type="submit">Daftar</button>
        </form>
        
        <div class="extra-links">
            Sudah punya akun? <a href="login_form.php">Login di sini</a>
        </div>

    </div>
</body>
</html>