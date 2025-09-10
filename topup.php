<?php
require_once 'auth_check.php';
// Siapkan data user untuk pesan konfirmasi
$username_for_wa = urlencode($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Top Up Saldo - Hokage PPOB</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --accent-color: #ff4b8b;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --gold-color: #ffd700;
            --silver-color: #c0c0c0;
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            --gradient-gold: linear-gradient(135deg, #ffd700 0%, #daa520 100%);
            --gradient-silver: linear-gradient(135deg, #c0c0c0 0%, #a9a9a9 100%);
            --shadow-primary: 0 10px 30px rgba(106, 17, 203, 0.15);
            --shadow-card: 0 15px 35px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            padding-bottom: 4rem;
            overflow-y: auto;
        }

        .main-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .main-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }

        .main-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .main-header p {
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Content Card */
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-primary);
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .tab-button {
            padding: 1.2rem 2rem;
            cursor: pointer;
            border: none;
            background-color: transparent;
            font-size: 1.1rem;
            font-weight: 500;
            color: #6c757d;
            position: relative;
            transition: all 0.3s ease;
            outline: none;
        }

        .tab-button:hover {
            color: var(--primary-color);
        }

        .tab-button.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 500;
            color: #495057;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
            outline: none;
            background-color: white;
        }

        button[type="submit"] {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1.2rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            box-shadow: var(--shadow-primary);
        }

        button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(106, 17, 203, 0.2);
        }

        /* Manual Info */
        .manual-info {
            text-align: center;
            padding: 1rem;
        }

        .manual-info h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
        }

        .manual-info h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .manual-info p {
            margin: 1.5rem 0;
            line-height: 1.7;
            color: #6c757d;
            font-size: 1.05rem;
        }

        .manual-info img {
            max-width: 280px;
            border-radius: 12px;
            margin: 1.5rem 0;
            border: 1px solid #e9ecef;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .manual-info img:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        /* Contact Buttons */
        .contact-buttons {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            max-width: 320px;
            margin: 2rem auto;
        }

        .contact-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }

        .btn-telegram {
            background: linear-gradient(135deg, #0088cc 0%, #005c8a 100%);
        }

        .contact-btn i {
            margin-right: 12px;
            font-size: 1.4rem;
        }

        /* Amount Suggestions */
        .amount-suggestions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .amount-option {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .amount-option:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-3px);
        }

        .amount-option.selected {
            border-color: var(--primary-color);
            background: rgba(106, 17, 203, 0.05);
            position: relative;
        }

        .amount-option.selected::after {
            content: '✓';
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--primary-color);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .amount-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .amount-bonus {
            font-size: 0.85rem;
            color: var(--success-color);
            margin-top: 0.3rem;
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-3px);
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: rgba(106, 17, 203, 0.05);
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .payment-icon img {
            max-width: 30px;
            max-height: 30px;
        }

        .payment-name {
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .amount-suggestions,
            .payment-methods {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .content-card {
                padding: 1.5rem;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .amount-suggestions,
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .manual-info img {
                max-width: 100%;
            }
        }

        /* Animation for elements */
        .animate-element {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Decorative elements */
        .floating-icon {
            position: absolute;
            opacity: 0.03;
            font-size: 15rem;
            z-index: 0;
            pointer-events: none;
        }

        .floating-icon.wallet {
            bottom: -50px;
            right: -30px;
            color: var(--primary-color);
        }

        .floating-icon.coins {
            top: 50%;
            left: -50px;
            color: var(--accent-color);
            transform: translateY(-50%);
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
                <a href="topup.php" class="active"><i class="fas fa-wallet"></i><span>Top Up Saldo</span></a>
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
                <h1>Top Up Saldo</h1>
                <p>Tingkatkan saldo Anda dengan mudah dan nikmati kemudahan bertransaksi</p>
            </header>

            <div class="content-card">
                <i class="floating-icon wallet fas fa-wallet"></i>
                <i class="floating-icon coins fas fa-coins"></i>
                
                <div class="tabs">
                    <button class="tab-button active" onclick="openTab(event, 'otomatis')">
                        <i class="fas fa-bolt"></i> Otomatis via Tripay
                    </button>
                    <button class="tab-button" onclick="openTab(event, 'manual')">
                        <i class="fas fa-exchange-alt"></i> Manual via Transfer
                    </button>
                </div>

                <div id="otomatis" class="tab-content active">
                    <h2 class="animate-element">Pilih Jumlah & Metode Pembayaran</h2>
                    
                    <div class="amount-suggestions animate-element" style="animation-delay: 0.1s">
                        <div class="amount-option" data-amount="10000">
                            <div class="amount-value">Rp 10.000</div>
                        </div>
                        <div class="amount-option" data-amount="25000">
                            <div class="amount-value">Rp 25.000</div>
                            <div class="amount-bonus">+Bonus Rp 500</div>
                        </div>
                        <div class="amount-option" data-amount="50000">
                            <div class="amount-value">Rp 50.000</div>
                            <div class="amount-bonus">+Bonus Rp 1.500</div>
                        </div>
                        <div class="amount-option" data-amount="100000">
                            <div class="amount-value">Rp 100.000</div>
                            <div class="amount-bonus">+Bonus Rp 3.500</div>
                        </div>
                        <div class="amount-option" data-amount="250000">
                            <div class="amount-value">Rp 250.000</div>
                            <div class="amount-bonus">+Bonus Rp 10.000</div>
                        </div>
                        <div class="amount-option" data-amount="500000">
                            <div class="amount-value">Rp 500.000</div>
                            <div class="amount-bonus">+Bonus Rp 25.000</div>
                        </div>
                    </div>
                    
                    <form action="topup_handler.php" method="POST">
                        <div class="form-group animate-element" style="animation-delay: 0.2s">
                            <label for="amount">Jumlah Top Up (Rp)</label>
                            <input type="number" id="amount" name="amount" min="10000" placeholder="Minimal Rp 10.000" required>
                        </div>
                        
                        <h3 class="animate-element" style="animation-delay: 0.3s">Pilih Metode Pembayaran</h3>
                        
                        <div class="payment-methods animate-element" style="animation-delay: 0.4s">
                            <div class="payment-method" data-method="QRIS">
                                <div class="payment-icon">
                                    <i class="fas fa-qrcode" style="color: #6a11cb;"></i>
                                </div>
                                <div class="payment-name">QRIS (GoPay, OVO, DANA, dll)</div>
                            </div>
                            <div class="payment-method" data-method="BCAVA">
                                <div class="payment-icon">
                                    <i class="fas fa-university" style="color: #0061a8;"></i>
                                </div>
                                <div class="payment-name">BCA Virtual Account</div>
                            </div>
                            <div class="payment-method" data-method="BRIVA">
                                <div class="payment-icon">
                                    <i class="fas fa-university" style="color: #ed1c24;"></i>
                                </div>
                                <div class="payment-name">BRI Virtual Account</div>
                            </div>
                            <div class="payment-method" data-method="MANDIRIVA">
                                <div class="payment-icon">
                                    <i class="fas fa-university" style="color: #003a73;"></i>
                                </div>
                                <div class="payment-name">Mandiri Virtual Account</div>
                            </div>
                            <div class="payment-method" data-method="ALFAMART">
                                <div class="payment-icon">
                                    <i class="fas fa-store" style="color: #00a650;"></i>
                                </div>
                                <div class="payment-name">Alfamart</div>
                            </div>
                            <div class="payment-method" data-method="INDOMARET">
                                <div class="payment-icon">
                                    <i class="fas fa-store" style="color: #d70010;"></i>
                                </div>
                                <div class="payment-name">Indomaret</div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selected-method" name="method" required>
                        
                        <button type="submit" class="animate-element" style="animation-delay: 0.5s">
                            <i class="fas fa-arrow-right"></i> Lanjutkan Pembayaran
                        </button>
                    </form>
                </div>

                <div id="manual" class="tab-content">
                    <div class="manual-info">
                        <h2 class="animate-element">Top Up Manual</h2>
                        <p class="animate-element" style="animation-delay: 0.1s">Silakan lakukan transfer ke salah satu rekening atau pindai QRIS di bawah ini. Setelah transfer, <strong>wajib</strong> konfirmasi melalui WhatsApp atau Telegram.</p>
                        
                        <img src="uploads/qris.jpg" alt="Scan QRIS untuk pembayaran" class="animate-element" style="animation-delay: 0.2s">

                        <p class="animate-element" style="animation-delay: 0.3s">Setelah berhasil transfer, klik tombol di bawah untuk konfirmasi ke admin.</p>
                        <div class="contact-buttons animate-element" style="animation-delay: 0.4s">
                            <a href="https://wa.me/6287726917005?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20top%20up%20manual.%0AUsername:%20<?php echo $username_for_wa; ?>%0AJumlah:%20%20(isi%20sendiri)%0A%0A(Lampirkan%20bukti%20transfer)" target="_blank" class="contact-btn btn-whatsapp">
                                <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
                            </a>
                            <a href="https://t.me/HookageLegend" target="_blank" class="contact-btn btn-telegram">
                                <i class="fab fa-telegram-plane"></i> Konfirmasi via Telegram
                            </a>
                        </div>
                        <p style="margin-top: 2em; font-size: 0.9em; color: #555;" class="animate-element" style="animation-delay: 0.5s">Grup Telegram: <a href="https://t.me/hokagelegend1" target="_blank">Gabung di sini</a><br>Email: admin@hokagelegend.web.id</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function openTab(evt, tabName) {
            // Sembunyikan semua konten tab
            const tabcontent = document.getElementsByClassName("tab-content");
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Hapus kelas 'active' dari semua tombol tab
            const tablinks = document.getElementsByClassName("tab-button");
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            // Tampilkan konten tab yang diklik dan tambahkan kelas 'active' ke tombolnya
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
            
            // Animate elements when tab changes
            animateElements();
        }
        
        // Amount selection
        document.querySelectorAll('.amount-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.amount-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('amount').value = this.getAttribute('data-amount');
            });
        });
        
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('selected-method').value = this.getAttribute('data-method');
            });
        });
        
        // Animate elements on page load
        document.addEventListener('DOMContentLoaded', function() {
            animateElements();
        });
        
        function animateElements() {
            const elements = document.querySelectorAll('.animate-element');
            elements.forEach((element, index) => {
                element.style.animationDelay = (index * 0.1) + 's';
                element.classList.add('animate-element');
            });
        }
    </script>
</body>
</html>