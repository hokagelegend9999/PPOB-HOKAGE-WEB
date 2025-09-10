<?php
session_start();
header('Content-Type: application/json');

// Memuat "kamus" fungsi dari api_client.php
require_once __DIR__ . '/api_client.php';

try {
    if (!isset($_SESSION['xl_api_tokens'])) {
        throw new Exception("Sesi otentikasi XL tidak ditemukan. Silakan ulangi proses OTP.");
    }
    $tokens = $_SESSION['xl_api_tokens'];

    // ✅ 1. Mengganti define() dengan sebuah array Family Codes
    // Anda bisa menambah atau menghapus kode dari daftar ini sesuai kebutuhan.
    $family_codes = [
        "08a3b1e6-8e78-4e45-a540-b40f06871cfe", // Kode lama Anda untuk XUT
        "ae2707e6-2996-45f8-944a-8a391e956f28", // Kode lama Anda untuk XUT
        "6fda76ee-e789-4897-89fb-9114da47b805",
        "20342db0-e03e-4dfd-b2d0-cd315d7ddc36"// Contoh lain
    ];

    // Siapkan array kosong untuk menampung semua hasil paket
    $simplePackages = [];
    $processed_codes = []; // Untuk mencegah paket duplikat jika muncul di family code berbeda

    // ✅ 2. Membuat perulangan (loop) untuk setiap kode dalam array
    foreach ($family_codes as $code) {
        try {
            // ✅ 3. Memanggil API untuk setiap kode di dalam loop
            $rawPackageData = getPackagesByFamily($tokens, $code);

            // ✅ (Tambahan) Menangani 2 jenis struktur data dari API agar lebih kuat
            $options_list = [];
            if (isset($rawPackageData['package_options']) && is_array($rawPackageData['package_options'])) {
                $options_list = $rawPackageData['package_options'];
            } elseif (isset($rawPackageData['package_variants']) && is_array($rawPackageData['package_variants'])) {
                foreach ($rawPackageData['package_variants'] as $variant) {
                    if (isset($variant['package_options']) && is_array($variant['package_options'])) {
                        $options_list = array_merge($options_list, $variant['package_options']);
                    }
                }
            }
            
            // ✅ 4. Memproses dan menggabungkan hasil ke dalam array $simplePackages
            foreach ($options_list as $option) {
                // Filter agar tidak ada paket yang sama muncul dua kali
                if (!in_array($option['package_option_code'], $processed_codes)) {
                    $simplePackages[] = [
                        'name'  => $option['name'],
                        'price' => $option['price'],
                        'code'  => $option['package_option_code']
                    ];
                    $processed_codes[] = $option['package_option_code'];
                }
            }

        } catch (Exception $e) {
            // Jika satu kode gagal, catat di log server (opsional) dan lanjut ke kode berikutnya
            error_log("Gagal mengambil paket dari family code '$code': " . $e->getMessage());
            continue;
        }
    }

    // ✅ 5. Memberi pesan error jika semua kode gagal dan tidak ada paket yang ditemukan
    if (empty($simplePackages)) {
        throw new Exception("Gagal mengambil daftar paket. Token mungkin kedaluwarsa atau semua Family Code tidak valid.");
    }

    echo json_encode(['success' => true, 'packages' => $simplePackages]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>