<?php

// ==============================================================================
// SIMPUS SD - HTTP End-to-End Test for Peminjaman::perpanjang Business Rules
// ==============================================================================

echo "=== MEMULAI PENGUJIAN END-TO-END ATURAN BISNIS PERPANJANGAN ===" . PHP_EOL;

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=perpustakaan;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// 1. Setup Test Data
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$future = date('Y-m-d', strtotime('+3 days'));

$pdo->exec("DELETE FROM pengembalian WHERE peminjaman_id IN (SELECT id FROM peminjaman WHERE kode_transaksi LIKE 'TEST-F3-%')");
$pdo->exec("DELETE FROM peminjaman WHERE kode_transaksi LIKE 'TEST-F3-%'");

// Case 1: Terlambat
$pdo->prepare("
    INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, jumlah_perpanjangan, admin_id, created_at)
    VALUES ('TEST-F3-001', 1, 1, '2026-09-10', ?, 'terlambat', 0, 1, NOW())
")->execute([$yesterday]);
$idTerlambat = $pdo->lastInsertId();

// Case 2: Anggota Punya Denda Belum Lunas
$pdo->prepare("
    INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, jumlah_perpanjangan, admin_id, created_at)
    VALUES ('TEST-F3-002', 1, 1, ?, ?, 'dipinjam', 0, 1, NOW())
")->execute([$today, $future]);
$idAdaDenda = $pdo->lastInsertId();

$pdo->prepare("
    INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, jumlah_perpanjangan, admin_id, created_at)
    VALUES ('TEST-F3-FINES', 1, 2, '2026-09-01', '2026-09-06', 'dikembalikan', 0, 1, NOW())
");
$idPinjamDenda = $pdo->lastInsertId();

$pdo->prepare("
    INSERT INTO pengembalian (peminjaman_id, tanggal_kembali, jumlah_hari_terlambat, denda, status_denda, admin_id, created_at)
    VALUES (?, ?, 2, 1000.00, 'belum_lunas', 1, NOW())
")->execute([$idPinjamDenda, $today]);

// Case 3: Max Perpanjang (2x)
$pdo->prepare("
    INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, jumlah_perpanjangan, admin_id, created_at)
    VALUES ('TEST-F3-003', 2, 1, ?, ?, 'dipinjam', 2, 1, NOW())
")->execute([$today, $future]);
$idMax = $pdo->lastInsertId();

// Case 4: Valid
$pdo->prepare("
    INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, jumlah_perpanjangan, admin_id, created_at)
    VALUES ('TEST-F3-004', 2, 1, ?, ?, 'dipinjam', 0, 1, NOW())
")->execute([$today, $future]);
$idValid = $pdo->lastInsertId();

// 2. Login as admin & capture session cookies
$ch = curl_init('http://localhost:8080/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Forwarded-Proto: https']);
$resp = curl_exec($ch);
curl_close($ch);

preg_match_all('/Set-Cookie:\s*([^;]+)/mi', $resp, $m);
$cookies = [];
foreach ($m[1] as $c) {
    list($k, $v) = explode('=', $c, 2);
    $cookies[$k] = $v;
}
preg_match('/name="csrf_test_name" value="([^"]+)"/', $resp, $mCsrf);
$csrf = $mCsrf[1] ?? '';

$cookieHeader = 'Cookie: ' . http_build_query($cookies, '', '; ');
$ch = curl_init('http://localhost:8080/login/process');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'csrf_test_name' => $csrf,
    'username'       => 'admin',
    'password'       => 'admin123',
]));
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Forwarded-Proto: https', $cookieHeader]);
$loginResp = curl_exec($ch);
curl_close($ch);

preg_match_all('/Set-Cookie:\s*([^;]+)/mi', $loginResp, $m2);
foreach ($m2[1] as $c) {
    list($k, $v) = explode('=', $c, 2);
    $cookies[$k] = $v;
}

