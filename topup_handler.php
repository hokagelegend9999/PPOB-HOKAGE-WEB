<?php
require_once 'auth_check.php';
require_once 'config.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (int)$_POST['amount'];
    $method = $_POST['method'];
    $user_id = $_SESSION['user_id'];

    if ($amount < 10000) {
        die("Jumlah top up minimal Rp 10.000.");
    }

    // Ambil data user dari database
    $stmt = $pdo->prepare("SELECT username, email, phone_number FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Buat referensi unik untuk transaksi ini
    $merchantRef = 'TOPUP-' . $user_id . '-' . time();

    // Simpan dulu transaksi ke database kita dengan status PENDING
    try {
        // ✅ DIPERBAIKI: Menggunakan nama kolom yang benar sesuai struktur database Anda
        // Menggunakan `reff_id`, `transaction_status`, `payment_method`, dan `price`
        // Kolom `package_name` diisi 'Top Up Saldo'
        $stmt = $pdo->prepare(
            "INSERT INTO transactions (user_id, reff_id, package_name, transaction_status, payment_method, price) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $merchantRef, 'Top Up Saldo', 'pending', $method, $amount]);
    } catch (PDOException $e) {
        die("Gagal menyimpan transaksi awal: " . $e->getMessage());
    }

    // Data untuk dikirim ke Tripay
    $data = [
        'method'         => $method,
        'merchant_ref'   => $merchantRef,
        'amount'         => $amount,
        'customer_name'  => $user['username'],
        'customer_email' => $user['email'],
        'customer_phone' => $user['phone_number'],
        'order_items'    => [
            [
                'name'     => 'Top Up Saldo PPOB',
                'price'    => $amount,
                'quantity' => 1,
            ]
        ],
        'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
        'signature'    => hash_hmac('sha256', TRIPAY_MERCHANT_CODE . $merchantRef . $amount, TRIPAY_PRIVATE_KEY)
    ];

    // Kirim request ke Tripay menggunakan cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => TRIPAY_API_URL . '/transaction/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . TRIPAY_API_KEY],
        CURLOPT_FAILONERROR    => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    
    $result = json_decode($response, true);
    if (!$result['success']) {
        die("Gagal membuat transaksi Tripay: " . $result['message']);
    }

    // Update transaksi di database kita dengan referensi dari Tripay
    $tripay_reference = $result['data']['reference'];
    // ✅ DIPERBAIKI: Mencocokkan transaksi berdasarkan merchant_ref (reff_id kita) yang unik
    $stmt = $pdo->prepare("UPDATE transactions SET gateway_reference = ? WHERE reff_id = ?");
    $stmt->execute([$tripay_reference, $merchantRef]);

    // Arahkan user ke halaman pembayaran Tripay
    header('Location: ' . $result['data']['checkout_url']);
    exit();
}
?>