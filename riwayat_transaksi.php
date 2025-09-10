<?php
// Memastikan user sudah login
require_once 'auth_check.php';
require_once 'db_connect.php';

// Ambil user_id dari sesi yang sedang login
$user_id = $_SESSION['user_id'];

// Ambil semua data transaksi untuk user ini, diurutkan dari yang terbaru
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();

// Ambil juga data avatar untuk ditampilkan di sidebar
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

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
    <title>Riwayat Transaksi - Hokage PPOB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS tambahan khusus untuk halaman riwayat */
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5em;
        }
        .transaction-table th, .transaction-table td {
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            text-align: left;
            vertical-align: middle;
        }
        .transaction-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 500;
            color: white;
            text-align: center;
        }
        .status-completed { background-color: #28a745; }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-failed { background-color: #dc3545; }
        .amount-positive { color: #28a745; font-weight: bold; }
        .amount-negative { color: #dc3545; font-weight: bold; }
        .no-transactions { text-align: center; padding: 2em; color: var(--text-muted); }
    </style>
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
                <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="myxl.php"><i class="fas fa-sim-card"></i><span>Beli Paket XL/Axis</span><span class="badge">Hot</span></a>
                <a href="riwayat_transaksi.php" class="active"><i class="fas fa-history"></i><span>Riwayat Transaksi</span></a>
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
                    <h1>Riwayat Transaksi</h1>
                    <p>Semua catatan transaksi Anda akan muncul di sini.</p>
                </div>
            </header>

            <div class="content-card">
                <?php if (count($transactions) > 0): ?>
                    <div style="overflow-x:auto;"> <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>ID Transaksi</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $trx): ?>
                                    <tr>
                                        <td><?php echo date('d M Y, H:i', strtotime($trx['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($trx['transaction_id']); ?></td>
                                        <td><?php echo htmlspecialchars($trx['description']); ?></td>
                                        <td>
                                            <?php
                                            $amount = $trx['amount'];
                                            $class = $amount >= 0 ? 'amount-positive' : 'amount-negative';
                                            $prefix = $amount >= 0 ? '+ ' : '- ';
                                            echo '<span class="' . $class . '">' . $prefix . 'Rp ' . number_format(abs($amount), 0, ',', '.') . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="status status-<?php echo htmlspecialchars($trx['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($trx['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-transactions">Anda belum memiliki riwayat transaksi.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>