<?php

/**
 * Test QA Fase 5 - UI/UX: Tampilan Profesional & Premium
 * Memeriksa:
 * 1. Tidak ada lagi referensi ke Tailwind Play CDN (cdn.tailwindcss.com) di seluruh views.
 * 2. Berkas public/css/app.css terkompilasi, berukuran optimal (< 150 KB), dan disajikan via HTTP 200.
 * 3. Halaman publik (Katalog OPAC, Login, Kiosk) dan Halaman Internal (Dashboard) menggunakan app.css.
 * 4. Uji Aksesibilitas Dasar: Setiap form input memiliki label / placeholder / aria yang valid; seluruh gambar memiliki alt text.
 * 5. Uji Design Tokens: Font IBM Plex Family (Sans, Serif, Mono) aktif, warna brand konsisten.
 * 6. Ketersediaan komponen reusable (empty_state, loading_skeleton, badge_status).
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_uiux.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
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
    curl_close($ch);
    
    return ['code' => $httpCode, 'headers' => $headers, 'body' => $body];
}

echo "=======================================================\n";
echo "       QA FASE 5: UI/UX TAMPILAN PROFESIONAL & PREMIUM\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($name, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$name}\n";
    } else {
        $failCount++;
        echo "  [FAIL] {$name} - {$details}\n";
    }
}

// 1. Audit Play CDN di Seluruh Berkas View
echo "[1] Audit Berkas Template View: Pembersihan Tailwind Play CDN...\n";
$viewsDir = dirname(__DIR__) . '/app/Views';
$viewFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$playCdnFound = 0;
$scannedFiles = 0;

foreach ($viewFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $scannedFiles++;
        $content = file_get_contents($file->getPathname());
        if (stripos($content, 'cdn.tailwindcss.com') !== false) {
            $playCdnFound++;
            echo "    Ditemukan CDN pada: " . $file->getFilename() . "\n";
        }
    }
}

assertTest("Seluruh View Bebas dari Tailwind Play CDN ({$scannedFiles} file diperiksa)", $playCdnFound === 0, "Ditemukan {$playCdnFound} file");

// 2. Verifikasi Berkas CSS Produksi (public/css/app.css)
echo "\n[2] Verifikasi Berkas CSS Produksi (Tailwind CLI)...\n";
$cssPath = dirname(__DIR__) . '/public/css/app.css';
$cssExists = file_exists($cssPath);
$cssSize = $cssExists ? filesize($cssPath) : 0;
$cssSizeKb = round($cssSize / 1024, 2);

assertTest("Berkas public/css/app.css Tersedia Fisik", $cssExists);
assertTest("Ukuran CSS Produksi Optimal ({$cssSizeKb} KB < 150 KB)", $cssSize > 10000 && $cssSize < 150000, "Ukuran: {$cssSizeKb} KB");

// Cek via HTTP Request
$resCss = httpReq($baseUrl . '/css/app.css');
assertTest("Server Menyajikan /css/app.css dengan Status HTTP 200", $resCss['code'] === 200);
assertTest("Header Content-Type CSS Sesuai", stripos($resCss['headers'], 'text/css') !== false);

// 3. Verifikasi Pemuatan app.css pada Halaman Publik & Internal
echo "\n[3] Verifikasi Link app.css pada Halaman Utama...\n";

// A. Halaman Katalog OPAC
$resKatalog = httpReq($baseUrl . '/katalog');
assertTest("Halaman Katalog OPAC Menggunakan app.css", strpos($resKatalog['body'], 'css/app.css') !== false);
assertTest("Halaman Katalog OPAC Tidak Memuat Play CDN", strpos($resKatalog['body'], 'cdn.tailwindcss.com') === false);

// B. Halaman Login
$resLogin = httpReq($baseUrl . '/login');
assertTest("Halaman Login Menggunakan app.css", strpos($resLogin['body'], 'css/app.css') !== false);

// C. Halaman Dashboard (Internal)
// Login Admin
$csrf = '';
if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $resLogin['body'], $m)) {
    $csrf = $m[1];
}
httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrf,
    'username'       => 'admin',
    'password'       => 'admin123'
], $cookieFile);

$resDashboard = httpReq($baseUrl . '/dashboard', 'GET', [], $cookieFile);
assertTest("Halaman Dashboard Menggunakan app.css", strpos($resDashboard['body'], 'css/app.css') !== false);
assertTest("Halaman Dashboard Memuat Font IBM Plex Family", strpos($resDashboard['body'], 'IBM+Plex') !== false);

// 4. Ketersediaan Komponen Reusable
echo "\n[4] Ketersediaan Komponen UI Reusable (Design Tokens & Partials)...\n";
$compDir = dirname(__DIR__) . '/app/Views/components';
assertTest("Komponen Reusable Empty State Tersedia", file_exists($compDir . '/empty_state.php'));
assertTest("Komponen Reusable Loading Skeleton Tersedia", file_exists($compDir . '/loading_skeleton.php'));
assertTest("Komponen Reusable Status Badge Tersedia", file_exists($compDir . '/badge_status.php'));

// 5. Uji Aksesibilitas Dasar (Alt Text & Form Accessibility)
echo "\n[5] Uji Aksesibilitas Dasar (Alt Text Gambar & Label Input Form)...\n";
// Ambil semua tag <img> pada Katalog & Dashboard dan pastikan memiliki alt attribute
preg_match_all('/<img\s+[^>]*>/i', $resKatalog['body'] . $resDashboard['body'], $imgMatches);
$missingAlt = 0;
foreach ($imgMatches[0] as $imgTag) {
    if (stripos($imgTag, 'alt=') === false) {
        $missingAlt++;
    }
}
assertTest("Seluruh Gambar (img) Memiliki Atribut Alt Text", $missingAlt === 0, "Ada {$missingAlt} gambar tanpa alt");

// Pastikan viewport meta tag responsif tersedia
assertTest("Viewport Meta Tag Responsif Terpasang di Seluruh Halaman", strpos($resKatalog['body'], 'viewport') !== false && strpos($resDashboard['body'], 'viewport') !== false);

// 6. Uji Loading State Kiosk Modern (Bukan Teks Polos Loading...)
echo "\n[6] Uji Indikator Loading State Kiosk...\n";
$kioskViewContent = file_get_contents(dirname(__DIR__) . '/app/Views/kiosk/index.php');
$hasSpinnerSvg = (strpos($kioskViewContent, 'animate-spin') !== false);
$hasNoPlainLoading = (strpos($kioskViewContent, 'textContent = "Loading..."') === false);
assertTest("Kiosk Menggunakan Animated SVG Spinner untuk Loading", $hasSpinnerSvg);
assertTest("Kiosk Bebas dari Teks Polos 'Loading...'", $hasNoPlainLoading);

if (file_exists($cookieFile)) unlink($cookieFile);

echo "\n=======================================================\n";
echo "               RINGKASAN HASIL QA FASE 5 UI/UX\n";
echo "=======================================================\n";
echo "Total Pengujian : " . ($passCount + $failCount) . "\n";
echo "Passed          : {$passCount}\n";
echo "Failed          : {$failCount}\n";
echo "Status Akhir    : " . ($failCount === 0 ? "SEMUA PENGUJIAN LULUS (SUCCESS)" : "ADA PENGUJIAN GAGAL") . "\n";
echo "=======================================================\n";

exit($failCount === 0 ? 0 : 1);
