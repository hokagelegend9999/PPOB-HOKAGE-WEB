<?php
session_start();
header('Content-Type: application/json');

// Memanggil semua file yang dibutuhkan
require_once 'db_connect.php';
require_once 'api_client.php';

try {
    // Pastikan user sudah login ke sistem kita dan ke myXL
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['xl_api_tokens'])) {
        throw new Exception("Sesi tidak valid. Silakan login kembali.");
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $packageCode = $input['package_code'] ?? null;
    $paymentMethod = $input['payment_method'] ?? null;
    $walletNumber = $input['wallet_number'] ?? ''; // Nomor HP untuk DANA/OVO

    if (empty($packageCode) || empty($paymentMethod)) {
        throw new Exception("Parameter tidak lengkap.");
    }

    $tokens = $_SESSION['xl_api_tokens'];
    $user_id = $_SESSION['user_id'];
    
    // 1. Dapatkan detail paket (untuk harga, nama, dan token konfirmasi)
    $package_details = get_package($tokens, $packageCode);
    $price = $package_details['package_option']['price'];
    $packageName = $package_details['package_option']['name'];
    $token_confirmation = $package_details['token_confirmation'];

    // 2. Dapatkan metode pembayaran (untuk token pembayaran)
    $payment_methods = get_payment_methods($tokens, $token_confirmation, $packageCode);
    $token_payment = $payment_methods['token_payment'];
    $ts_to_sign = $payment_methods['timestamp'];

    // 3. Lakukan settlement ke provider via E-Wallet
    $settlement_data = settlement_ewallet($tokens, $token_payment, $ts_to_sign, $packageCode, $price, $walletNumber, $packageName, $paymentMethod);
    
    // 4. Simpan transaksi ke database kita
    $transaction_id = 'PCHS-' . $user_id . '-' . time();
    $stmt = $pdo->prepare(
        "INSERT INTO transactions (user_id, transaction_id, type, description, amount, status, gateway_reference) 
         VALUES (?, ?, 'purchase', ?, ?, 'pending', ?)"
    );
    $stmt->execute([$user_id, $transaction_id, $packageName, $price, $settlement_data['transaction_id'] ?? null]);

    // 5. Kirim kembali respons ke frontend
    echo json_encode(['success' => true, 'data' => $settlement_data]);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}