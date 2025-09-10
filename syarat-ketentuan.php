<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Hokage Legend</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="https://i.pravatar.cc/150?img=12" alt="Logo Hokage">
                <h2>Hokage PPOB</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="myxl.php"><i class="fas fa-sim-card"></i><span>Beli Paket XL/Axis</span><span class="badge">Hot</span></a>
                <a href="#"><i class="fas fa-history"></i><span>Riwayat Transaksi</span></a>
                <a href="topup.php"><i class="fas fa-wallet"></i><span>Top Up Saldo</span></a>
                <a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
            <div class="sidebar-footer">
    <div class="footer-links">
        <a href="tentang-kami.php">Tentang</a> | 
        <a href="kontak-kami.php">Kontak</a> | 
        <a href="syarat-ketentuan.php">S&K</a>
    </div>
    &copy; <?php echo date("Y"); ?> Hokage Legend
</div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="welcome-text">
                    <h1>Syarat dan Ketentuan</h1>
                    <p>Mohon baca dengan saksama sebelum menggunakan layanan kami.</p>
                </div>
            </header>

            <div class="content-card">
                <h2>1. Pendahuluan</h2>
                <p>Syarat dan Ketentuan ini mengatur penggunaan platform Hokage PPOB. Dengan mendaftar dan menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui seluruh isi dalam Syarat dan Ketentuan ini.</p>

                <h2>2. Akun Pengguna</h2>
                <p>Anda bertanggung jawab penuh atas keamanan akun dan password Anda. Segala aktivitas yang terjadi melalui akun Anda adalah tanggung jawab Anda. Segera hubungi kami jika Anda mencurigai adanya penyalahgunaan akun.</p>

                <h2>3. Transaksi dan Pembayaran</h2>
                <p>Semua transaksi yang telah diproses tidak dapat dibatalkan. Pastikan Anda memasukkan data (nomor tujuan, ID pelanggan, dll.) dengan benar sebelum melakukan pembayaran. Harga dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu.</p>

                <h2>4. Privasi</h2>
                <p>Kami menjaga kerahasiaan data pribadi Anda sesuai dengan Kebijakan Privasi kami. Kami tidak akan membagikan informasi pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.</p>
                
                <h2>5. Perubahan Ketentuan</h2>
                <p>Hokage PPOB berhak untuk mengubah Syarat dan Ketentuan ini dari waktu ke waktu. Perubahan akan diinformasikan melalui platform. Dengan tetap menggunakan layanan, Anda dianggap setuju dengan perubahan tersebut.</p>
            </div>
        </main>
    </div>
</body>
</html>