<?php
// Alamat server API XL
$url = 'https://api.myxl.co.id';

echo "Mencoba koneksi ke: " . $url . "<br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Waktu tunggu 10 detik

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "<strong style='color:red;'>Koneksi Gagal!</strong><br>";
    echo "Pesan Error cURL: " . $error;
    echo "<br><br><b>Kesimpulan:</b> Masalah kemungkinan besar ada di sisi hosting Anda. Server Anda tidak bisa menghubungi server XL. Hubungi penyedia hosting Anda.";
} else {
    echo "<strong style='color:green;'>Koneksi Berhasil!</strong><br>";
    echo "Server XL merespons dengan Kode HTTP: " . $http_code;
    echo "<br><br><b>Kesimpulan:</b> Masalah bukan pada koneksi, melainkan 100% pada logika skrip API Anda (`api_client.php` atau `crypto_helper.php`) yang sudah tidak sinkron dengan server XL.";
}
?>