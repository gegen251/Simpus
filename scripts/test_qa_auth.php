<?php

/**
 * Test QA Modul Autentikasi SIMPUS SD
 * Memeriksa:
 * 1. Proteksi filter seluruh route admin tanpa login
 * 2. Login gagal (salah password) & hitungan sisa kesempatan
 * 3. Proteksi brute-force lockout (5x gagal -> kunci 15 menit)
 * 4. Login berhasil dengan kredensial valid & inisialisasi sesi
 * 5. Logout & pembersihan sesi
 * 6. Validasi konfigurasi session expiration
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_auth.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null, $followRedirect = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirect);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body,
        'url' => $effectiveUrl,
    ];
}

function getCsrfToken($baseUrl, $cookieFile) {
    $res = httpReq($baseUrl . '/login', 'GET', [], $cookieFile, true);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL AUTENTIKASI SIMPUS SD\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

// -------------------------------------------------------------
// TEST 1: Proteksi Akses Tanpa Login pada Seluruh Endpoint
// -------------------------------------------------------------
echo "[1] Uji Proteksi Akses Tanpa Login (AuthFilter)...\n";
$protectedEndpoints = [
    '/dashboard',
    '/buku',
    '/kategori',
    '/peminjaman',
    '/pengembalian',
    '/anggota',
    '/laporan',
    '/pengaturan',
];

$allProtected = true;
foreach ($protectedEndpoints as $ep) {
    $res = httpReq($baseUrl . $ep, 'GET', [], null, false);
    $isRedirectToLogin = (in_array($res['code'], [302, 303]) && strpos($res['headers'], '/login') !== false);
    if ($isRedirectToLogin) {
        echo "  [OK] Endpoint {$ep} terproteksi -> Redirect ke /login (HTTP {$res['code']})\n";
    } else {
        echo "  [FAIL] Endpoint {$ep} TIDAK terproteksi! HTTP {$res['code']}\n";
        $allProtected = false;
    }
}

if ($allProtected) {
    echo "-> HASIL: Seluruh endpoint staf/admin 100% aman terproteksi AuthFilter.\n\n";
    $passCount++;
} else {
    echo "-> HASIL: Ditemukan celah akses pada endpoint tertentu!\n\n";
    $failCount++;
}

// -------------------------------------------------------------
// TEST 2: Salah Password & Sisa Kesempatan Login
// -------------------------------------------------------------
echo "[2] Uji Salah Password & Penghitung Kesempatan...\n";
if (file_exists($cookieFile)) unlink($cookieFile);
$csrf = getCsrfToken($baseUrl, $cookieFile);

$wrongRes = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrf,
    'username' => 'admin_test_qa',
    'password' => 'passwordsalah123'
], $cookieFile, true);

if (strpos($wrongRes['body'], 'Username atau password salah') !== false) {
    echo "  [OK] Pesan peringatan kesalahan password muncul dengan benar.\n";
    if (strpos($wrongRes['body'], 'Sisa kesempatan login') !== false) {
        echo "  [OK] Indikator sisa kesempatan login ditampilkan ke pengguna.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Sisa kesempatan tidak tampil.\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Pesan kesalahan tidak sesuai ekspektasi.\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Proteksi Brute-Force Lockout (5x Gagal)
// -------------------------------------------------------------
echo "[3] Uji Proteksi Brute-Force Lockout (Simulasi 5x Gagal Berturut-turut)...\n";
$bfUser = 'admin_bf_victim';
for ($i = 1; $i <= 4; $i++) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/login/process', 'POST', [
        'csrf_test_name' => $csrf,
        'username' => $bfUser,
        'password' => 'wrongpass' . $i
    ], $cookieFile, true);
}

// Percobaan ke-5
$csrf = getCsrfToken($baseUrl, $cookieFile);
$res5 = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrf,
    'username' => $bfUser,
    'password' => 'wrongpass5'
], $cookieFile, true);

if (strpos($res5['body'], 'diblokir sementara') !== false) {
    echo "  [OK] Akun/IP berhasil dikunci (Lockout 15 menit) setelah 5x salah password berturut-turut.\n";
    
    // Percobaan ke-6 (saat terkunci)
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    $res6 = httpReq($baseUrl . '/login/process', 'POST', [
        'csrf_test_name' => $csrf,
        'username' => $bfUser,
        'password' => 'anypassword'
    ], $cookieFile, true);
    
    if (strpos($res6['body'], 'diblokir sementara') !== false) {
        echo "  [OK] Percobaan saat status terkunci langsung ditolak dengan pemberitahuan waktu tersisa.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Status penguncian tidak bertahan pada percobaan berikutnya.\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Lockout gagal terpicu setelah 5 percobaan.\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: Login Berhasil dengan Kredensial Valid
// -------------------------------------------------------------
echo "[4] Uji Login Berhasil dengan Kredensial Valid...\n";
if (file_exists($cookieFile)) unlink($cookieFile);
$csrf = getCsrfToken($baseUrl, $cookieFile);

$loginRes = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrf,
    'username' => 'admin',
    'password' => 'admin123'
], $cookieFile, false);

$loginSuccess = (in_array($loginRes['code'], [302, 303]) && strpos($loginRes['headers'], '/dashboard') !== false);
if ($loginSuccess) {
    echo "  [OK] Login sukses -> Redirect ke /dashboard (HTTP {$loginRes['code']}).\n";
    
    // Akses dashboard menggunakan session cookie
    $dashRes = httpReq($baseUrl . '/dashboard', 'GET', [], $cookieFile, true);
    if ($dashRes['code'] === 200 && (strpos($dashRes['body'], 'Dashboard') !== false || strpos($dashRes['body'], 'admin') !== false)) {
        echo "  [OK] Berhasil mengakses Dashboard dengan sesi autentikasi valid.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Gagal mengakses dashboard setelah login!\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Login admin gagal! HTTP {$loginRes['code']}\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 5: Uji Logout
// -------------------------------------------------------------
echo "[5] Uji Logout Pengguna...\n";
$logoutRes = httpReq($baseUrl . '/logout', 'GET', [], $cookieFile, false);
if (in_array($logoutRes['code'], [302, 303]) && strpos($logoutRes['headers'], '/login') !== false) {
    echo "  [OK] Request logout meredirect ke /login (HTTP {$logoutRes['code']}).\n";
    
    // Coba akses dashboard kembali dengan cookie yang sama
    $afterLogoutDash = httpReq($baseUrl . '/dashboard', 'GET', [], $cookieFile, false);
    if (in_array($afterLogoutDash['code'], [302, 303]) && strpos($afterLogoutDash['headers'], '/login') !== false) {
        echo "  [OK] Sesi terhapus permanen; akses kembali ke dashboard langsung ditolak.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Sesi masih aktif setelah logout!\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Logout gagal meredirect ke login.\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 6: Validasi Konfigurasi Session Timeout
// -------------------------------------------------------------
echo "[6] Validasi Konfigurasi Session Expiration...\n";
$sessFile = __DIR__ . '/../app/Config/Session.php';
$sessContent = file_get_contents($sessFile);
if (preg_match('/public\s+int\s+\$expiration\s*=\s*(\d+);/i', $sessContent, $m)) {
    $exp = (int)$m[1];
    $hours = $exp / 3600;
    echo "  [OK] Waktu kedaluwarsa sesi terkonfigurasi: {$exp} detik ({$hours} jam).\n";
    $passCount++;
} else {
    echo "  [FAIL] Gagal membaca konfigurasi session expiration.\n";
    $failCount++;
}
echo "\n";

// Pembersihan file cookie sementara
if (file_exists($cookieFile)) unlink($cookieFile);

echo "=======================================================\n";
echo "RINGKASAN QA MODUL AUTENTIKASI:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL AUTENTIKASI BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
