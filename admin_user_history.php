<?php
require_once 'admin_auth_check.php';
require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
$user_id = $_GET['id'];

// Ambil data user
$stmt_user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

if (!$user) {
    header("Location: admin_dashboard.php?error=User tidak ditemukan.");
    exit();
}

// Ambil data transaksi user
$stmt_transactions = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt_transactions->execute([$user_id]);
$transactions = $stmt_transactions->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 1.5em; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #f8f9fa; }
        .status-completed { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-failed { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="menu-toggle"><i class="fas fa-bars"></i></div>
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Admin PPOB</h2></div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="active"><i class="fas fa-users"></i><span>Kelola User</span></a>
                <a href="index.php"><i class="fas fa-arrow-left"></i><span>Kembali ke Website</span></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="welcome-text">
                    <h1>Riwayat Transaksi: <?php echo htmlspecialchars($user['username']); ?></h1>
                </div>
            </header>

            <div class="content-card">
                <?php if (count($transactions) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Tipe</th>
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
                            <td><?php echo ucfirst(htmlspecialchars($trx['type'])); ?></td>
                            <td><?php echo htmlspecialchars($trx['description']); ?></td>
                            <td>Rp <?php echo number_format($trx['amount'], 0, ',', '.'); ?></td>
                            <td><span class="status-<?php echo htmlspecialchars($trx['status']); ?>"><?php echo ucfirst(htmlspecialchars($trx['status'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>Tidak ada riwayat transaksi untuk pengguna ini.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="js/main.js"></script>
</body>
</html>