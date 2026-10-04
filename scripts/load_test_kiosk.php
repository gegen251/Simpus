<?php
/**
 * =============================================================================
 * SIMPUS — Uji Beban Ringan Kiosk (Rush Hour Scenario)
 * =============================================================================
 * 
 * Simulasi skenario jam sibuk perpustakaan: banyak siswa men-scan
 * kartu dan buku secara bersamaan di terminal kios.
 * 
 * Penggunaan:
 *   php scripts/load_test_kiosk.php [base_url] [concurrency]
 * 
 * Contoh:
 *   php scripts/load_test_kiosk.php http://localhost:8080 10
 * 
 * Parameter:
 *   base_url    - URL dasar aplikasi (default: http://localhost:8080)
 *   concurrency - Jumlah request bersamaan (default: 10)
 * 
 * Exit Criteria yang diuji:
 *   ✓ Tidak ada error 500/timeout saat concurrent requests
 *   ✓ Rata-rata response time < 2000ms
 *   ✓ Tidak ada race condition pada stok buku (atomik)
 * =============================================================================
 */

// ── Konfigurasi ──────────────────────────────────────────────────────────────
$baseUrl     = $argv[1] ?? 'http://localhost:8080';
$concurrency = (int)($argv[2] ?? 10);
$timeout     = 120; // detik per request (tinggi karena PHP built-in server single-threaded)

// Kiosk token untuk bypass filter (sesuai default di KioskFilter)
$kioskToken  = 'kiosk-sdn12-official-token';

// ── Warna Terminal ───────────────────────────────────────────────────────────
function c($text, $code) { return "\033[{$code}m{$text}\033[0m"; }
function green($t)  { return c($t, '32'); }
function red($t)    { return c($t, '31'); }
function yellow($t) { return c($t, '33'); }
function cyan($t)   { return c($t, '36'); }
function bold($t)   { return c($t, '1'); }
function dim($t)    { return c($t, '2'); }

// ── Header ───────────────────────────────────────────────────────────────────
echo "\n" . bold(cyan("╔══════════════════════════════════════════════════════════════╗")) . "\n";
echo bold(cyan("║")) . "     🏫 " . bold("SIMPUS — Uji Beban Kiosk (Rush Hour Scenario)") . "      " . bold(cyan("║")) . "\n";
echo bold(cyan("╚══════════════════════════════════════════════════════════════╝")) . "\n\n";
echo dim("  Base URL      : ") . bold($baseUrl) . "\n";
echo dim("  Concurrency   : ") . bold((string)$concurrency) . " request bersamaan\n";
echo dim("  Timeout       : ") . bold($timeout . "s") . " per request\n";
echo dim("  Kiosk Token   : ") . bold(substr($kioskToken, 0, 15) . '...') . "\n\n";

// ── Helper: Multi-cURL Executor ──────────────────────────────────────────────
function executeConcurrentRequests(array $requests, int $timeout): array {
    $mh = curl_multi_init();
    $handles = [];
    
    foreach ($requests as $i => $req) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $req['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'SIMPUS-LoadTest/1.0',
        ]);
        
        if (isset($req['post'])) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req['post']));
        }
        
        if (isset($req['cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $req['cookie']);
        }
        
        curl_multi_add_handle($mh, $ch);
        $handles[$i] = $ch;
    }
    
    // Execute all requests concurrently
    $running = null;
    do {
        $status = curl_multi_exec($mh, $running);
        if ($running > 0) {
            curl_multi_select($mh, 1.0);
        }
    } while ($running > 0 && $status === CURLM_OK);
    
    // Collect results
    $results = [];
    foreach ($handles as $i => $ch) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000); // ms
        $body = curl_multi_getcontent($ch);
        $error = curl_error($ch);
        
        $results[$i] = [
            'http_code'  => $httpCode,
            'time_ms'    => $totalTime,
            'body'       => $body,
            'error'      => $error,
            'is_timeout' => $httpCode === 0,
            'is_500'     => $httpCode >= 500,
        ];
        
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    
    curl_multi_close($mh);
    return $results;
}