function requestPerpanjangHttp($id, &$cookies) {
    $cookieHeader = 'Cookie: ' . http_build_query($cookies, '', '; ');
    
    // GET /peminjaman untuk ambil CSRF
    $ch = curl_init('http://localhost:8080/peminjaman');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Forwarded-Proto: https', $cookieHeader]);
    $pageResp = curl_exec($ch);
    curl_close($ch);

    preg_match_all('/Set-Cookie:\s*([^;]+)/mi', $pageResp, $mP);
    foreach ($mP[1] as $c) {
        list($k, $v) = explode('=', $c, 2);
        $cookies[$k] = $v;
    }
    $cookieHeader = 'Cookie: ' . http_build_query($cookies, '', '; ');

    preg_match('/name="csrf_test_name" value="([^"]+)"/', $pageResp, $mC);
    $csrf = $mC[1] ?? ($cookies['csrf_cookie_name'] ?? '');

    // POST /peminjaman/perpanjang/{id}
    $ch = curl_init("http://localhost:8080/peminjaman/perpanjang/{$id}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['csrf_test_name' => $csrf]));
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Forwarded-Proto: https', $cookieHeader]);
    $postResp = curl_exec($ch);
    curl_close($ch);

    preg_match_all('/Set-Cookie:\s*([^;]+)/mi', $postResp, $mPost);
    foreach ($mPost[1] as $c) {
        list($k, $v) = explode('=', $c, 2);
        $cookies[$k] = $v;
    }
    $cookieHeader = 'Cookie: ' . http_build_query($cookies, '', '; ');

    // GET /peminjaman untuk membaca flash message
    $ch = curl_init('http://localhost:8080/peminjaman');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Forwarded-Proto: https', $cookieHeader]);
    $html = curl_exec($ch);
    curl_close($ch);

    $msg = '';
    if (preg_match('/showToast\(\'[^\']+\',\s*([^\,]+),/s', $html, $toast)) {
        $msg = json_decode(trim($toast[1]), true) ?? $toast[1];
    } elseif (preg_match('/<div[^>]*class="[^"]*bg-(rose|red|emerald|green)-[^"]*"[^>]*>(.*?)<\/div>/s', $html, $alert)) {
        $msg = trim(preg_replace('/\s+/', ' ', strip_tags($alert[2])));
    }
    return $msg;
}

echo PHP_EOL . "--- HASIL UJI KASUS BATAS PERPANJANGAN (HTTP) ---" . PHP_EOL;

// 1. Terlambat
$res1 = requestPerpanjangHttp($idTerlambat, $cookies);
echo "1. Transaksi Terlambat -> " . ($res1 ?: 'No Alert') . PHP_EOL;

// 2. Ada Denda
$res2 = requestPerpanjangHttp($idAdaDenda, $cookies);
echo "2. Anggota Ada Denda  -> " . ($res2 ?: 'No Alert') . PHP_EOL;

// 3. Melebihi Kuota Max (2x)
$res3 = requestPerpanjangHttp($idMax, $cookies);
echo "3. Melebihi Kuota Max -> " . ($res3 ?: 'No Alert') . PHP_EOL;

// 4. Valid
$res4 = requestPerpanjangHttp($idValid, $cookies);
echo "4. Transaksi Sah      -> " . ($res4 ?: 'No Alert') . PHP_EOL;

// Verifikasi DB transaksi valid
$rowValid = $pdo->query("SELECT * FROM peminjaman WHERE id = {$idValid}")->fetch(PDO::FETCH_ASSOC);
echo "   -> DB: Jatuh Tempo Baru = {$rowValid['tanggal_jatuh_tempo']} (Awal: {$future}), Jumlah Perpanjang = {$rowValid['jumlah_perpanjangan']}" . PHP_EOL;

// Verifikasi Audit Log
$audit = $pdo->query("SELECT * FROM audit_log WHERE action = 'PERPANJANG_PINJAM' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo "   -> Audit Log: [{$audit['action']}] {$audit['description']}" . PHP_EOL;

// Bersihkan Test Data
$pdo->exec("DELETE FROM pengembalian WHERE peminjaman_id IN (SELECT id FROM peminjaman WHERE kode_transaksi LIKE 'TEST-F3-%')");
$pdo->exec("DELETE FROM peminjaman WHERE kode_transaksi LIKE 'TEST-F3-%'");

echo PHP_EOL . "=== PENGUJIAN END-TO-END ATURAN BISNIS PERPANJANGAN SUKSES ===" . PHP_EOL;
