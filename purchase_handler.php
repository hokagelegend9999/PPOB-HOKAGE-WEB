<?php
session_start();
header('Content-Type: application/json');
require_once 'db_connect.php'; // Diperlukan untuk cek saldo
require_once 'api_client.php';

// --- BAGIAN BARU: PENGECEKAN SALDO ---
define('MINIMUM_BALANCE', 10000);

try {
    // Pastikan user_id ada di sesi
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Sesi tidak valid. Silakan login kembali.");
    }
    $user_id = $_SESSION['user_id'];

    // Ambil saldo user dari database
    $stmt = $pdo->prepare("SELECT saldo FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("User tidak ditemukan.");
    }

    // Cek apakah saldo mencukupi
    if ($user['saldo'] < MINIMUM_BALANCE) {
        throw new Exception("Saldo Anda tidak mencukupi. Minimal saldo untuk bertransaksi adalah Rp " . number_format(MINIMUM_BALANCE, 0, ',', '.'));
    }
    // --- AKHIR BAGIAN PENGECEKAN SALDO ---


    // Jika saldo cukup, lanjutkan proses pembelian seperti biasa
    $input = json_decode(file_get_contents('php://input'), true);
    $packageCode = $input['package_code'] ?? null;
    if (empty($packageCode)) {
        throw new Exception("Kode paket tidak boleh kosong.");
    }

    $tokens = $_SESSION['xl_api_tokens'];
    $qr_code = purchasePackageWithQris($tokens, $packageCode);

    echo json_encode(['success' => true, 'qr_code' => $qr_code]);

} catch (Exception $e) {
    http_response_code(400); // Bad Request (karena kondisi tidak terpenuhi)
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}