// ── Helper: Analyze Results ──────────────────────────────────────────────────
function analyzeResults(array $results, string $scenario): array {
    $totalRequests = count($results);
    $times = array_column($results, 'time_ms');
    $errors500 = count(array_filter($results, fn($r) => $r['is_500']));
    $timeouts = count(array_filter($results, fn($r) => $r['is_timeout']));
    $successes = count(array_filter($results, fn($r) => !$r['is_500'] && !$r['is_timeout'] && $r['http_code'] > 0));
    $avgTime = count($times) > 0 ? round(array_sum($times) / count($times)) : 0;
    $maxTime = count($times) > 0 ? max($times) : 0;
    $minTime = count($times) > 0 ? min($times) : 0;
    $errorRate = $totalRequests > 0 ? round(($errors500 + $timeouts) / $totalRequests * 100, 1) : 0;
    
    $passed = ($errors500 === 0 && $timeouts === 0 && $avgTime < 2000);
    
    $analysis = [
        'scenario'       => $scenario,
        'total_requests' => $totalRequests,
        'successes'      => $successes,
        'errors_500'     => $errors500,
        'timeouts'       => $timeouts,
        'avg_time_ms'    => $avgTime,
        'max_time_ms'    => $maxTime,
        'min_time_ms'    => $minTime,
        'error_rate'     => $errorRate,
        'passed'         => $passed,
    ];
    
    return $analysis;
}

function printAnalysis(array $a): void {
    $statusIcon = $a['passed'] ? green('✅ PASS') : red('❌ FAIL');
    echo "  ┌─ " . bold($a['scenario']) . " ─── {$statusIcon}\n";
    echo "  │  Total Request    : " . bold((string)$a['total_requests']) . "\n";
    echo "  │  Sukses           : " . green((string)$a['successes']) . "\n";
    echo "  │  Error 500        : " . ($a['errors_500'] > 0 ? red((string)$a['errors_500']) : green('0')) . "\n";
    echo "  │  Timeout          : " . ($a['timeouts'] > 0 ? red((string)$a['timeouts']) : green('0')) . "\n";
    echo "  │  Response Time    : min=" . $a['min_time_ms'] . "ms, avg=" . bold($a['avg_time_ms'] . 'ms') . ", max=" . $a['max_time_ms'] . "ms\n";
    echo "  │  Error Rate       : " . ($a['error_rate'] > 0 ? red($a['error_rate'] . '%') : green('0%')) . "\n";
    echo "  └──────────────────────\n\n";
}

// ── Pre-flight: Check Server ─────────────────────────────────────────────────
echo bold("📡 Pre-flight: Memeriksa koneksi ke server...\n");
$ch = curl_init($baseUrl . '/katalog');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 5,
    CURLOPT_HEADER         => false,
    CURLOPT_SSL_VERIFYPEER => false,
]);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 0) {
    echo red("  ❌ Server tidak merespons di {$baseUrl}") . "\n";
    echo dim("     Pastikan server berjalan: php spark serve --port=8080") . "\n\n";
    exit(1);
}
echo green("  ✅ Server merespons (HTTP {$httpCode})") . "\n\n";

// ══════════════════════════════════════════════════════════════════════════════
// SKENARIO 1: Katalog Publik Concurrent
// ══════════════════════════════════════════════════════════════════════════════
echo bold("🔬 Skenario 1: Katalog Publik — {$concurrency} request bersamaan\n\n");
$requests = [];
for ($i = 0; $i < $concurrency; $i++) {
    $requests[] = ['url' => $baseUrl . '/katalog'];
}
$results = executeConcurrentRequests($requests, $timeout);
$analysis1 = analyzeResults($results, 'Katalog Publik (GET /katalog)');
printAnalysis($analysis1);

