<?php
// Memastikan hanya user (reseller) yang sudah login ke website yang bisa mengakses halaman ini.
require_once 'auth_check.php';
require_once 'db_connect.php';
$stmt_saldo = $pdo->prepare("SELECT saldo FROM users WHERE id = ?");
$stmt_saldo->execute([$_SESSION['user_id']]);
$current_saldo = $stmt_saldo->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Paket XL/Axis (Reseller) - Hokage PPOB</title>
    <style>
        /* CSS Lengkap dari Dashboard Utama */
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --bg-color: #f4f7f6;
            --sidebar-bg: #343a40;
            --card-bg: #ffffff;
            --text-color: #333;
            --text-light: #f8f9fa;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--sidebar-bg); color: var(--text-light); padding: 1.5em; display: flex; flex-direction: column; }
        .sidebar-header { text-align: center; margin-bottom: 2em; font-size: 1.5em; font-weight: bold; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; color: var(--text-light); text-decoration: none; padding: 1em; border-radius: 8px; margin-bottom: 0.5em; transition: background-color 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #495057; }
        .sidebar-nav a svg, .sidebar-nav a i { width: 24px; height: 24px; margin-right: 5px; text-align: center; }
        .sidebar-footer { margin-top: auto; text-align: center; font-size: 0.8em; opacity: 0.7; }
        .main-content { flex-grow: 1; padding: 2em; }
        .main-header { margin-bottom: 2em; }
        .content-card { background-color: var(--card-bg); padding: 2em; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .view-container { display: none; /* Diatur oleh JS */ }
        .view-container h2 { color: var(--primary-color); text-align: center; margin-top: 0; }
        .form-group { margin-bottom: 1.5em; }
        label { display: block; margin-bottom: 0.5em; font-weight: 500; }
        input { width: 100%; padding: 0.75em; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; }
        button { width: 100%; padding: 0.75em; background-color: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; font-weight: bold; }
        button:disabled { background-color: var(--secondary-color); cursor: not-allowed; }
        .response { margin-top: 1.5em; padding: 1em; border-radius: 4px; word-wrap: break-word; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

        #package-list { margin-top: 1em; max-height: 400px; overflow-y: auto; padding-right: 10px; }
        .package-item { display: flex; justify-content: space-between; align-items: center; padding: 1em; border-bottom: 1px solid #e9ecef; }
        .package-item:last-child { border-bottom: none; }
        .package-details { text-align: left; }
        .package-name { font-weight: bold; }
        .package-price { color: #28a745; }
        .buy-btn { width: auto; padding: 0.5em 1em; font-size: 0.9em; }

        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1em; }
        .change-number-btn { background-color: var(--secondary-color); color: white; text-decoration: none; padding: 0.5em 1em; border-radius: 6px; font-size: 0.9em; font-weight: 500; transition: background-color 0.2s; }
        .change-number-btn:hover { background-color: #5a6268; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        #modal-container {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center; align-items: center; z-index: 1000;
            animation: fadeIn 0.3s ease-out;
        }
        #modal-content {
            background: white; border-radius: 12px;
            width: 90%; max-width: 380px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: scaleUp 0.3s ease-out;
            overflow: hidden;
        }
        .modal-header { background-color: var(--primary-color); color: white; padding: 1em; display: flex; align-items: center; gap: 10px; font-size: 1.2em; font-weight: bold; }
        .modal-header svg { width: 28px; height: 28px; }
        .modal-body { padding: 1.5em; }
        .invoice-details { margin-bottom: 1.5em; font-size: 0.9em; }
        .invoice-item { display: flex; justify-content: space-between; padding: 0.5em 0; border-bottom: 1px dashed #ccc; }
        .invoice-item:last-child { border-bottom: none; }
        .invoice-item span:last-child { font-weight: bold; }
        #qrcode-wrapper { position: relative; margin: 1em auto; border: 1px solid #eee; border-radius: 8px; padding: 10px; width: 256px; height: 256px; display: flex; justify-content: center; align-items: center; }
        .instructions { font-size: 0.85em; color: #555; margin-top: 1.5em; line-height: 1.5; }
        .timer { font-size: 1.1em; font-weight: bold; color: var(--primary-color); margin-top: 1em; }
        .modal-footer { padding: 1.5em; background-color: #f9f9f9; border-top: 1px solid #eee; }
        #close-modal { background-color: var(--secondary-color); }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 1.5em 0.5em; }
            .sidebar-header, .sidebar-nav span { display: none; }
            .sidebar-nav a { justify-content: center; }
        }
        .package-card {
    background: linear-gradient(135deg, #f0f4f8, #e0e7ee); /* Latar belakang gradien lembut */
    border-radius: 15px; /* Sudut membulat */
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Bayangan lembut yang mengangkat card */
    padding: 25px 30px; /* Jarak di dalam card */
    margin: 15px auto; /* Pusatkan card dan beri jarak */
    text-align: center; /* Teks di tengah */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Efek transisi saat hover */
    max-width: 300px; /* Batasi lebar card untuk tampilan rapi */
}

/* Efek saat kursor di atas card (optional tapi keren) */
.package-card:hover {
    transform: translateY(-5px); /* Card sedikit terangkat */
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15); /* Bayangan sedikit lebih kuat */
}

/* Styling untuk nama paket */
.package-name {
    font-size: 1.6em; /* Ukuran font lebih besar */
    font-weight: 700; /* Tebal */
    color: #4a4a4a; /* Warna teks gelap */
    margin-bottom: 5px; /* Jarak bawah */
    letter-spacing: 0.5px; /* Spasi antar huruf */
}

/* Styling untuk harga paket */
.package-price {
    font-size: 2.2em; /* Ukuran font sangat besar untuk harga */
    font-weight: 800; /* Sangat tebal */
    color: #7B68EE; /* Warna ungu yang menonjol */
    display: block;
    margin-top: 10px;
    margin-bottom: 20px; /* Jarak bawah untuk tombol */
    background: -webkit-linear-gradient(45deg, #7B68EE, #a87ee0); /* Gradien warna pada teks harga */
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Styling untuk tombol "Beli" */
.buy-btn {
    background-color: #7B68EE; /* Warna latar belakang tombol yang cocok dengan teks harga */
    color: #fff; /* Warna teks putih */
    border: none; /* Hapus border default */
    border-radius: 50px; /* Sudut sangat membulat, gaya "pill button" */
    padding: 12px 28px; /* Ruang dalam tombol */
    font-size: 1em; /* Ukuran font tombol */
    font-weight: 600; /* Ketebalan font */
    cursor: pointer; /* Ubah kursor menjadi pointer saat di atas tombol */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Transisi halus saat interaksi */
    box-shadow: 0 4px 15px rgba(123, 104, 238, 0.3); /* Bayangan tombol */
}

/* Efek saat kursor di atas tombol */
.buy-btn:hover {
    background-color: #6a5acd; /* Warna sedikit lebih gelap saat di-hover */
    transform: translateY(-2px); /* Tombol sedikit terangkat */
    box-shadow: 0 6px 20px rgba(123, 104, 238, 0.4); /* Bayangan sedikit lebih kuat */
}

/* Penyesuaian untuk tampilan responsif (opsional) */
@media (max-width: 600px) {
    .package-card {
        padding: 20px;
        margin: 10px auto;
    }
    .package-name {
        font-size: 1.4em;
    }
    .package-price {
        font-size: 1.8em;
    }
    .buy-btn {
        padding: 10px 24px;
        font-size: 0.9em;
    }
}
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                Hokage PPOB
            </div>
            <nav class="sidebar-nav">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="myxl.php" class="active">
                     <i class="fas fa-sim-card"></i>
                    <span>Beli Paket XL/Axis</span>
                </a>
                 <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
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
                <h1>Pembelian Paket XL & Axis (Reseller)</h1>
                <p>Masukkan nomor pelanggan untuk memulai transaksi.</p>
            </header>
            <div class="stat-card" style="margin-bottom: 2em; background-color: #e9f7ff;">
        <div class="stat-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <h3>Rp <?php echo number_format($current_saldo, 0, ',', '.'); ?></h3>
            <p>Saldo Anda Saat Ini</p>
        </div>
    </div>
            <div class="content-card">
                <div id="link-account-view" class="view-container">
                    <h2>Masukkan Nomor Pelanggan</h2>
                    <form id="requestOtpForm">
                        <div class="form-group">
                            <label for="customerPhoneNumber">Nomor HP Pelanggan (Contoh: 628xxxx):</label>
                            <input type="tel" id="customerPhoneNumber" required>
                        </div>
                        <button type="submit" id="requestOtpBtn">Kirim OTP</button>
                    </form>
                    <div id="link-response" class="response"></div>
                </div>

                <div id="otp-view" class="view-container">
                    <h2>Verifikasi OTP</h2>
                    <p style="text-align:center;">Minta dan masukkan 6 digit kode OTP yang dikirim ke <strong id="otp-phonenumber-display"></strong>.</p>
                    <form id="otpForm">
                        <div class="form-group"><label for="otpCode">Kode OTP:</label><input type="number" id="otpCode" required></div>
                        <button type="submit" id="otpBtn">Verifikasi & Lanjutkan</button>
                    </form>
                    <div id="otp-response" class="response"></div>
                </div>

                <div id="purchase-view" class="view-container">
                    <div class="header-actions">
                        <h2>Pilih Paket untuk <span id="customer-number-display" style="color: var(--primary-color); font-weight: bold;"></span></h2>
                        <a href="myxl_logout_handler.php" class="change-number-btn">Ganti Nomor</a>
                    </div>
                    <div id="purchase-response" class="response"></div>
                    <div id="package-list"></div>
                </div>
            </div>
        </main>
    </div>

    <div id="modal-container">
        <div id="modal-content">
            <div class="modal-header">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5v15a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25V4.5m-18 0h18M12 18.75h.008v.008H12v-.008Z" /></svg>
                <span>Pembayaran QRIS</span>
            </div>
            <div class="modal-body">
                <div class="invoice-details">
                    <div class="invoice-item"><span>Paket:</span><span id="modal-package-name">-</span></div>
                    <div class="invoice-item"><span>Harga:</span><span id="modal-package-price">-</span></div>
                </div>
                <div id="qrcode-wrapper"><span id="qrcode-loader">Memuat QR Code...</span></div>
                <div id="timer" class="timer"></div>
                <p class="instructions">
    Yuk, selesaikan pembayaran! Ikuti langkah mudah berikut:<br><br>
    <strong>1. Buka Mobile Banking atau E-Wallet pilihan Anda.</strong><br>
    <strong>2. Pilih menu "Bayar" Pembayaran Langsung Ke XL PROVIDER"</strong><br>
</p>
            </div>
            <div class="modal-footer"><button id="close-modal">Tutup</button></div>
        </div>
    </div>
    
    <script>
    // --- DEKLARASI ELEMEN ---
    const linkAccountView = document.getElementById('link-account-view');
    const otpView = document.getElementById('otp-view');
    const purchaseView = document.getElementById('purchase-view');
    const modalContainer = document.getElementById('modal-container');
    let countdownInterval;

    // --- FUNGSI-FUNGSI BANTUAN ---
    function showView(viewId) {
        document.querySelectorAll('.view-container').forEach(v => v.style.display = 'none');
        document.getElementById(viewId).style.display = 'block';
    }

    function startTimer(duration, display) {
        let timer = duration, minutes, seconds;
        clearInterval(countdownInterval);
        countdownInterval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = "QRIS valid dalam: " + minutes + ":" + seconds;
            if (--timer < 0) {
                clearInterval(countdownInterval);
                display.textContent = "QRIS Kedaluwarsa";
                document.getElementById('qrcode-wrapper').innerHTML = '<span style="color:red;">Waktu habis, silakan coba lagi.</span>';
            }
        }, 1000);
    }

    async function loadPackages() {
        const packageListDiv = document.getElementById('package-list');
        const responseDiv = document.getElementById('purchase-response');
        packageListDiv.innerHTML = '<p style="text-align:center;">Memuat daftar paket...</p>';
        responseDiv.innerHTML = '';
        try {
            const res = await fetch('packages_handler.php');
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'Gagal memuat paket.');
            packageListDiv.innerHTML = '';
            data.packages.forEach(pkg => {
                const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(pkg.price);
                const item = document.createElement('div');
                item.className = 'package-item';
                item.innerHTML = `
                   <div class="package-card">
    <div class="package-name">${pkg.name}</div>
    <div class="package-price">${priceFormatted}</div>
    <button class="buy-btn" data-code="${pkg.code}" data-name="${pkg.name}" data-price="${pkg.price}">Beli</button>
</div>
                `;
                packageListDiv.appendChild(item);
            });
        } catch (error) {
            responseDiv.className = 'response error';
            responseDiv.textContent = error.message;
        }
    }

    // --- EVENT LISTENERS ---

    // Alur Utama Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await fetch('myxl_login_handler.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'check_status' }) });
            const data = await res.json();
            if (data.status === 'session_active') {
                showView('purchase-view');
                loadPackages();
            } else {
                showView('link-account-view');
            }
        } catch (e) { showView('link-account-view'); }
    });

    // Event Listener untuk form Kirim OTP
    document.getElementById('requestOtpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('requestOtpBtn');
        const responseDiv = document.getElementById('link-response');
        const customerPhoneNumber = document.getElementById('customerPhoneNumber').value;
        
        btn.disabled = true; btn.textContent = 'Mengirim...'; responseDiv.innerHTML = ''; responseDiv.className = 'response';
        
        try {
            const res = await fetch('myxl_login_handler.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ action: 'request_otp', phone_number: customerPhoneNumber }) 
            });
            const data = await res.json(); 
            if (!res.ok) throw new Error(data.message);
            
            document.getElementById('otp-phonenumber-display').textContent = customerPhoneNumber;
            document.getElementById('customer-number-display').textContent = customerPhoneNumber;
            showView('otp-view');
        } catch (error) {
            responseDiv.className = 'response error'; responseDiv.textContent = 'Gagal: ' + error.message;
        } finally {
            btn.disabled = false; btn.textContent = 'Kirim OTP';
        }
    });

    // Event Listener untuk form verifikasi OTP
    document.getElementById('otpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('otpBtn');
        const responseDiv = document.getElementById('otp-response');
        const otpCode = document.getElementById('otpCode').value;
        btn.disabled = true; btn.textContent = 'Memverifikasi...'; responseDiv.innerHTML = ''; responseDiv.className = 'response';
        try {
            const res = await fetch('myxl_login_handler.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ action: 'submit_otp', otp_code: otpCode }) 
            });
            const data = await res.json(); if (!res.ok) throw new Error(data.message);
            showView('purchase-view');
            loadPackages();
        } catch (error) {
            responseDiv.className = 'response error'; responseDiv.textContent = 'Gagal: ' + error.message;
        } finally {
            btn.disabled = false; btn.textContent = 'Verifikasi & Lanjutkan';
        }
    });
    
    // Event Listener untuk tombol Beli di daftar paket
    document.getElementById('package-list').addEventListener('click', async (e) => {
        if (e.target && e.target.classList.contains('buy-btn')) {
            const button = e.target;
            const packageCode = button.dataset.code;
            const packageName = button.dataset.name;
            const packagePrice = button.dataset.price;
            const originalText = button.textContent;
            button.disabled = true; button.textContent = '...';
            
            const modalPackageName = document.getElementById('modal-package-name');
            const modalPackagePrice = document.getElementById('modal-package-price');
            const qrcodeWrapper = document.getElementById('qrcode-wrapper');
            
            modalPackageName.textContent = packageName;
            modalPackagePrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(packagePrice);
            qrcodeWrapper.innerHTML = '<span id="qrcode-loader">Membuat QR Code...</span>';
            modalContainer.style.display = 'flex';
            startTimer(5 * 60, document.getElementById('timer'));
            try {
                const res = await fetch('purchase_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ package_code: packageCode })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);
                qrcodeWrapper.innerHTML = '';
                new QRCode(qrcodeWrapper, { text: data.qr_code, width: 256, height: 256 });
            } catch (error) {
                clearInterval(countdownInterval);
                document.getElementById('timer').textContent = '';
                qrcodeWrapper.innerHTML = `<span style="color:red;">Gagal: ${error.message}</span>`;
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        }
    });

    // Event listener untuk tombol tutup modal
    document.getElementById('close-modal').addEventListener('click', () => {
        modalContainer.style.display = 'none';
        clearInterval(countdownInterval);
    });
</script>
</body>
</html>