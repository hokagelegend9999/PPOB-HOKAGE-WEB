<?php
// Memastikan user sudah login (opsional, hapus jika halaman ini boleh diakses publik)
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Hokage Legend</title>
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
            <div class="sidebar-footer">&copy; <?php echo date("Y"); ?> Hokage Legend</div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="welcome-text">
                    <h1>Tentang Kami</h1>
                    <p>Mengenal lebih jauh tentang Hokage PPOB.</p>
                </div>
            </header>

            <div class="content-card">
                <h2>Selamat Datang di Hokage PPOB</h2>
                <p>
                    Hokage PPOB adalah platform Pembelian Pulsa digital terdepan di Indonesia yang menyediakan berbagai layanan PPOB (Payment Point Online Bank) untuk memenuhi semua kebutuhan transaksi digital Anda. Kami berkomitmen untuk memberikan kemudahan, kecepatan, dan keamanan dalam setiap transaksi.
                </p>

                <h3>Visi & Misi</h3>
                <p><strong>Visi:</strong> Menjadi platform PPOB paling andal dan inovatif yang dapat diakses oleh seluruh lapisan masyarakat Indonesia.</p>
                <p><strong>Misi:</strong></p>
                <ul>
                    <li>Menyediakan layanan transaksi yang lengkap, mulai dari pulsa, paket data, token listrik, hingga pembayaran tagihan.</li>
                    <li>Menawarkan harga yang kompetitif dan transparan tanpa biaya tersembunyi.</li>
                    <li>Mengutamakan keamanan data dan privasi pengguna dengan teknologi terkini.</li>
                    <li>Memberikan dukungan pelanggan yang responsif dan siap membantu 24/7.</li>
                </ul>

                <h3>Mengapa Memilih Kami?</h3>
                <p>Dengan teknologi yang canggih dan tim yang berdedikasi, kami memastikan setiap transaksi Anda berjalan lancar. Bergabunglah dengan ribuan pengguna puas lainnya dan nikmati kemudahan bertransaksi di ujung jari Anda.</p>
            </div>
        </main>
    </div>
</body>
</html>