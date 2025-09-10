<?php
require_once 'config.php';
require_once 'db_connect.php';

// Ambil notifikasi dari Tripay
$json = file_get_contents("php://input");

// Validasi signature (PENTING UNTUK KEAMANAN)
$callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
$signature = hash_hmac('sha256', $json, TRIPAY_PRIVATE_KEY);

if ($callbackSignature !== $signature) {
    http_response_code(403);
    die("Invalid signature");
}

$data = json_decode($json, true);
$event = $_SERVER['HTTP_X_CALLBACK_EVENT'] ?? '';

if ($event == 'payment_status') {
    if ($data['status'] == 'PAID') {
        $merchantRef = $data['merchant_ref'];
        $tripayReference = $data['reference'];
        $totalAmount = $data['total_amount'];

        try {
            // Cari transaksi di database Anda
            $stmt = $pdo->prepare("SELECT id, user_id FROM transactions WHERE gateway_reference = ? AND transaction_status = 'pending'");
            $stmt->execute([$tripayReference]);
            $transaction = $stmt->fetch();

            if ($transaction) {
                // 1. Update status transaksi menjadi COMPLETED
                $updateStmt = $pdo->prepare("UPDATE transactions SET transaction_status = 'completed' WHERE id = ?");
                $updateStmt->execute([$transaction['id']]);

                // 2. Tambahkan saldo ke user
                $updateUserStmt = $pdo->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
                $updateUserStmt->execute([$totalAmount, $transaction['user_id']]);

                // Beri respons sukses ke Tripay
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Transaction not found or already processed.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
}
?>