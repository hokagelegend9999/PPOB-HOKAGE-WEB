<?php
// Untuk keamanan, letakkan file ini satu level di atas folder root web Anda jika memungkinkan.
// Di Hestia, Anda bisa meletakkannya di /home/USER/web/domain.com/ (di luar public_html).
// Jika diletakkan di dalam public_html, pastikan server dikonfigurasi untuk tidak menampilkannya.

// API Key Anda
define('API_KEY', '5ef27d6e-1dbf-42cc-9b44-8a9a7f8c8251');

// Lokasi file untuk menyimpan refresh token (harus bisa ditulis oleh server web)
define('TOKENS_FILE', __DIR__ . '/refresh-tokens.json');

// Lokasi file untuk menyimpan sesi aktif (harus bisa ditulis oleh server web)
define('SESSION_FILE', __DIR__ . '/session.json');

// URL API Kriptografi
define('CRYPTO_API_BASE_URL', 'https://crypto.mashu.lol/api');

// URL API myXL
define('MYXL_API_BASE_URL', 'https://api.myxl.xlaxiata.co.id');
define('MYXL_AUTH_BASE_URL', 'https://gede.ciam.xlaxiata.co.id/realms/xl-ciam');

    // Nanti diisi dengan kredensial production
define('TRIPAY_API_URL', 'https://tripay.co.id/api');
define('TRIPAY_API_KEY', 'safEJ03IxOxRkpYp9jWMw1ylXL6FbTQDuA1Z81ma');
define('TRIPAY_PRIVATE_KEY', 'y9aKf-1Qnht-MRPjN-vYAou-IAd9J');
define('TRIPAY_MERCHANT_CODE', 'T45045');

// Kunci untuk Google Login
define('GOOGLE_CLIENT_ID', '157963873584-l9lue2dsjv77uij2s5fkvaiqbf222h15.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URL', 'https://hokagelegend.web.id/google-callback.php');