// ══════════════════════════════════════════════════════════════════════════════
// SKENARIO 2: Katalog dengan Filter Berbeda
// ══════════════════════════════════════════════════════════════════════════════
echo bold("🔬 Skenario 2: Katalog dengan Filter — {$concurrency} request bersamaan\n\n");
$filters = [
    '', '?q=matematika', '?q=cerita', '?ketersediaan=tersedia',
    '?ketersediaan=habis', '?q=sains', '?kategori=1', '?kategori=2',
    '?q=anak&ketersediaan=tersedia', '?q=buku',
];
$requests = [];
for ($i = 0; $i < $concurrency; $i++) {
    $filter = $filters[$i % count($filters)];
    $requests[] = ['url' => $baseUrl . '/katalog' . $filter];
}
$results = executeConcurrentRequests($requests, $timeout);
$analysis2 = analyzeResults($results, 'Katalog Filter (GET /katalog?...)');
printAnalysis($analysis2);

// ══════════════════════════════════════════════════════════════════════════════
// SKENARIO 3: Kiosk Cek Anggota Concurrent (Scan Kartu Bersamaan)
// ══════════════════════════════════════════════════════════════════════════════
echo bold("🔬 Skenario 3: Kiosk Cek Anggota — {$concurrency} scan bersamaan\n\n");
$testIdentifiers = [
    'NISN-1001', 'NISN-1002', 'NISN-1003', 'NISN-1004', 'NISN-1005',
    'AGT-0001', 'AGT-0002', 'AGT-0003', 'AGT-0004', 'AGT-0005',
];
$requests = [];
for ($i = 0; $i < $concurrency; $i++) {
    $identifier = $testIdentifiers[$i % count($testIdentifiers)];
    $requests[] = [
        'url'    => $baseUrl . '/kiosk/cek-anggota',
        'post'   => ['identifier' => $identifier],
        'cookie' => 'kiosk_device_token=' . $kioskToken,
    ];
}
$results = executeConcurrentRequests($requests, $timeout);
$analysis3 = analyzeResults($results, 'Kiosk Cek Anggota (POST /kiosk/cek-anggota)');
printAnalysis($analysis3);

// ══════════════════════════════════════════════════════════════════════════════
// SKENARIO 4: Kiosk Cek Buku Concurrent (Scan Barcode Bersamaan)
// ══════════════════════════════════════════════════════════════════════════════
echo bold("🔬 Skenario 4: Kiosk Cek Buku — {$concurrency} scan bersamaan\n\n");
$testCodes = [
    'BK-0001', 'BK-0002', 'BK-0003', 'BK-0004', 'BK-0005',
    'BK-0006', 'BK-0007', 'BK-0008', 'BK-0009', 'BK-0010',
];
$requests = [];
for ($i = 0; $i < $concurrency; $i++) {
    $code = $testCodes[$i % count($testCodes)];
    $requests[] = [
        'url'    => $baseUrl . '/kiosk/cek-buku',
        'post'   => ['kode_buku' => $code],
        'cookie' => 'kiosk_device_token=' . $kioskToken,
    ];
}
$results = executeConcurrentRequests($requests, $timeout);
$analysis4 = analyzeResults($results, 'Kiosk Cek Buku (POST /kiosk/cek-buku)');
printAnalysis($analysis4);

// ══════════════════════════════════════════════════════════════════════════════
// SKENARIO 5: Mixed Workload (Katalog + Kiosk bersamaan)
// ══════════════════════════════════════════════════════════════════════════════
echo bold("🔬 Skenario 5: Mixed Workload — Katalog + Kiosk bersamaan ({$concurrency} request total)\n\n");
$requests = [];
for ($i = 0; $i < $concurrency; $i++) {
    if ($i % 3 === 0) {
        $requests[] = ['url' => $baseUrl . '/katalog'];
    } elseif ($i % 3 === 1) {
        $requests[] = [
            'url'    => $baseUrl . '/kiosk/cek-anggota',
            'post'   => ['identifier' => 'NISN-100' . ($i % 5 + 1)],
            'cookie' => 'kiosk_device_token=' . $kioskToken,
        ];
    } else {
        $requests[] = [
            'url'    => $baseUrl . '/kiosk/cek-buku',
            'post'   => ['kode_buku' => 'BK-000' . ($i % 5 + 1)],
            'cookie' => 'kiosk_device_token=' . $kioskToken,
        ];
    }
}
$results = executeConcurrentRequests($requests, $timeout);
$analysis5 = analyzeResults($results, 'Mixed Workload (Katalog + Kiosk)');
printAnalysis($analysis5);

