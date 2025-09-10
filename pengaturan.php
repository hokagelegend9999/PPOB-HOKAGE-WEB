<?php
require_once 'auth_check.php';
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, phone_number, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Tentukan path avatar, gunakan gambar default jika tidak ada
$avatar_path = 'uploads/avatars/' . ($user['profile_picture'] ?? 'default.png');
if (!file_exists($avatar_path)) {
    $avatar_path = 'assets/uploads/avatars/default.png'; // Pastikan Anda punya file default.png
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengaturan Akun - Hokage PPOB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style tambahan khusus untuk halaman pengaturan */
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2em;
        }
        .profile-picture-section {
            text-align: center;
        }
        .avatar-preview {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 1em;
        }
        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .btn-upload {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background-color: white;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
        }
        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .form-section label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .form-section small {
            color: var(--text-muted);
        }
        .form-section input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        @media (max-width: 768px) {
            .settings-container { grid-template-columns: 1fr; }
            .profile-picture-section { margin-bottom: 2em; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Hokage PPOB</h2></div>
            <nav class="sidebar-nav">
                <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="myxl.php"><i class="fas fa-sim-card"></i><span>Beli Paket XL/Axis</span></a>
                <a href="pengaturan.php" class="active"><i class="fas fa-cog"></i><span>Pengaturan</span></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
            <div class="sidebar-footer">&copy; 2025 Hokage Legend</div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Pengaturan Akun</h1>
                <p>Ubah informasi akun dan data pribadi Anda di sini.</p>
            </header>

            <div class="content-card">
                <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="response success" style="margin-bottom: 1em;">Data berhasil diperbarui!</div>
                <?php endif; ?>
                 <?php if(isset($_GET['error'])): ?>
                    <div class="response error" style="margin-bottom: 1em;"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <form action="pengaturan_handler.php" method="POST" enctype="multipart/form-data">
                    <div class="settings-container">
                        <div class="profile-picture-section">
                            <img src="<?php echo $avatar_path; ?>" alt="Foto Profil" id="avatarPreview" class="avatar-preview">
                            <div class="upload-btn-wrapper">
                                <button class="btn-upload" type="button">Ubah Foto</button>
                                <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*">
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small>Username tidak dapat diubah.</small>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Nomor Telepon (untuk myXL)</label>
                                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password Baru (opsional)</label>
                                <input type="password" id="password" name="password" placeholder="Isi jika ingin ganti password">
                            </div>
                            <button type="submit">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // JavaScript untuk preview gambar sebelum diupload
        const avatarInput = document.getElementById('profile_picture_input');
        const avatarPreview = document.getElementById('avatarPreview');

        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>