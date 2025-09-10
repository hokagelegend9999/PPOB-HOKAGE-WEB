<?php
// Ganti seluruh isi file api_client.php Anda dengan ini

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/crypto_helper.php';
require_once __DIR__ . '/db_connect.php';

function get_new_token($refresh_token) {
    $url = MYXL_AUTH_BASE_URL . "/protocol/openid-connect/token";
    $headers = [
        'Host: gede.ciam.xlaxiata.co.id',
        'authorization: Basic OWZjOTdlZDEtNmEzMC00OGQ1LTk1MTYtNjBjNTNjZTNhMTM1OllEV21GNExKajlYSUt3UW56eTJlMmxiMHRKUWIyOW8z',
        'user-agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)',
        'content-type: application/x-www-form-urlencoded'
    ];
    $data = http_build_query(['grant_type' => 'refresh_token', 'refresh_token' => $refresh_token]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); $response = curl_exec($ch); curl_close($ch);
    $result = json_decode($response, true);
    if (isset($result['error'])) {
        throw new Exception("Gagal mendapatkan token baru: " . ($result['error_description'] ?? 'Error tidak diketahui'));
    }
    return $result;
}

function request_otp($contact) {
    $url = MYXL_AUTH_BASE_URL . "/auth/otp?" . http_build_query([
        'contact' => $contact, 'contactType' => 'SMS', 'alternateContact' => 'false'
    ]);
    $headers = [
        'Host: gede.ciam.xlaxiata.co.id',
        'Authorization: Basic OWZjOTdlZDEtNmEzMC00OGQ1LTk1MTYtNjBjNTNjZTNhMTM1OllEV21GNExKajlYSUt3UW56eTJlMmxiMHRKUWIyOW8z',
        'Ax-Device-Id: 92fb44c0804233eb4d9e29f838223a14',
        'Ax-Fingerprint: YmQLy9ZiLLBFAEVcI4Dnw9+NJWZcdGoQyewxMF/9hbfk/8GbKBgtZxqdiiam8+m2lK31E/zJQ7kjuPXpB3EE8naYL0Q8+0WLhFV1WAPl9Eg=',
        'Ax-Request-At: ' . java_like_timestamp(), 'Ax-Request-Device: samsung',
        'Ax-Request-Device-Model: SM-N935F', 'Ax-Request-Id: ' . generate_uuid_v4(),
        'Ax-Substype: PREPAID', 'User-Agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); $response = curl_exec($ch); curl_close($ch);
    $result = json_decode($response, true);
    if (!isset($result['subscriber_id'])) {
        throw new Exception($result['message'] ?? ($result['error'] ?? 'Gagal meminta OTP.'));
    }
    return $result['subscriber_id'];
}

function submit_otp($contact, $code) {
    $url = MYXL_AUTH_BASE_URL . "/protocol/openid-connect/token";
    $now_gmt7 = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $ts_for_sign = $now_gmt7->format('Y-m-d\TH:i:s.') . substr($now_gmt7->format('u'), 0, 3) . $now_gmt7->format('O');
    $ts_for_header = (clone $now_gmt7)->modify('-5 minutes')->format('Y-m-d\TH:i:s.') . substr($now_gmt7->format('u'), 0, 3) . $now_gmt7->format('O');
    $signature = ax_api_signature($ts_for_sign, $contact, $code, "SMS");
    $payload = http_build_query([
        'contactType' => 'SMS', 'code' => $code, 'grant_type' => 'password',
        'contact' => $contact, 'scope' => 'openid'
    ]);
    $headers = [
        'Host: gede.ciam.xlaxiata.co.id',
        'Authorization: Basic OWZjOTdlZDEtNmEzMC00OGQ1LTk1MTYtNjBjNTNjZTNhMTM1OllEV21GNExKajlYSUt3UW56eTJlMmxiMHRKUWIyOW8z',
        'Ax-Api-Signature: ' . $signature,
        'Ax-Device-Id: 92fb44c0804233eb4d9e29f838223a14',
        'Ax-Fingerprint: YmQLy9ZiLLBFAEVcI4Dnw9+NJWZcdGoQyewxMF/9hbfk/8GbKBgtZxqdiiam8+m2lK31E/zJQ7kjuPXpB3EE8naYL0Q8+0WLhFV1WAPl9Eg=',
        'Ax-Request-At: ' . $ts_for_header, 'Ax-Request-Device: samsung',
        'Ax-Request-Device-Model: SM-N935F', 'Ax-Request-Id: ' . generate_uuid_v4(),
        'Ax-Substype: PREPAID', 'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); $response = curl_exec($ch); curl_close($ch);
    $result = json_decode($response, true);
    if (isset($result['error'])) {
        throw new Exception($result['error_description'] ?? 'OTP Salah atau tidak valid.');
    }
    return $result;
}

function send_api_request($path, $payloadDict, $id_token, $method = "POST") {
    $encrypted = encryptsign_xdata($method, $path, $id_token, $payloadDict);
    $body = $encrypted["encrypted_body"]; $sigTimeSec = floor($encrypted["encrypted_body"]["xtime"] / 1000);
    $headers = [
        'host: ' . parse_url(MYXL_API_BASE_URL, PHP_URL_HOST), 'content-type: application/json; charset=utf-8',
        'user-agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)',
        'x-api-key: ' . API_KEY, 'authorization: Bearer ' . $id_token, 'x-hv: v3',
        'x-signature-time: ' . $sigTimeSec, 'x-signature: ' . $encrypted["x_signature"],
        'x-request-id: ' . generate_uuid_v4(), 'x-request-at: ' . java_like_timestamp(),
        'x-version-app: 8.6.0'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MYXL_API_BASE_URL . '/' . $path); curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body)); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); $response = curl_exec($ch); curl_close($ch);
    return decrypt_xdata(json_decode($response, true));
}