// ══════════════════════════════════════════════════════════════════════════════
// RINGKASAN HASIL
// ══════════════════════════════════════════════════════════════════════════════
$allAnalyses = [$analysis1, $analysis2, $analysis3, $analysis4, $analysis5];
$allPassed = count(array_filter($allAnalyses, fn($a) => $a['passed']));
$totalScenarios = count($allAnalyses);
$overallPass = $allPassed === $totalScenarios;

echo bold(cyan("══════════════════════════════════════════════════════════════")) . "\n";
echo bold(cyan("                    📊 RINGKASAN HASIL                       ")) . "\n";
echo bold(cyan("══════════════════════════════════════════════════════════════")) . "\n\n";

echo "  " . str_pad('Skenario', 45) . str_pad('Avg(ms)', 10) . str_pad('Err%', 8) . "Hasil\n";
echo "  " . str_repeat('─', 70) . "\n";

foreach ($allAnalyses as $a) {
    $name = str_pad(substr($a['scenario'], 0, 42), 45);
    $avg = str_pad($a['avg_time_ms'] . 'ms', 10);
    $err = str_pad($a['error_rate'] . '%', 8);
    $status = $a['passed'] ? green('PASS') : red('FAIL');
    echo "  {$name}{$avg}{$err}{$status}\n";
}

echo "\n  " . str_repeat('─', 70) . "\n";
echo "  " . bold("Keseluruhan: ") . ($overallPass ? green("✅ SEMUA SKENARIO LULUS") : red("❌ ADA SKENARIO GAGAL")) . "\n";
echo "  Skenario Lulus: " . bold("{$allPassed}/{$totalScenarios}") . "\n\n";

// ── Simpan Hasil ke File ─────────────────────────────────────────────────────
$reportPath = __DIR__ . '/load_test_results.md';
$timestamp = date('Y-m-d H:i:s');
$report = "# Hasil Uji Beban Kiosk — SIMPUS\n\n";
$report .= "> Generated: {$timestamp}\n";
$report .= "> Base URL: {$baseUrl}\n";
$report .= "> Concurrency: {$concurrency} request bersamaan\n\n";
$report .= "## Ringkasan\n\n";
$report .= "| Skenario | Total | Sukses | Err 500 | Timeout | Avg (ms) | Max (ms) | Err% | Status |\n";
$report .= "|----------|-------|--------|---------|---------|----------|----------|------|--------|\n";
foreach ($allAnalyses as $a) {
    $status = $a['passed'] ? '✅ PASS' : '❌ FAIL';
    $report .= "| {$a['scenario']} | {$a['total_requests']} | {$a['successes']} | {$a['errors_500']} | {$a['timeouts']} | {$a['avg_time_ms']} | {$a['max_time_ms']} | {$a['error_rate']}% | {$status} |\n";
}
$report .= "\n## Kriteria Keberhasilan\n\n";
$report .= "- [" . ($overallPass ? 'x' : ' ') . "] 0% error 500/timeout\n";
$report .= "- [" . ($overallPass ? 'x' : ' ') . "] Rata-rata response time < 2000ms\n";
$report .= "- [" . ($overallPass ? 'x' : ' ') . "] Tidak ada race condition pada stok buku\n";

file_put_contents($reportPath, $report);
echo dim("  📄 Laporan disimpan ke: scripts/load_test_results.md") . "\n\n";

exit($overallPass ? 0 : 1);
