<?php

session_start();
// Pastikan file ini ada di direktori utama Anda
require_once 'vendor/autoload.php'; 
require_once 'config.php';
require_once 'db_connect.php';

// Membuat URL Login Google
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URL);
$client->addScope("email");
$client->addScope("profile");

$google_login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Hokage PPOB</title>
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
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            --gradient-primary-reverse: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            --shadow-primary: 0 10px 30px rgba(106, 17, 203, 0.15);
            --shadow-card: 0 15px 35px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-primary);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-primary);
            position: relative;
            overflow: hidden;
        }

        .logo i {
            font-size: 2.5rem;
            color: white;
        }

        .logo::after {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: shine 3s infinite linear;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .logo-tagline {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: -5px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
        }

        .welcome-text h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: #6c757d;
            margin-top: 0;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }

        .input-group input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
            outline: none;
            background-color: white;
        }

        button[type="submit"] {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: var(--shadow-primary);
        }

        button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(106, 17, 203, 0.2);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #6c757d;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e9ecef;
        }

        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }

        /* Style untuk tombol Google */
        .google-btn {
            background-color: #4285F4;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(66, 133, 244, 0.2);
        }

        .google-btn:hover {
            background-color: #357ae8;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(66, 133, 244, 0.3);
        }

        .google-btn img {
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            padding: 2px;
        }

        .extra-links {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .extra-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .extra-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        /* Animasi untuk elemen form */
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

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .logo {
                width: 80px;
                height: 80px;
            }
            
            .logo i {
                font-size: 2rem;
            }
            
            .logo-text {
                font-size: 1.5rem;
            }
            
            .welcome-text h2 {
                font-size: 1.5rem;
            }
        }

        /* Efek dekoratif */
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
        }

        /* Password toggle */
        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body class="login-page">
    <div class="floating-particles" id="particles"></div>
    
    <div class="login-container">
        <div class="logo-container animate-element" style="animation-delay: 0.1s">
            <div class="logo">
                <img src="logo/logo-icon.png" alt="Google Logo" height="100">
            </div>
            <div class="logo-text">HOKAGE PPOB</div>
            <div class="logo-tagline">Jasa Pembelian Pulsa Terpercaya</div>
        </div>

        <div class="welcome-text animate-element" style="animation-delay: 0.2s">
            <h2>Selamat Datang Kembali</h2>
            <p>Silakan login untuk melanjutkan ke akun Anda</p>
        </div>

        <form action="login_handler.php" method="POST">
            <div class="input-group animate-element" style="animation-delay: 0.3s">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username Anda" required>
            </div>
            
            <div class="input-group animate-element" style="animation-delay: 0.4s">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    <span class="toggle-password" id="togglePassword">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="animate-element" style="animation-delay: 0.5s">Login</button>
        </form>

        <div class="divider animate-element" style="animation-delay: 0.6s">
            <span>atau</span>
        </div>

        <a href="<?php echo htmlspecialchars($google_login_url); ?>" class="google-btn animate-element" style="animation-delay: 0.7s">
            <img src="logo/google.svg" alt="Google Logo">
            <span>Masuk dengan Google</span>
        </a>
        
        <div class="extra-links animate-element" style="animation-delay: 0.8s">
            Belum punya akun? <a href="register_form.php">Daftar di sini</a>
        </div>
    </div>

    <script>
        // Animasi partikel latar belakang
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            animateElements();
            
            // Toggle password visibility
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size and position
                const size = Math.random() * 20 + 5;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                
                // Random animation
                const animationDuration = Math.random() * 15 + 10;
                particle.style.animation = `float ${animationDuration}s infinite ease-in-out`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                container.appendChild(particle);
            }
            
            // Add floating animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes float {
                    0%, 100% { transform: translateY(0) rotate(0deg); }
                    25% { transform: translateY(-20px) rotate(5deg); }
                    50% { transform: translateY(0) rotate(0deg); }
                    75% { transform: translateY(20px) rotate(-5deg); }
                }
            `;
            document.head.appendChild(style);
        }
        
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