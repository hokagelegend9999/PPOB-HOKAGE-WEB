<?php
require_once 'config.php';

/**
 * Fungsi dasar untuk membuat request ke API Kripto.
 */
function make_crypto_request($endpoint, $body) {
    $url = CRYPTO_API_BASE_URL . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'x-api-key: ' . API_KEY
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception("Crypto API request failed for {$endpoint} with code {$http_code}: {$response}");
    }
    return json_decode($response, true);
}

/**
 * Padanan `encryptsign_xdata`
 */
function encryptsign_xdata($method, $path, $id_token, $payload) {
    $request_body = [ "id_token" => $id_token, "method" => $method, "path" => $path, "body" => $payload ];
    return make_crypto_request('/encryptsign', $request_body);
}

/**
 * Padanan `decrypt_xdata`
 */
function decrypt_xdata($encrypted_payload) {
    $result = make_crypto_request('/decrypt', $encrypted_payload);
    return $result['plaintext'] ?? null;
}

/**
 * Padanan `get_x_signature_payment`
 */
function get_x_signature_payment($access_token, $sig_time_sec, $package_code, $token_payment, $payment_method) {
    $request_body = [
        "access_token" => $access_token, "sig_time_sec" => (int)$sig_time_sec,
        "package_code" => $package_code, "token_payment" => $token_payment,
        "payment_method" => $payment_method
    ];
    $result = make_crypto_request('/sign-payment', $request_body);
    return $result['x_signature'] ?? null;
}

/**
 * Padanan `build_encrypted_field` (satu-satunya fungsi kripto lokal).
 */
function build_encrypted_field($urlsafe_b64 = false) {
    $key = "5dccbf08920a5527";
    $iv_hex = bin2hex(random_bytes(8));
    $iv = $iv_hex;
    $plaintext = str_pad('', 16, "\0");
    $ciphertext = openssl_encrypt($plaintext, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    if ($urlsafe_b64) {
        $encoded_ct = rtrim(strtr(base64_encode($ciphertext), '+/', '-_'), '=');
    } else {
        $encoded_ct = base64_encode($ciphertext);
    }
    return $encoded_ct . $iv_hex;
}

/**
 * Padanan `java_like_timestamp`.
 */
function java_like_timestamp() {
    $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $microseconds = substr($now->format('u'), 0, 2);
    return $now->format('Y-m-d\TH:i:s.') . $microseconds . $now->format('P');
}

/**
 * Helper untuk membuat UUID v4.
 */
function generate_uuid_v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
function ax_api_signature($ts_for_sign, $contact, $code, $contact_type) {
    $request_body = [
        "ts_for_sign" => $ts_for_sign,
        "contact" => $contact,
        "code" => $code,
        "contact_type" => $contact_type
    ];
    $result = make_crypto_request('/sign-ax', $request_body);
    return $result['ax_signature'] ?? null;
}
