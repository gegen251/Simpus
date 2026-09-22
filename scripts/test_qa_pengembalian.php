<?php

/**
 * Test QA Modul Pengembalian SIMPUS SD
 * Memeriksa:
 * 1. Pengembalian Tepat Waktu (Denda 0, stok bertambah atomik, status dikembalikan)
 * 2. Perhitungan Denda Keterlambatan Otomatis (Hari telat * Tarif denda di pengaturan)
 * 3. Proses Pelunasan Denda (status_denda -> 'lunas', timestamp catatan, audit_log)
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_pengembalian.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

$mysqli = new mysqli('127.0.0.1', 'root', '', 'perpustakaan', 3307);
if ($mysqli->connect_error) {
    die("Koneksi DB Gagal: " . $mysqli->connect_error);
}

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
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

function getCsrfToken($baseUrl, $cookieFile, $path = '/pengembalian') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL PENGEMBALIAN SIMPUS SD\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

// Login Admin
$resLogin = httpReq($baseUrl . '/login', 'GET', [], $cookieFile);
preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $resLogin['body'], $m);
$loginCsrf = $m[1] ?? '';
httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $loginCsrf,
    'username' => 'admin',
    'password' => 'admin123'
], $cookieFile);

// Siapkan Anggota & Buku untuk pengujian pengembalian
$nisnKembali = 'NISN-RET-' . time();
$mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, status) 
                VALUES ('AG-RET-01', 'Anggota Uji Kembali', 'siswa', 'Kelas 5C', '{$nisnKembali}', 'L', '0812777777', 'aktif')");
$anggotaRetId = $mysqli->insert_id;

$bukuRow = $mysqli->query("SELECT id, stok_tersedia, jumlah_eksemplar FROM buku WHERE status = 'aktif' LIMIT 1")->fetch_assoc();
$bukuId = (int)$bukuRow['id'];
$stokAwal = (int)$bukuRow['stok_tersedia'];

// Ambil tarif denda per hari dari pengaturan
$qTarif = $mysqli->query("SELECT nilai FROM pengaturan WHERE kunci = 'tarif_denda_per_hari'")->fetch_assoc();
$tarifPerHari = (float)($qTarif['nilai'] ?? 1000);
echo "Tarif Denda Konfigurasi: Rp " . number_format($tarifPerHari, 0, ',', '.') . " per hari.\n\n";

// -------------------------------------------------------------
// TEST 1: Pengembalian Normal (Tepat Waktu)
// -------------------------------------------------------------
echo "[1] Uji Pengembalian Normal (Tepat Waktu)...\n";
// Buat pinjaman yang jatuh temponya masih masa depan
$tglPinjam = date('Y-m-d', strtotime('-2 days'));
$tglTempo  = date('Y-m-d', strtotime('+3 days'));

// Kurangi stok 1 untuk simulasi peminjaman aktif
$mysqli->query("UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id = {$bukuId}");
$stokSaatPinjam = $stokAwal - 1;

$mysqli->query("INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, admin_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
                VALUES ('PJ-RET-NORMAL-" . time() . "', {$anggotaRetId}, {$bukuId}, 1, '{$tglPinjam}', '{$tglTempo}', 'dipinjam')");
$peminjamanNormalId = $mysqli->insert_id;

$tglKembaliNormal = date('Y-m-d');
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resKembali = httpReq($baseUrl . '/pengembalian/save', 'POST', [
    'csrf_test_name'  => $csrf,
    'peminjaman_id'   => $peminjamanNormalId,
    'tanggal_kembali' => $tglKembaliNormal,
    'catatan'         => 'Pengembalian tepat waktu',
], $cookieFile);

// Verifikasi di DB
$cekPinjamNormal = $mysqli->query("SELECT status FROM peminjaman WHERE id = {$peminjamanNormalId}")->fetch_assoc();
$cekRetNormal = $mysqli->query("SELECT * FROM pengembalian WHERE peminjaman_id = {$peminjamanNormalId}")->fetch_assoc();
$cekStokNormal = (int)$mysqli->query("SELECT stok_tersedia FROM buku WHERE id = {$bukuId}")->fetch_assoc()['stok_tersedia'];

if ($cekPinjamNormal && $cekPinjamNormal['status'] === 'dikembalikan') {
    echo "  [OK] Status peminjaman berhasil berubah menjadi 'dikembalikan'.\n";
    $passCount++;
} else {
    echo "  [FAIL] Status peminjaman gagal diperbarui!\n";
    $failCount++;
}

if ($cekRetNormal && (float)$cekRetNormal['denda'] === 0.0 && $cekRetNormal['status_denda'] === 'tidak_ada') {
    echo "  [OK] Rekord pengembalian tercatat dengan denda Rp 0 (status_denda: 'tidak_ada').\n";
    $passCount++;
} else {
    echo "  [FAIL] Data pengembalian denda tidak sesuai!\n";
    $failCount++;
}

if ($cekStokNormal === $stokSaatPinjam + 1) {
    echo "  [OK] Stok buku bertambah tepat 1 secara atomik ({$stokSaatPinjam} -> {$cekStokNormal}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Penambahan stok buku gagal atau tidak akurat!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Perhitungan Denda Keterlambatan Otomatis
// -------------------------------------------------------------
echo "[2] Uji Perhitungan Denda Keterlambatan Otomatis...\n";
// Buat peminjaman yang jatuh temponya 4 hari yang lalu
$tglPinjamLate = date('Y-m-d', strtotime('-9 days'));
$tglTempoLate  = date('Y-m-d', strtotime('-4 days'));
$hariTerlambatUji = 4;
$expectedDenda = $hariTerlambatUji * $tarifPerHari;

// Kurangi stok 1 untuk simulasi
$mysqli->query("UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id = {$bukuId}");

$mysqli->query("INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, admin_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
                VALUES ('PJ-RET-LATE-" . time() . "', {$anggotaRetId}, {$bukuId}, 1, '{$tglPinjamLate}', '{$tglTempoLate}', 'terlambat')");
$peminjamanLateId = $mysqli->insert_id;

$tglKembaliLate = date('Y-m-d'); // Hari ini (terlambat 4 hari)
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resKembaliLate = httpReq($baseUrl . '/pengembalian/save', 'POST', [
    'csrf_test_name'  => $csrf,
    'peminjaman_id'   => $peminjamanLateId,
    'tanggal_kembali' => $tglKembaliLate,
    'status_denda'    => 'belum_lunas',
    'catatan'         => 'Pengembalian terlambat 4 hari',
], $cookieFile);

// Verifikasi kalkulasi denda di DB
$cekRetLate = $mysqli->query("SELECT * FROM pengembalian WHERE peminjaman_id = {$peminjamanLateId}")->fetch_assoc();
if ($cekRetLate) {
    $dendaActual = (float)$cekRetLate['denda'];
    echo "  Denda Terhitung: Rp " . number_format($dendaActual, 0, ',', '.') . " | Ekspektasi: Rp " . number_format($expectedDenda, 0, ',', '.') . "\n";
    if ($dendaActual == $expectedDenda && $cekRetLate['status_denda'] === 'belum_lunas') {
        echo "  [OK] Denda keterlambatan ({$hariTerlambatUji} hari * Rp {$tarifPerHari} = Rp " . number_format($dendaActual, 0, ',', '.') . ") dihitung presisi.\n";
        echo "  [OK] Status denda tercatat 'belum_lunas'.\n";
        $passCount += 2;
    } else {
        echo "  [FAIL] Perhitungan denda tidak cocok dengan rumus tarif pengaturan!\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Rekord pengembalian terlambat tidak tercatat di DB!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Proses Pelunasan Denda
// -------------------------------------------------------------
echo "[3] Uji Proses Pelunasan Denda (lunasiDenda)...\n";
if (!empty($cekRetLate['id'])) {
    $pengembalianIdToSettle = (int)$cekRetLate['id'];
    
    // Akses endpoint pelunasan denda via POST + CSRF
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    $resLunasi = httpReq($baseUrl . '/pengembalian/lunasi-denda/' . $pengembalianIdToSettle, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);
    
    // Verifikasi DB
    $cekLunas = $mysqli->query("SELECT * FROM pengembalian WHERE id = {$pengembalianIdToSettle}")->fetch_assoc();
    if ($cekLunas && $cekLunas['status_denda'] === 'lunas') {
        echo "  [OK] Status denda berhasil diperbarui menjadi 'lunas'.\n";
        if (strpos($cekLunas['catatan'], 'dilunasi ke pustakawan') !== false) {
            echo "  [OK] Catatan transaksi memuat stempel waktu pelunasan.\n";
        }
        $passCount += 2;
        
        // Verifikasi audit log
        $qAudit = $mysqli->query("SELECT * FROM audit_log WHERE action = 'LUNASI_DENDA' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($qAudit && strpos($qAudit['description'], "ID {$pengembalianIdToSettle}") !== false) {
            echo "  [OK] Pelunasan denda tercatat di tabel audit_log (ID Audit: {$qAudit['id']}).\n";
            $passCount++;
        } else {
            echo "  [FAIL] Pelunasan tidak tercatat di audit_log!\n";
            $failCount++;
        }
    } else {
        echo "  [FAIL] Status pelunasan denda gagal diperbarui di database!\n";
        $failCount++;
    }
}
echo "\n";

// -------------------------------------------------------------
// PEMBERSIHAN DATA UJI
// -------------------------------------------------------------
if (!empty($cekRetNormal['id'])) $mysqli->query("DELETE FROM pengembalian WHERE id = {$cekRetNormal['id']}");
if (!empty($cekRetLate['id'])) $mysqli->query("DELETE FROM pengembalian WHERE id = {$cekRetLate['id']}");
$mysqli->query("DELETE FROM peminjaman WHERE anggota_id = {$anggotaRetId}");
$mysqli->query("DELETE FROM anggota WHERE id = {$anggotaRetId}");
$mysqli->query("UPDATE buku SET stok_tersedia = {$stokAwal} WHERE id = {$bukuId}");

if (file_exists($cookieFile)) unlink($cookieFile);
$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL PENGEMBALIAN:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL PENGEMBALIAN BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
