<?php
// Memastikan user sudah login
require_once 'auth_check.php';
require_once 'db_connect.php'; // Menghubungkan ke database

// Mengambil data terbaru dari user yang sedang login
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, saldo, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Menghitung jumlah transaksi pada bulan berjalan
$stmt_transaksi = $pdo->prepare(
    "SELECT COUNT(*) FROM transactions WHERE user_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())"
);
$stmt_transaksi->execute([$user_id]);
$transactions_this_month = $stmt_transaksi->fetchColumn();

// Menyiapkan variabel untuk ditampilkan
$saldo = $user ? $user['saldo'] : 0;
$avatar_path = 'uploads/avatars/' . ($user['profile_picture'] ?? 'default.png');
if (!file_exists($avatar_path)) {
    $avatar_path = 'uploads/avatars/default.png'; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hokage Legend</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>

        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="User Avatar">
                <h2>Hokage PPOB</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="myxl.php"><i class="fas fa-sim-card"></i><span>Beli Paket XL/Axis</span><span class="badge">Hot</span></a>
                <a href="riwayat_transaksi.php"><i class="fas fa-history"></i><span>Riwayat Transaksi</span></a>
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
                    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p>Silakan pilih layanan PPOB yang Anda butuhkan.</p>
                </div>
                <div class="user-profile">
                    <div class="user-notification">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="user-avatar">
                        <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="User Avatar">
                    </div>
                </div>
            </header>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                        <p>Saldo Anda</p>
                    </div>
                </div>
                
                <a href="riwayat_transaksi.php" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon" style="background: rgba(76, 201, 240, 0.1); color: #4cc9f0;"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $transactions_this_month; ?></h3>
                        <p>Transaksi Bulan Ini</p>
                    </div>
                </a>

                <div id="bonus-card" class="stat-card" style="cursor: pointer;">
                    <div class="stat-icon" style="background: rgba(247, 37, 133, 0.1); color: #f72585;"><i class="fas fa-gift"></i></div>
                    <div class="stat-info">
                        <h3>5</h3>
                        <p>Bonus Aktif</p>
                    </div>
                </div>
            </div>

            <div class="services-grid">
                 <a href="myxl.php" class="service-card">
                    <img src="logo/xl-logo.png" alt="Ikon XL" class="icon">
                    <span>PAKET DATA XL</span>
                    <p>Paket data khusus XL & Axis</p>
                </a>
                <a href="topup.php" class="service-card">
                    <i class="fas fa-wallet"></i>
                    <span>Top Up E-Wallet</span>
                    <p>Gopay, OVO, Dana, dll</p>
                </a>
                <a href="#" class="service-card disabled">
                    <span class="badge-coming-soon">Segera Hadir</span>
                    <i class="fas fa-mobile-alt"></i>
                    <span>Pulsa All Operator</span>
                    <p>Layanan ini akan segera tersedia.</p>
                </a>
                <a href="#" class="service-card disabled">
                    <span class="badge-coming-soon">Segera Hadir</span>
                    <i class="fas fa-bolt"></i>
                    <span>Listrik PLN</span>
                    <p>Layanan ini akan segera tersedia.</p>
                </a>
                <a href="#" class="service-card disabled">
                    <span class="badge-coming-soon">Segera Hadir</span>
                    <i class="fas fa-gamepad"></i>
                    <span>Voucher Game</span>
                    <p>Layanan ini akan segera tersedia.</p>
                </a>
                <a href="#" class="service-card disabled">
                    <span class="badge-coming-soon">Segera Hadir</span>
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <p>Layanan ini akan segera tersedia.</p>
                </a>
            </div>
        </main>
    </div>
    
    <div id="bonus-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-star"></i>
            </div>
            <h2>Bonus Spesial Untuk Anda!</h2>
            <p>Tingkatkan terus transaksinya untuk mendapatkan bonus yang melimpah dari admin.</p>
            <button id="close-bonus-modal">Mengerti</button>
        </div>
    </div>

    <script src="js/main.js?v=<?php echo time(); ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bonusCard = document.getElementById('bonus-card');
        const bonusModal = document.getElementById('bonus-modal');
        const closeModalBtn = document.getElementById('close-bonus-modal');

        if (bonusCard) {
            bonusCard.addEventListener('click', () => {
                bonusCard.classList.add('is-animating');
                bonusModal.style.display = 'flex';
                setTimeout(() => {
                    bonusCard.classList.remove('is-animating');
                }, 600);
            });
        }

        const closeModal = () => {
            bonusModal.style.display = 'none';
        };
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }

        if (bonusModal) {
            bonusModal.addEventListener('click', (event) => {
                if (event.target === bonusModal) {
                    closeModal();
                }
            });
        }
    });
    </script>
</body>
</html>