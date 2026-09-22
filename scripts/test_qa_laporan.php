<?php

/**
 * Test QA Modul Laporan SIMPUS SD
 * Memeriksa:
 * 1. Export 4 Jenis Laporan (semua, peminjaman, pengembalian, buku) ke 3 Format (PDF, Word, Excel).
 * 2. Integritas File (PDF signature %PDF-, Word Content-Type & HTML, Excel Content-Type & HTML).
 * 3. Filter rentang tanggal (start_date, end_date), status sirkulasi, dan kategori buku.
 * 4. Penanganan rentang tanggal kosong (empty result) tanpa error 500.
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_laporan.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
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
echo "       QA MODUL LAPORAN SIMPUS SD (EXPORT MULTI-FORMAT)\n";
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

// 1. Login sebagai Administrator
echo "[1] Otentikasi Admin untuk Akses Laporan...\n";
$loginPage = httpReq($baseUrl . '/login', 'GET', [], $cookieFile);
preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $loginPage['body'], $csrfMatch);
$csrf = $csrfMatch[1] ?? '';

$loginRes = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrf,
    'username'       => 'admin',
    'password'       => 'admin123'
], $cookieFile);

assertTest("Login Admin Berhasil", $loginRes['code'] === 200 && strpos($loginRes['body'], 'Dashboard') !== false);

// 2. Pengujian 12 Kombinasi Export (4 Jenis x 3 Format)
echo "\n[2] Uji Seluruh 12 Kombinasi Export Laporan (Standar/Default)...\n";

$jenisList = ['semua', 'peminjaman', 'pengembalian', 'buku'];
$formats = [
    'pdf' => [
        'path'        => '/laporan/pdf/',
        'mime'        => 'application/pdf',
        'ext'         => '.pdf',
        'check'       => function($body) {
            return substr($body, 0, 4) === '%PDF';
        }
    ],
    'word' => [
        'path'        => '/laporan/word/',
        'mime'        => 'application/vnd.ms-word',
        'ext'         => '.doc',
        'check'       => function($body) {
            return (strpos($body, '<html') !== false || strpos($body, '<table') !== false) && strlen($body) > 300;
        }
    ],
    'excel' => [
        'path'        => '/laporan/excel/',
        'mime'        => 'application/vnd.ms-excel',
        'ext'         => '.xls',
        'check'       => function($body) {
            return (strpos($body, '<html') !== false || strpos($body, '<table') !== false) && strlen($body) > 300;
        }
    ]
];

foreach ($jenisList as $jenis) {
    foreach ($formats as $fmt => $cfg) {
        $url = $baseUrl . $cfg['path'] . $jenis;
        $res = httpReq($url, 'GET', [], $cookieFile);
        
        $codeOk = ($res['code'] === 200);
        $headerOk = (stripos($res['headers'], $cfg['mime']) !== false);
        $bodyOk = call_user_func($cfg['check'], $res['body']);
        $sizeBytes = strlen($res['body']);
        
        assertTest(
            "Export {$jenis} format " . strtoupper($fmt) . " (Ukuran: {$sizeBytes} bytes)",
            $codeOk && $headerOk && $bodyOk,
            "HTTP: {$res['code']}, Header match: " . ($headerOk ? 'Yes' : 'No') . ", Body valid: " . ($bodyOk ? 'Yes' : 'No')
        );
    }
}

// 3. Pengujian Filter Parameter pada Setiap Jenis Laporan
echo "\n[3] Uji Filter Parameter pada Laporan...\n";

// A. Filter Rentang Tanggal Spesifik pada Laporan 'semua'
$currMonthStart = date('Y-m-01');
$currMonthEnd   = date('Y-m-t');
$urlFilteredSemua = $baseUrl . "/laporan/pdf/semua?start_date={$currMonthStart}&end_date={$currMonthEnd}";
$resFilteredSemua = httpReq($urlFilteredSemua, 'GET', [], $cookieFile);
assertTest(
    "Filter Tanggal Bulan Berjalan pada PDF 'semua'",
    $resFilteredSemua['code'] === 200 && substr($resFilteredSemua['body'], 0, 4) === '%PDF',
    "HTTP: {$resFilteredSemua['code']}"
);

// B. Filter Status pada Laporan Peminjaman (PDF, Word, Excel)
$urlPinjamDipinjam = $baseUrl . "/laporan/excel/peminjaman?status=dipinjam&start_date=2026-01-01&end_date=2026-12-31";
$resPinjamDipinjam = httpReq($urlPinjamDipinjam, 'GET', [], $cookieFile);
assertTest(
    "Filter Status 'dipinjam' pada Excel Peminjaman",
    $resPinjamDipinjam['code'] === 200 && stripos($resPinjamDipinjam['headers'], 'vnd.ms-excel') !== false,
    "HTTP: {$resPinjamDipinjam['code']}"
);

// C. Filter Kategori dan Status pada Laporan Buku
$urlBukuFilter = $baseUrl . "/laporan/word/buku?kategori_id=1&status=aktif";
$resBukuFilter = httpReq($urlBukuFilter, 'GET', [], $cookieFile);
assertTest(
    "Filter Kategori & Status 'aktif' pada Word Buku",
    $resBukuFilter['code'] === 200 && stripos($resBukuFilter['headers'], 'vnd.ms-word') !== false,
    "HTTP: {$resBukuFilter['code']}"
);

// D. Uji Penanganan Tanggal Kosong / Tidak Ada Data (Toleransi Empty Set)
echo "\n[4] Uji Kasus Batas: Rentang Tanggal Masa Depan (Empty Data)...\n";
$urlEmptyPdf = $baseUrl . "/laporan/pdf/pengembalian?start_date=2099-01-01&end_date=2099-01-31";
$resEmptyPdf = httpReq($urlEmptyPdf, 'GET', [], $cookieFile);
assertTest(
    "Export PDF Pengembalian Rentang Kosong (2099) Tidak Mengalami Crash (HTTP 200)",
    $resEmptyPdf['code'] === 200 && substr($resEmptyPdf['body'], 0, 4) === '%PDF',
    "HTTP: {$resEmptyPdf['code']}"
);

$urlEmptyExcel = $baseUrl . "/laporan/excel/semua?start_date=2099-01-01&end_date=2099-01-31";
$resEmptyExcel = httpReq($urlEmptyExcel, 'GET', [], $cookieFile);
assertTest(
    "Export Excel Rekapitulasi Rentang Kosong (2099) Menghasilkan Format Valid (HTTP 200)",
    $resEmptyExcel['code'] === 200 && strpos($resEmptyExcel['body'], '<table') !== false,
    "HTTP: {$resEmptyExcel['code']}"
);

// Cleanup cookie
if (file_exists($cookieFile)) unlink($cookieFile);

echo "\n=======================================================\n";
echo "               RINGKASAN HASIL QA LAPORAN\n";
echo "=======================================================\n";
echo "Total Pengujian : " . ($passCount + $failCount) . "\n";
echo "Passed          : {$passCount}\n";
echo "Failed          : {$failCount}\n";
echo "Status Akhir    : " . ($failCount === 0 ? "SEMUA PENGUJIAN LULUS (SUCCESS)" : "ADA PENGUJIAN GAGAL") . "\n";
echo "=======================================================\n";

exit($failCount === 0 ? 0 : 1);