function get_package($tokens, $package_option_code) {
    $path = "api/v8/xl-stores/options/detail";
    $payload = ["package_option_code" => $package_option_code, "lang" => "en"];
    $result = send_api_request($path, $payload, $tokens["id_token"]);
    if (!isset($result['data'])) throw new Exception("Gagal mendapatkan detail paket: " . ($result['error']['message'] ?? 'Error tidak diketahui'));
    return $result['data'];
}

function get_payment_methods($tokens, $token_confirmation, $payment_target) {
    $path = "payments/api/v8/payment-methods-option";
    $payload = [
        "payment_type" => "PURCHASE", "is_enterprise" => false, "payment_target" => $payment_target,
        "lang" => "en", "is_referral" => false, "token_confirmation" => $token_confirmation
    ];
    $result = send_api_request($path, $payload, $tokens["id_token"]);
    if (($result['status'] ?? '') !== 'SUCCESS') throw new Exception("Gagal mendapatkan metode pembayaran.");
    return $result['data'];
}

function settlement_qris($tokens, $token_payment, $ts_to_sign, $payment_target, $price, $item_name = "") {
    $path = "payments/api/v8/settlement-multipayment/qris";
    $payload = [
        "akrab" => ["akrab_members" => [], "akrab_parent_alias" => "", "members" => []],
        "can_trigger_rating" => false, "total_discount" => 0, "coupon" => "",
        "payment_for" => "BUY_PACKAGE", "topup_number" => "", "is_enterprise" => false,
        "autobuy" => ["is_using_autobuy" => false, "activated_autobuy_code" => "", "autobuy_threshold_setting" => ["label" => "", "type" => "", "value" => 0]],
        "access_token" => $tokens["access_token"], "is_myxl_wallet" => false,
        "additional_data" => ["original_price" => $price, "is_spend_limit_temporary" => false, "migration_type" => "", "spend_limit_amount" => 0, "is_spend_limit" => false, "tax" => 0, "benefit_type" => "", "quota_bonus" => 0, "cashtag" => "", "is_family_plan" => false, "combo_details" => [], "is_switch_plan" => false, "discount_recurring" => 0, "has_bonus" => false, "discount_promo" => 0],
        "total_amount" => $price, "total_fee" => 0, "is_use_point" => false, "lang" => "en",
        "items" => [[ "item_code" => $payment_target, "product_type" => "", "item_price" => $price, "item_name" => $item_name, "tax" => 0 ]],
        "verification_token" => $token_payment, "payment_method" => "QRIS", "timestamp" => time()
    ];
    $encrypted = encryptsign_xdata("POST", $path, $tokens["id_token"], $payload);
    $body = $encrypted["encrypted_body"]; $sigTimeSec = floor($encrypted["encrypted_body"]["xtime"] / 1000);
    $x_sig = get_x_signature_payment($tokens["access_token"], $ts_to_sign, $payment_target, $token_payment, "QRIS");
    $headers = [
        'host: ' . parse_url(MYXL_API_BASE_URL, PHP_URL_HOST), 'content-type: application/json; charset=utf-8',
        'user-agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)',
        'x-api-key: ' . API_KEY, 'authorization: Bearer ' . $tokens['id_token'], 'x-hv: v3',
        'x-signature-time: ' . $sigTimeSec, 'x-signature: ' . $x_sig,
        'x-request-id: ' . generate_uuid_v4(), 'x-request-at: ' . java_like_timestamp(), 'x-version-app: 8.6.0',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MYXL_API_BASE_URL . '/' . $path); curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body)); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); $response = curl_exec($ch); curl_close($ch);
    $decrypted = decrypt_xdata(json_decode($response, true));
    if (($decrypted['status'] ?? '') !== 'SUCCESS') {
        $errorMessage = $decrypted['error']['message'] ?? 'Gagal settlement QRIS.'; throw new Exception($errorMessage);
    }
    return $decrypted['data']['transaction_code'];
}

