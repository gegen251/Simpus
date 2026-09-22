<?php

/**
 * Test QA Modul Peminjaman SIMPUS SD
 * Memeriksa:
 * 1. Proses Peminjaman Normal (Pengurangan stok atomik & pencatatan audit log)
 * 2. Validasi Kuota Maksimal Peminjaman (max_pinjam_buku)
 * 3. Validasi Tanggungan Denda Belum Lunas (Blokir pinjam jika ada denda)
 * 4. Uji Keunikan Kode Transaksi pada Transaksi Berdekatan Waktu (Collision Test)
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_peminjaman.txt';
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

function getCsrfToken($baseUrl, $cookieFile, $path = '/peminjaman') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL PEMINJAMAN SIMPUS SD\n";
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

// Siapkan Anggota Khusus Uji & Buku Khusus Uji
$nisnPinjam = 'NISN-PINJAM-' . time();
$mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, status) 
                VALUES ('AG-TEST-01', 'Anggota Uji Pinjam', 'siswa', 'Kelas 4A', '{$nisnPinjam}', 'L', '0812345678', 'aktif')");
$anggotaUjiId = $mysqli->insert_id;

// Ambil buku yang memiliki stok > 0
$bukuRow = $mysqli->query("SELECT id, judul, stok_tersedia FROM buku WHERE stok_tersedia >= 3 AND status = 'aktif' LIMIT 1")->fetch_assoc();
if (!$bukuRow) {
    // Buat buku dummy stok 5
    $katRow = $mysqli->query("SELECT id FROM kategori_buku LIMIT 1")->fetch_assoc();
    $katId = (int)($katRow['id'] ?? 1);
    $mysqli->query("INSERT INTO buku (kode_buku, judul, penulis, penerbit, kategori_id, jumlah_eksemplar, stok_tersedia, status) 
                    VALUES ('BK-UJI-01', 'Buku Uji Pinjam QA', 'Penulis Uji', 'Penerbit Uji', {$katId}, 5, 5, 'aktif')");
    $bukuId = $mysqli->insert_id;
    $stokAwal = 5;
} else {
    $bukuId = (int)$bukuRow['id'];
    $stokAwal = (int)$bukuRow['stok_tersedia'];
}

// -------------------------------------------------------------
// TEST 1: Proses Peminjaman Normal & Pengurangan Stok Atomik
// -------------------------------------------------------------
echo "[1] Uji Proses Peminjaman Normal & Pengurangan Stok Atomik...\n";
$tglPinjam = date('Y-m-d');
$tglTempo  = date('Y-m-d', strtotime('+7 days'));

$csrf = getCsrfToken($baseUrl, $cookieFile);
$resPinjam = httpReq($baseUrl . '/peminjaman/store', 'POST', [
    'csrf_test_name'      => $csrf,
    'anggota_id'          => $anggotaUjiId,
    'buku_id'             => $bukuId,
    'tanggal_pinjam'      => $tglPinjam,
    'tanggal_jatuh_tempo' => $tglTempo,
], $cookieFile);

// Verifikasi transaksi di DB
$trx1 = $mysqli->query("SELECT * FROM peminjaman WHERE anggota_id = {$anggotaUjiId} AND buku_id = {$bukuId} AND status = 'dipinjam' ORDER BY id DESC LIMIT 1")->fetch_assoc();
$bukuCek1 = $mysqli->query("SELECT stok_tersedia FROM buku WHERE id = {$bukuId}")->fetch_assoc();
$stokSetelah1 = (int)$bukuCek1['stok_tersedia'];

if ($trx1) {
    echo "  [OK] Transaksi peminjaman berhasil dibuat: {$trx1['kode_transaksi']} (Status: {$trx1['status']}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Transaksi peminjaman tidak tercatat di database!\n";
    $failCount++;
}

if ($stokSetelah1 === $stokAwal - 1) {
    echo "  [OK] Stok buku berkurang tepat 1 secara atomik ({$stokAwal} -> {$stokSetelah1}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Pengurangan stok tidak akurat! Stok awal {$stokAwal}, stok saat ini {$stokSetelah1}\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Validasi Kuota Maksimal Peminjaman (max_pinjam_buku)
// -------------------------------------------------------------
echo "[2] Uji Validasi Batas Kuota Maksimal Peminjaman (max_pinjam_buku)...\n";
// Ambil kuota max_pinjam_buku dari database pengaturan
$qMax = $mysqli->query("SELECT nilai FROM pengaturan WHERE kunci = 'max_pinjam_buku'")->fetch_assoc();
$maxBuku = (int)($qMax['nilai'] ?? 3);
echo "  Konfigurasi Kuota Maksimal: {$maxBuku} buku per anggota.\n";

// Pinjam hingga kuota maksimal terpenuhi
$pinjamSekarang = (int)$mysqli->query("SELECT COUNT(*) as c FROM peminjaman WHERE anggota_id = {$anggotaUjiId} AND status = 'dipinjam'")->fetch_assoc()['c'];
while ($pinjamSekarang < $maxBuku) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/peminjaman/store', 'POST', [
        'csrf_test_name'      => $csrf,
        'anggota_id'          => $anggotaUjiId,
        'buku_id'             => $bukuId,
        'tanggal_pinjam'      => $tglPinjam,
        'tanggal_jatuh_tempo' => $tglTempo,
    ], $cookieFile);
    $pinjamSekarang = (int)$mysqli->query("SELECT COUNT(*) as c FROM peminjaman WHERE anggota_id = {$anggotaUjiId} AND status = 'dipinjam'")->fetch_assoc()['c'];
}

echo "  Pinjaman aktif anggota saat ini telah mencapai kuota: {$pinjamSekarang} buku.\n";

// Percobaan pinjam melebihi kuota
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resOver = httpReq($baseUrl . '/peminjaman/store', 'POST', [
    'csrf_test_name'      => $csrf,
    'anggota_id'          => $anggotaUjiId,
    'buku_id'             => $bukuId,
    'tanggal_pinjam'      => $tglPinjam,
    'tanggal_jatuh_tempo' => $tglTempo,
], $cookieFile);

// Hitung kembali di DB
$pinjamAkhir = (int)$mysqli->query("SELECT COUNT(*) as c FROM peminjaman WHERE anggota_id = {$anggotaUjiId} AND status = 'dipinjam'")->fetch_assoc()['c'];

if ($pinjamAkhir === $maxBuku) {
    echo "  [OK] Percobaan meminjam melebihi kuota DITOLAK sistem (jumlah pinjam tetap {$maxBuku}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Validasi kuota jebol! Jumlah pinjaman tembus ({$pinjamAkhir} > {$maxBuku}).\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Validasi Blokir Pinjam Jika Ada Denda Belum Lunas
// -------------------------------------------------------------
echo "[3] Uji Validasi Blokir Peminjaman Jika Ada Denda Belum Lunas...\n";
// Buat anggota terpisah yang memiliki denda belum lunas
$nisnDenda = 'NISN-DENDA-' . time();
$mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, status) 
                VALUES ('AG-DENDA-01', 'Anggota Punya Denda', 'siswa', 'Kelas 6A', '{$nisnDenda}', 'L', '0812999999', 'aktif')");
$anggotaDendaId = $mysqli->insert_id;

// Buat peminjaman lalu buat pengembalian berstatus belum_lunas
$mysqli->query("INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, admin_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
                VALUES ('PJ-DUMMY-FINE', {$anggotaDendaId}, {$bukuId}, 1, '2026-09-01', '2026-09-07', 'dikembalikan')");
$peminjamanFineId = $mysqli->insert_id;

$mysqli->query("INSERT INTO pengembalian (peminjaman_id, admin_id, tanggal_kembali, denda, status_denda, catatan)
                VALUES ({$peminjamanFineId}, 1, '2026-09-12', 5000, 'belum_lunas', 'Denda terlambat 5 hari')");
$pengembalianFineId = $mysqli->insert_id;

// Coba lakukan peminjaman baru oleh anggota berpenunggakan denda ini
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resFineBlock = httpReq($baseUrl . '/peminjaman/store', 'POST', [
    'csrf_test_name'      => $csrf,
    'anggota_id'          => $anggotaDendaId,
    'buku_id'             => $bukuId,
    'tanggal_pinjam'      => $tglPinjam,
    'tanggal_jatuh_tempo' => $tglTempo,
], $cookieFile);

$checkFineBorrow = $mysqli->query("SELECT id FROM peminjaman WHERE anggota_id = {$anggotaDendaId} AND status = 'dipinjam'")->fetch_assoc();

if (!$checkFineBorrow) {
    echo "  [OK] Peminjaman baru DITOLAK karena anggota masih memiliki denda tertunggak (Rp 5.000, belum_lunas).\n";
    $passCount++;
} else {
    echo "  [FAIL] Validasi denda jebol! Anggota berpenunggakan denda berhasil meminjam buku.\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: Uji Keunikan Kode Transaksi (Collision Test)
// -------------------------------------------------------------
echo "[4] Uji Keunikan Kode Transaksi pada Transaksi Beruntun (Collision Test)...\n";
$collMemberIds = [];
for ($m = 1; $m <= 3; $m++) {
    $nisnC = 'NISN-COLL-' . time() . '-' . $m;
    $mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, status) 
                    VALUES ('AG-COLL-0{$m}', 'Anggota Collision {$m}', 'siswa', 'Kelas 4B', '{$nisnC}', 'P', '081288888{$m}', 'aktif')");
    $collMemberIds[] = $mysqli->insert_id;
}

// Jalankan 3 peminjaman cepat berurutan untuk masing-masing anggota
$generatedCodes = [];
foreach ($collMemberIds as $mid) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/peminjaman/store', 'POST', [
        'csrf_test_name'      => $csrf,
        'anggota_id'          => $mid,
        'buku_id'             => $bukuId,
        'tanggal_pinjam'      => $tglPinjam,
        'tanggal_jatuh_tempo' => $tglTempo,
    ], $cookieFile);
}

$idList = implode(',', $collMemberIds);
$qCodes = $mysqli->query("SELECT kode_transaksi FROM peminjaman WHERE anggota_id IN ({$idList}) ORDER BY id ASC");
while ($r = $qCodes->fetch_assoc()) {
    $generatedCodes[] = $r['kode_transaksi'];
}

echo "  Kode Transaksi yang Dihasilkan: " . implode(', ', $generatedCodes) . "\n";
$uniqueCodes = array_unique($generatedCodes);

if (count($generatedCodes) === 3 && count($uniqueCodes) === 3) {
    echo "  [OK] Seluruh kode transaksi (3 transaksi) terbukti 100% unik tanpa duplikasi/collision.\n";
    $passCount++;
} else {
    echo "  [FAIL] Ditemukan kode transaksi duplikat atau transaksi gagal!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// PEMBERSIHAN DATA UJI
// -------------------------------------------------------------
$mysqli->query("DELETE FROM pengembalian WHERE id = {$pengembalianFineId}");
$mysqli->query("DELETE FROM peminjaman WHERE anggota_id IN ({$anggotaUjiId}, {$anggotaDendaId}, {$idList})");
$mysqli->query("DELETE FROM anggota WHERE id IN ({$anggotaUjiId}, {$anggotaDendaId}, {$idList})");

// Kembalikan stok buku ke semula
$mysqli->query("UPDATE buku SET stok_tersedia = {$stokAwal} WHERE id = {$bukuId}");

if (file_exists($cookieFile)) unlink($cookieFile);
$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL PEMINJAMAN:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL PEMINJAMAN BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
