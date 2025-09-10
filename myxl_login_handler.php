<?php
session_start();
require_once 'db_connect.php';
require_once 'api_client.php';

// Cek apakah user (reseller) sudah login ke website
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? null;

    switch ($action) {
        case 'request_otp':
            $phoneNumber = $input['phone_number'] ?? null;
            if (!$phoneNumber) throw new Exception("Nomor telepon pelanggan tidak boleh kosong.");
            
            // Simpan nomor pelanggan di sesi untuk langkah selanjutnya
            $_SESSION['customer_phone_number'] = $phoneNumber;
            
            request_otp($phoneNumber);
            echo json_encode(['success' => true, 'message' => 'OTP berhasil dikirim ke ' . $phoneNumber]);
            break;

        case 'submit_otp':
            $otpCode = $input['otp_code'] ?? null;
            $customerPhoneNumber = $_SESSION['customer_phone_number'] ?? null;
            if (!$otpCode || !$customerPhoneNumber) throw new Exception("Sesi nomor pelanggan tidak ditemukan atau OTP kosong.");
            
            $tokens = submit_otp($customerPhoneNumber, $otpCode);
            
            if (isset($tokens['refresh_token'])) {
                // SIMPAN TOKEN KE SESI, BUKAN DATABASE
                $_SESSION['xl_api_tokens'] = $tokens;
                echo json_encode(['success' => true, 'message' => 'Otentikasi berhasil!']);
            } else {
                throw new Exception("Gagal mendapatkan refresh token dari server.");
            }
            break;
            
        case 'check_status':
            // Cek apakah ada token XL yang tersimpan di sesi
            if (isset($_SESSION['xl_api_tokens']) && !empty($_SESSION['xl_api_tokens'])) {
                 echo json_encode(['success' => true, 'status' => 'session_active']);
            } else {
                 echo json_encode(['success' => true, 'status' => 'no_session']);
            }
            break;

        default:
            throw new Exception("Aksi tidak valid.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>