function get_qris_code($tokens, $transaction_id) {
    $path = "payments/api/v8/pending-detail";
    $payload = ["transaction_id" => $transaction_id, "lang" => "en"];
    $result = send_api_request($path, $payload, $tokens["id_token"]);
    if (($result['status'] ?? '') !== 'SUCCESS') throw new Exception("Gagal mengambil QR Code.");
    return $result['data']['qr_code'];
}

function purchasePackageWithQris($tokens, $package_option_code) {
    global $pdo;
    if (session_status() === PHP_SESSION_NONE) session_start();
    $package_details = get_package($tokens, $package_option_code);
    $price = $package_details['package_option']['price'];
    $packageName = $package_details['package_option']['name'];
    $token_confirmation = $package_details['token_confirmation'];
    $payment_methods = get_payment_methods($tokens, $token_confirmation, $package_option_code);
    $token_payment = $payment_methods['token_payment'];
    $ts_to_sign = $payment_methods['timestamp'];
    $transaction_id = settlement_qris($tokens, $token_payment, $ts_to_sign, $package_option_code, $price, $packageName);
    $qr_code_data = get_qris_code($tokens, $transaction_id);
    if ($qr_code_data) {
        $user_id = $_SESSION['user_id'];
        $developer_fee = 2000;
        $stmt_deduct = $pdo->prepare("UPDATE users SET saldo = saldo - ? WHERE id = ?");
        $stmt_deduct->execute([$developer_fee, $user_id]);
        // ✅ Query untuk mencatat biaya jasa (BENAR)
$stmt_log_fee = $pdo->prepare(
    "INSERT INTO transactions (user_id, transaction_id, type, description, amount, status) 
     VALUES (?, ?, 'purchase', ?, ?, 'completed')"
);
$stmt_log_fee->execute([$user_id, 'FEE-' . time(), 'Biaya Jasa Pembelian: ' . $packageName, -$developer_fee]);

// ✅ Query untuk mencatat pembelian (BENAR)
$stmt_log_purchase = $pdo->prepare(
    "INSERT INTO transactions (user_id, transaction_id, type, description, amount, status, gateway_reference) 
     VALUES (?, ?, 'purchase', ?, ?, 'pending', ?)"
);
// BENAR: Sekarang ada 5 data untuk 5 placeholder (?)
$stmt_log_purchase->execute([$user_id, 'PCHS-' . time(), $packageName, $price, $transaction_id]);
    }
    return $qr_code_data;
}

