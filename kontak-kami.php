<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - Hokage Legend</title>
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
                    <h1>Hubungi Kami</h1>
                    <p>Kami siap membantu Anda. Silakan hubungi kami melalui kanal di bawah ini.</p>
                </div>
            </header>

            <div class="content-card">
                <div class="contact-info-grid">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4>Alamat Kantor</h4>
                        <p>Jl. Konoha No. 123, Jakarta, Indonesia</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <h4>Email</h4>
                        <p>support@hokagelegend.web.id</p>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-whatsapp"></i>
                        <h4>WhatsApp</h4>
                        <p>+62 812-3456-7890</p>
                    </div>
                     <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <h4>Jam Operasional</h4>
                        <p>Senin - Jumat (08:00 - 17:00 WIB)</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>