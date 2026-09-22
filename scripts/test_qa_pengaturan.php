<?php

/**
 * Test QA Modul Pengaturan SIMPUS SD
 * Memeriksa:
 * 1. Form Update Pengaturan via POST /pengaturan/update
 * 2. Perubahan parameter (tarif_denda_per_hari, max_pinjam_buku, durasi_pinjam_default)
 *    langsung berlaku pada transaksi berikutnya secara real-time tanpa restart server.
 * 3. Uji transaksi batas kuota maksimal pinjam (max_pinjam_buku)
 * 4. Uji kalkulasi jatuh tempo baru (durasi_pinjam_default)
 * 5. Uji kalkulasi denda pengembalian dengan tarif baru (tarif_denda_per_hari)
 * 6. Audit Log mencatat aksi UBAH_PENGATURAN
 * 7. Pemulihan parameter ke nilai awal setelah selesai pengujian.
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_pengaturan.txt';
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

function getCsrfToken($baseUrl, $cookieFile, $path = '/pengaturan') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL PENGATURAN SIMPUS SD (DYNAMIC APPLY)\n";
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

// 1. Simpan Nilai Pengaturan Awal (Baseline)
$resBaseline = $mysqli->query("SELECT kunci, nilai FROM pengaturan");
$baseline = [];
while ($row = $resBaseline->fetch_assoc()) {
    $baseline[$row['kunci']] = $row['nilai'];
}

echo "[1] Baseline Pengaturan Terbaca:\n";
echo "    - Durasi Pinjam Default : {$baseline['durasi_pinjam_default']} hari\n";
echo "    - Maksimal Pinjam Buku  : {$baseline['max_pinjam_buku']} buku\n";
echo "    - Tarif Denda per Hari  : Rp " . number_format($baseline['tarif_denda_per_hari'], 0, ',', '.') . "\n\n";

// 2. Login Admin
echo "[2] Otentikasi Admin...\n";
$loginCsrf = getCsrfToken($baseUrl, $cookieFile, '/login');
$loginRes = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $loginCsrf,
    'username'       => 'admin',
    'password'       => 'admin123'
], $cookieFile);

assertTest("Login Admin Berhasil", $loginRes['code'] === 200 && strpos($loginRes['body'], 'Dashboard') !== false);

// 3. Update Pengaturan Baru:
//    - durasi_pinjam_default -> 10
//    - max_pinjam_buku -> 1 (hanya boleh pinjam 1)
//    - tarif_denda_per_hari -> 2500
echo "\n[3] Mengubah Pengaturan Sistem via POST /pengaturan/update...\n";
$updateCsrf = getCsrfToken($baseUrl, $cookieFile, '/pengaturan');

$updatePayload = [
    'csrf_test_name'        => $updateCsrf,
    'nama_perpustakaan'     => $baseline['nama_perpustakaan'] ?? 'Perpustakaan SDN 12 Sumbawa',
    'alamat_perpustakaan'   => $baseline['alamat_perpustakaan'] ?? 'Jl. Garuda No. 12',
    'kepala_perpustakaan'   => $baseline['kepala_perpustakaan'] ?? 'Drs. H. Ahmad',
    'nip_kepala'            => $baseline['nip_kepala'] ?? '197501012000031001',
    'durasi_pinjam_default' => '10',
    'tarif_denda_per_hari'  => '2500',
    'max_pinjam_buku'       => '1',
    'kiosk_allowed_ips'     => $baseline['kiosk_allowed_ips'] ?? '',
    'kiosk_secret_token'    => $baseline['kiosk_secret_token'] ?? 'KIOSK-SECRET-SDN12',
];

$updateRes = httpReq($baseUrl . '/pengaturan/update', 'POST', $updatePayload, $cookieFile);
assertTest("POST Update Pengaturan Berhasil Disimpan", $updateRes['code'] === 200 && strpos($updateRes['body'], 'berhasil') !== false);

// Verifikasi di DB
$qCheck = $mysqli->query("SELECT kunci, nilai FROM pengaturan WHERE kunci IN ('durasi_pinjam_default', 'max_pinjam_buku', 'tarif_denda_per_hari')");
$updatedVals = [];
while ($row = $qCheck->fetch_assoc()) {
    $updatedVals[$row['kunci']] = $row['nilai'];
}

assertTest("DB Menyimpan durasi_pinjam_default = 10", ($updatedVals['durasi_pinjam_default'] ?? '') === '10');
assertTest("DB Menyimpan max_pinjam_buku = 1", ($updatedVals['max_pinjam_buku'] ?? '') === '1');
assertTest("DB Menyimpan tarif_denda_per_hari = 2500", ($updatedVals['tarif_denda_per_hari'] ?? '') === '2500');

// Verifikasi Audit Log
$qAudit = $mysqli->query("SELECT * FROM audit_log WHERE action = 'UBAH_PENGATURAN' ORDER BY id DESC LIMIT 1")->fetch_assoc();
assertTest("Audit Log Mencatat Aksi UBAH_PENGATURAN", $qAudit !== null && strpos($qAudit['description'], 'durasi_pinjam_default') !== false);

// 4. Uji Pengaruh Langsung ke Transaksi Sirkulasi Tanpa Restart
echo "\n[4] Pengujian Dampak Langsung ke Transaksi Baru (Tanpa Restart Server)...\n";

// Siapkan Anggota & Buku Uji
$nisnTest = 'NISN-SETTING-' . time();
$mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, status) 
                VALUES ('AG-SETTING-01', 'Siswa Uji Pengaturan', 'siswa', 'Kelas 6A', '{$nisnTest}', 'L', '0812999901', 'aktif')");
$anggotaId = $mysqli->insert_id;

$books = $mysqli->query("SELECT id, judul, stok_tersedia FROM buku WHERE status = 'aktif' AND stok_tersedia >= 2 LIMIT 2")->fetch_all(MYSQLI_ASSOC);
$buku1Id = (int)$books[0]['id'];
$buku2Id = (int)$books[1]['id'];

// A. Uji Kuota Maksimal Baru (max_pinjam_buku = 1)
// Transaksi 1: Pinjam buku 1 (Harus Sukses)
$pinjamCsrf1 = getCsrfToken($baseUrl, $cookieFile, '/peminjaman');
$resPinjam1 = httpReq($baseUrl . '/peminjaman/store', 'POST', [
    'csrf_test_name'      => $pinjamCsrf1,
    'anggota_id'          => $anggotaId,
    'buku_id'             => $buku1Id,
    'tanggal_pinjam'      => date('Y-m-d'),
    'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+7 days')),
], $cookieFile);

$trx1 = $mysqli->query("SELECT * FROM peminjaman WHERE anggota_id = {$anggotaId} AND buku_id = {$buku1Id} AND status = 'dipinjam' LIMIT 1")->fetch_assoc();
assertTest("Transaksi Pinjam Pertama Sukses (Kuota 1/1)", $trx1 !== null);

// Transaksi 2: Coba pinjam buku 2 (Harus Ditolak karena max_pinjam_buku = 1)
$pinjamCsrf2 = getCsrfToken($baseUrl, $cookieFile, '/peminjaman');
$resPinjam2 = httpReq($baseUrl . '/peminjaman/store', 'POST', [
    'csrf_test_name'      => $pinjamCsrf2,
    'anggota_id'          => $anggotaId,
    'buku_id'             => $buku2Id,
    'tanggal_pinjam'      => date('Y-m-d'),
    'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+7 days')),
], $cookieFile);

assertTest(
    "Transaksi Pinjam Kedua Ditolak Sesuai Kuota Baru (max_pinjam_buku = 1)",
    strpos($resPinjam2['body'], 'telah mencapai batas maksimal (1 buku)') !== false,
    "Respon: " . substr(strip_tags($resPinjam2['body']), 0, 100)
);

// B. Uji Perpanjangan Pinjaman Memakai durasi_pinjam_default Baru (= 10 Hari)
if ($trx1) {
    $oldDue = $trx1['tanggal_jatuh_tempo'];
    $expectedNewDue = date('Y-m-d', strtotime($oldDue . ' +10 days'));
    
    $perpanjangCsrf = getCsrfToken($baseUrl, $cookieFile, '/peminjaman');
    $resPerpanjang = httpReq($baseUrl . '/peminjaman/perpanjang/' . $trx1['id'], 'POST', [
        'csrf_test_name' => $perpanjangCsrf
    ], $cookieFile);
    
    $trx1Updated = $mysqli->query("SELECT * FROM peminjaman WHERE id = {$trx1['id']}")->fetch_assoc();
    assertTest(
        "Perpanjangan Menggunakan durasi_pinjam_default Baru (+10 Hari)",
        $trx1Updated['tanggal_jatuh_tempo'] === $expectedNewDue,
        "Expected: {$expectedNewDue}, Actual: {$trx1Updated['tanggal_jatuh_tempo']}"
    );
}

// C. Uji Perhitungan Denda Pengembalian Memakai tarif_denda_per_hari Baru (= Rp 2.500)
// Set tanggal jatuh tempo mundur 4 hari lalu
if ($trx1) {
    $pastDate = date('Y-m-d', strtotime('-4 days'));
    $mysqli->query("UPDATE peminjaman SET tanggal_jatuh_tempo = '{$pastDate}' WHERE id = {$trx1['id']}");
    
    $returnCsrf = getCsrfToken($baseUrl, $cookieFile, '/pengembalian');
    $resReturn = httpReq($baseUrl . '/pengembalian/save', 'POST', [
        'csrf_test_name' => $returnCsrf,
        'peminjaman_id'  => $trx1['id'],
        'tanggal_kembali'=> date('Y-m-d'),
        'kondisi_buku'   => 'baik',
        'catatan'        => 'Uji QA Pengaturan Denda 2500'
    ], $cookieFile);
    
    $pengembalianRow = $mysqli->query("SELECT * FROM pengembalian WHERE peminjaman_id = {$trx1['id']}")->fetch_assoc();
    $expectedDenda = 4 * 2500; // 4 hari * Rp 2.500 = Rp 10.000
    
    assertTest(
        "Pengembalian Menghitung Denda Menggunakan Tarif Baru (4 hari x Rp 2.500 = Rp 10.000)",
        $pengembalianRow !== null && (float)$pengembalianRow['denda'] === (float)$expectedDenda,
        "Expected: {$expectedDenda}, Actual: " . ($pengembalianRow['denda'] ?? 'null')
    );
}

// 5. Cleanup Test Data & Restore Baseline Pengaturan
echo "\n[5] Pemulihan Pengaturan ke Nilai Awal & Pembersihan Data Uji...\n";

// Hapus transaksi uji
if ($trx1) {
    $mysqli->query("DELETE FROM pengembalian WHERE peminjaman_id = {$trx1['id']}");
    $mysqli->query("DELETE FROM peminjaman WHERE id = {$trx1['id']}");
    $mysqli->query("UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id = {$buku1Id}");
}
$mysqli->query("DELETE FROM anggota WHERE id = {$anggotaId}");

// Kembalikan pengaturan ke baseline
$restoreCsrf = getCsrfToken($baseUrl, $cookieFile, '/pengaturan');
$restorePayload = [
    'csrf_test_name'        => $restoreCsrf,
    'nama_perpustakaan'     => $baseline['nama_perpustakaan'] ?? 'Perpustakaan SDN 12 Sumbawa',
    'alamat_perpustakaan'   => $baseline['alamat_perpustakaan'] ?? 'Jl. Garuda No. 12',
    'kepala_perpustakaan'   => $baseline['kepala_perpustakaan'] ?? 'Drs. H. Ahmad',
    'nip_kepala'            => $baseline['nip_kepala'] ?? '197501012000031001',
    'durasi_pinjam_default' => $baseline['durasi_pinjam_default'] ?? '5',
    'tarif_denda_per_hari'  => $baseline['tarif_denda_per_hari'] ?? '500',
    'max_pinjam_buku'       => $baseline['max_pinjam_buku'] ?? '2',
    'kiosk_allowed_ips'     => $baseline['kiosk_allowed_ips'] ?? '',
    'kiosk_secret_token'    => $baseline['kiosk_secret_token'] ?? 'KIOSK-SECRET-SDN12',
];

$restoreRes = httpReq($baseUrl . '/pengaturan/update', 'POST', $restorePayload, $cookieFile);
$qFinal = $mysqli->query("SELECT nilai FROM pengaturan WHERE kunci = 'tarif_denda_per_hari'")->fetch_assoc();
assertTest(
    "Pengaturan Berhasil Dipulihkan ke Nilai Baseline ({$baseline['tarif_denda_per_hari']})",
    ($qFinal['nilai'] ?? '') === $baseline['tarif_denda_per_hari']
);

if (file_exists($cookieFile)) unlink($cookieFile);

echo "\n=======================================================\n";
echo "             RINGKASAN HASIL QA PENGATURAN\n";
echo "=======================================================\n";
echo "Total Pengujian : " . ($passCount + $failCount) . "\n";
echo "Passed          : {$passCount}\n";
echo "Failed          : {$failCount}\n";
echo "Status Akhir    : " . ($failCount === 0 ? "SEMUA PENGUJIAN LULUS (SUCCESS)" : "ADA PENGUJIAN GAGAL") . "\n";
echo "=======================================================\n";

exit($failCount === 0 ? 0 : 1);