function getPackagesByFamily($tokens, $family_code) {
    // --- KODE DEBUGGING BARU ---
    // Baris ini akan mencatat isi dari token yang kita terima dari Sesi
    error_log("Tokens being used for package request: " . json_encode($tokens));
    // --- AKHIR KODE DEBUGGING ---

    $path = "api/v8/xl-stores/options/list";
    $payload = [
        "package_family_code" => $family_code,
        "is_transaction_routine" => false,
        "is_enterprise" => false,
        "lang" => "en"
    ];
    
    // Kirim permintaan ke API myXL
    $result = send_api_request($path, $payload, $tokens["id_token"]);

    // --- KODE DEBUGGING LAMA (TETAP BIARKAN) ---
    error_log("===== GET PACKAGES DEBUG =====");
    error_log("Response from myXL for package list: " . json_encode($result, JSON_PRETTY_PRINT));
    error_log("===== END GET PACKAGES DEBUG =====");
    // --- AKHIR KODE DEBUGGING ---

    if (($result['status'] ?? '') !== 'SUCCESS' || !isset($result['data'])) {
        $errorMessage = $result['error']['message'] ?? 'Gagal mengambil daftar paket dari family code.';
        throw new Exception($errorMessage);
    }
    
    return $result['data'];
}
// ✅ FUNGSI BARU UNTUK SETTLEMENT E-WALLET
function settlement_ewallet($tokens, $token_payment, $ts_to_sign, $payment_target, $price, $wallet_number, $item_name, $payment_method) {
    $path = "payments/api/v8/settlement-multipayment/ewallet";
    
    $payload = [
        "akrab" => ["akrab_members" => [], "akrab_parent_alias" => "", "members" => []],
        "can_trigger_rating" => false, "total_discount" => 0, "coupon" => "",
        "payment_for" => "BUY_PACKAGE", "topup_number" => "", "is_enterprise" => false,
        "autobuy" => [
            "is_using_autobuy" => false, "activated_autobuy_code" => "",
            "autobuy_threshold_setting" => ["label" => "", "type" => "", "value" => 0]
        ],
        "cc_payment_type" => "", "access_token" => $tokens["access_token"],
        "is_myxl_wallet" => false, "wallet_number" => $wallet_number,
        "additional_data" => [
            "original_price" => $price, "is_spend_limit_temporary" => false, "migration_type" => "",
            "spend_limit_amount" => 0, "is_spend_limit" => false, "tax" => 0, "benefit_type" => "",
            "quota_bonus" => 0, "cashtag" => "", "is_family_plan" => false, "combo_details" => [],
            "is_switch_plan" => false, "discount_recurring" => 0, "has_bonus" => false, "discount_promo" => 0
        ],
        "total_amount" => $price, "total_fee" => 0, "is_use_point" => false, "lang" => "en",
        "items" => [[
            "item_code" => $payment_target, "product_type" => "", "item_price" => $price,
            "item_name" => $item_name, "tax" => 0
        ]],
        "verification_token" => $token_payment, "payment_method" => $payment_method,
        "timestamp" => time()
    ];

    $encrypted = encryptsign_xdata("POST", $path, $tokens["id_token"], $payload);
    $body = $encrypted["encrypted_body"];
    $sigTimeSec = floor($encrypted["encrypted_body"]["xtime"] / 1000);
    $x_sig = get_x_signature_payment($tokens["access_token"], $ts_to_sign, $payment_target, $token_payment, $payment_method);
    
    $headers = [
        'host: ' . parse_url(MYXL_API_BASE_URL, PHP_URL_HOST), 'content-type: application/json; charset=utf-8',
        'user-agent: myXL / 8.6.0(1179); com.android.vending; (samsung; SM-N935F; SDK 33; Android 13)',
        'x-api-key: ' . API_KEY, 'authorization: Bearer ' . $tokens['id_token'], 'x-hv: v3',
        'x-signature-time: ' . $sigTimeSec, 'x-signature: ' . $x_sig,
        'x-request-id: ' . generate_uuid_v4(), 'x-request-at: ' . java_like_timestamp(), 'x-version-app: 8.6.0',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MYXL_API_BASE_URL . '/' . $path);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $decrypted = decrypt_xdata(json_decode($response, true));
    if (($decrypted['status'] ?? '') !== 'SUCCESS') {
        $errorMessage = $decrypted['error']['message'] ?? 'Gagal settlement E-Wallet.';
        throw new Exception($errorMessage);
    }
    return $decrypted['data'];
}