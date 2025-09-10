<?php
require_once 'admin_auth_check.php';
require_once 'db_connect.php';

// Ambil semua data user untuk ditampilkan
$stmt = $pdo->query("SELECT id, username, email, phone_number, saldo, role FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin PPOB</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS yang sudah ada sebelumnya */
        table { width: 100%; border-collapse: collapse; margin-top: 1.5em; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #f8f9fa; }
        .topup-form { display: flex; align-items: center; gap: 8px; }
        .topup-form input { padding: 8px; max-width: 120px; border: 1px solid #ccc; border-radius: 6px; }
        .action-btn, .topup-form button { 
            padding: 8px 12px; font-size: 0.9em; width: auto; color: white;
            border: none; border-radius: 6px; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .topup-form button { background-color: var(--primary-color); }
        .topup-form button:hover { background-color: var(--secondary-color); }
        
        /* CSS untuk tombol Aksi baru */
        .actions-cell { display: flex; flex-wrap: wrap; gap: 8px; }
        .btn-history { background-color: #17a2b8; } /* Biru Info */
        .btn-delete { background-color: #dc3545; } /* Merah Bahaya */
        .btn-add-user {
            background-color: #28a745; /* Hijau Sukses */
            color: white; padding: 10px 15px; border-radius: 8px;
            margin-bottom: 1em; cursor: pointer; border: none; font-weight: 500;
        }
        
        /* Form Tambah User */
        #addUserFormContainer { display: none; background-color: #f8f9fa; padding: 1.5em; border-radius: 8px; margin-bottom: 1.5em; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1em; }
        .form-grid input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }

        @media (max-width: 768px) {
            /* CSS Responsif yang sudah ada */
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { border: 1px solid #ccc; margin-bottom: 1rem; }
            td { display: flex; justify-content: space-between; align-items: center; border: none; border-bottom: 1px solid #eee; padding: 12px; overflow-wrap: break-word; word-wrap: break-word; text-align: right; }
            td:before { content: attr(data-label); font-weight: bold; padding-right: 1em; text-align: left; }
        }
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
                <div class="welcome-text"><h1>Kelola Pengguna</h1></div>
            </header>
            
            <?php if(isset($_GET['message'])): ?>
                <div class="response success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 1em;">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="response error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 1em;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <button id="addUserBtn" class="btn-add-user"><i class="fas fa-plus"></i> Tambah User Baru</button>
            <div id="addUserFormContainer">
                <h3>Form Tambah User</h3>
                <form action="add_user_handler.php" method="POST">
                    <div class="form-grid">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="tel" name="phone_number" placeholder="Nomor Telepon" required>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <br>
                    <button type="submit" class="action-btn" style="background-color: var(--primary-color);">Simpan User</button>
                </form>
            </div>

            <div class="content-card">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email / No. HP</th>
                            <th>Saldo</th>
                            <th>Top Up</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Username"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td data-label="Email/HP"><?php echo htmlspecialchars($user['email']); ?><br><small><?php echo htmlspecialchars($user['phone_number']); ?></small></td>
                            <td data-label="Saldo">Rp <?php echo number_format($user['saldo'], 0, ',', '.'); ?></td>
                            <td data-label="Aksi Top Up">
                                <form action="admin_topup_handler.php" method="POST" class="topup-form">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="number" name="amount" placeholder="Jumlah" required min="1000">
                                    <button type="submit">Top Up</button>
                                </form>
                            </td>
                            <td data-label="Aksi" class="actions-cell">
                                <a href="admin_user_history.php?id=<?php echo $user['id']; ?>" class="action-btn btn-history"><i class="fas fa-history"></i> Riwayat</a>
                                <form action="delete_user_handler.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="action-btn btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Script untuk menampilkan/menyembunyikan form tambah user
        document.getElementById('addUserBtn').addEventListener('click', function() {
            var formContainer = document.getElementById('addUserFormContainer');
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>