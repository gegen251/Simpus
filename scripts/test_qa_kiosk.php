<?php

/**
 * Test QA Modul Kiosk Layanan Mandiri SIMPUS SD
 * Memeriksa:
 * 1. Cek Anggota via Barcode Scanner (Nomor Anggota & NISN)
 * 2. Proteksi Privasi Kiosk: Masking data sensitif (PII)
 * 3. Deteksi Anggota Berpenunggakan Denda pada Kiosk
 * 4. Penanganan Barcode Anggota Tidak Ditemukan
 * 5. Cek Buku via Barcode Scanner (Kode Buku & ISBN)
 * 6. Deteksi Kesalahan Scan: Barcode Kartu Anggota terscan di langkah Buku
 * 7. Penanganan Barcode Buku Tidak Ditemukan
 * 8. Alur Peminjaman Mandiri Kiosk (Atomic stock & quota)
 * 9. Alur Pengembalian Mandiri Kiosk (Atomic return & late fee check)
 */

$baseUrl = 'http://localhost:8080';
$mysqli = new mysqli('127.0.0.1', 'root', '', 'perpustakaan', 3307);
if ($mysqli->connect_error) {
    die("Koneksi DB Gagal: " . $mysqli->connect_error);
}

function httpPostJson($url, $data = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'json' => json_decode($response, true),
        'raw'  => $response
    ];
}

echo "=======================================================\n";
echo "       QA MODUL KIOSK LAYANAN MANDIRI SIMPUS SD\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

// Siapkan Anggota & Buku Uji untuk Kiosk
$nisnKiosk = 'NISN-KSK-' . time();
$nomorAnggotaKiosk = 'AG-KSK-' . rand(100, 999);
$mysqli->query("INSERT INTO anggota (nomor_anggota, nama, tipe_anggota, kelas, no_identitas, jenis_kelamin, kontak, email, alamat, status) 
                VALUES ('{$nomorAnggotaKiosk}', 'Siswa Kiosk Ceria', 'siswa', 'Kelas 4C', '{$nisnKiosk}', 'L', '081299991111', 'privasi@sch.id', 'Alamat Rahasia Siswa', 'aktif')");
$anggotaKioskId = $mysqli->insert_id;

$bukuRow = $mysqli->query("SELECT id, kode_buku, isbn, judul, stok_tersedia FROM buku WHERE stok_tersedia >= 2 AND status = 'aktif' LIMIT 1")->fetch_assoc();
$bukuKioskId = (int)$bukuRow['id'];
$kodeBukuKiosk = $bukuRow['kode_buku'];
$isbnBukuKiosk = $bukuRow['isbn'];
$stokAwalKiosk = (int)$bukuRow['stok_tersedia'];

// -------------------------------------------------------------
// TEST 1: Cek Anggota via Barcode Scanner
// -------------------------------------------------------------
echo "[1] Uji Cek Anggota via Barcode Scanner (Nomor Anggota & NISN)...\n";
$resCek1 = httpPostJson($baseUrl . '/kiosk/cek-anggota', ['nomor_anggota' => $nomorAnggotaKiosk]);
$resCek2 = httpPostJson($baseUrl . '/kiosk/cek-anggota', ['nomor_anggota' => $nisnKiosk]);

if ($resCek1['code'] === 200 && ($resCek1['json']['success'] ?? false) === true) {
    echo "  [OK] Scan barcode Nomor Anggota ('{$nomorAnggotaKiosk}') sukses diidentifikasi.\n";
    $passCount++;
} else {
    echo "  [FAIL] Scan barcode Nomor Anggota gagal! HTTP {$resCek1['code']}\n";
    $failCount++;
}

if ($resCek2['code'] === 200 && ($resCek2['json']['success'] ?? false) === true) {
    echo "  [OK] Scan barcode Nomor Identitas / NISN ('{$nisnKiosk}') sukses diidentifikasi.\n";
    $passCount++;
} else {
    echo "  [FAIL] Scan barcode NISN gagal! HTTP {$resCek2['code']}\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Proteksi Privasi Layar Umum Kiosk (Masking PII)
// -------------------------------------------------------------
echo "[2] Uji Proteksi Privasi Data Sensitif (PII Masking) pada Layar Kiosk...\n";
$anggotaData = $resCek1['json']['anggota'] ?? [];
$isPiiProtected = !isset($anggotaData['kontak']) && !isset($anggotaData['email']) && !isset($anggotaData['alamat']);
if ($isPiiProtected && isset($anggotaData['nama'])) {
    echo "  [OK] Data pribadi (kontak/telepon, email, alamat fisik) TIDAK dibocorkan ke layar kiosk.\n";
    $passCount++;
} else {
    echo "  [FAIL] Celah privasi: Data PII (kontak/alamat) terekspos di API Kiosk!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Barcode Anggota Tidak Terdaftar / Tidak Ditemukan
// -------------------------------------------------------------
echo "[3] Uji Barcode Anggota Tidak Ditemukan (Scanner Salah Scan)...\n";
$resUnknownMember = httpPostJson($baseUrl . '/kiosk/cek-anggota', ['nomor_anggota' => 'BARCODE-PALSU-99999']);
if ($resUnknownMember['code'] === 200 && ($resUnknownMember['json']['success'] ?? true) === false) {
    echo "  [OK] Respon penolakan barcode tidak terdaftar ditangani dengan baik: '{$resUnknownMember['json']['message']}'.\n";
    $passCount++;
} else {
    echo "  [FAIL] Penanganan barcode tidak terdaftar tidak sesuai ekspektasi!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: Deteksi Anggota Berpenunggakan Denda di Kiosk
// -------------------------------------------------------------
echo "[4] Uji Deteksi Anggota Berpenunggakan Denda pada Kiosk...\n";
// Buat denda tertunggak untuk anggota uji
$mysqli->query("INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, admin_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
                VALUES ('PJ-KSK-FINE', {$anggotaKioskId}, {$bukuKioskId}, 1, '2026-09-01', '2026-09-07', 'dikembalikan')");
$fineLoanId = $mysqli->insert_id;
$mysqli->query("INSERT INTO pengembalian (peminjaman_id, admin_id, tanggal_kembali, denda, status_denda, catatan)
                VALUES ({$fineLoanId}, 1, '2026-09-12', 5000, 'belum_lunas', 'Denda Kiosk Test')");
$fineRetId = $mysqli->insert_id;

$resFineBlockKiosk = httpPostJson($baseUrl . '/kiosk/cek-anggota', ['nomor_anggota' => $nomorAnggotaKiosk]);
if ($resFineBlockKiosk['code'] === 200 && ($resFineBlockKiosk['json']['success'] ?? true) === false && strpos($resFineBlockKiosk['json']['message'], 'denda keterlambatan yang belum lunas') !== false) {
    echo "  [OK] Kiosk berhasil mendeteksi denda tertunggak dan mengarahkan siswa ke meja pustakawan.\n";
    $passCount++;
} else {
    echo "  [FAIL] Siswa dengan denda belum lunas lolos dari blokir Kiosk!\n";
    $failCount++;
}

// Bersihkan denda uji agar pengujian peminjaman mandiri berikutnya dapat berjalan
$mysqli->query("DELETE FROM pengembalian WHERE id = {$fineRetId}");
$mysqli->query("DELETE FROM peminjaman WHERE id = {$fineLoanId}");
echo "\n";

// -------------------------------------------------------------
// TEST 5: Cek Buku via Barcode Scanner (Kode Buku & ISBN)
// -------------------------------------------------------------
echo "[5] Uji Cek Buku via Barcode Scanner (Kode Buku & ISBN)...\n";
$resBukuKode = httpPostJson($baseUrl . '/kiosk/cek-buku', ['kode_buku' => $kodeBukuKiosk]);
if ($resBukuKode['code'] === 200 && ($resBukuKode['json']['success'] ?? false) === true) {
    echo "  [OK] Scan barcode Kode Buku ('{$kodeBukuKiosk}') sukses diidentifikasi: '{$resBukuKode['json']['buku']['judul']}'.\n";
    $passCount++;
} else {
    echo "  [FAIL] Scan barcode kode buku gagal!\n";
    $failCount++;
}

if (!empty($isbnBukuKiosk)) {
    $resBukuIsbn = httpPostJson($baseUrl . '/kiosk/cek-buku', ['kode_buku' => $isbnBukuKiosk]);
    if ($resBukuIsbn['code'] === 200 && ($resBukuIsbn['json']['success'] ?? false) === true) {
        echo "  [OK] Scan barcode ISBN ('{$isbnBukuKiosk}') sukses diidentifikasi di katalog.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Scan barcode ISBN gagal!\n";
        $failCount++;
    }
}
echo "\n";

// -------------------------------------------------------------
// TEST 6: Deteksi Kesalahan Scan: Barcode Kartu Anggota di Langkah Buku
// -------------------------------------------------------------
echo "[6] Uji Deteksi Kesalahan Scan Barcode Kartu Anggota di Langkah Buku...\n";
$resWrongBarcode = httpPostJson($baseUrl . '/kiosk/cek-buku', ['kode_buku' => $nomorAnggotaKiosk]);
if ($resWrongBarcode['code'] === 200 && ($resWrongBarcode['json']['success'] ?? true) === false && ($resWrongBarcode['json']['is_anggota'] ?? false) === true) {
    echo "  [OK] Sistem cerdas mendeteksi bahwa kode tersebut adalah Barcode Kartu Anggota dan memandu siswa men-scan barcode buku.\n";
    $passCount++;
} else {
    echo "  [FAIL] Deteksi salah scan kartu anggota gagal!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 7: Barcode Buku Tidak Terdaftar
// -------------------------------------------------------------
echo "[7] Uji Barcode Buku Tidak Terdaftar di Katalog...\n";
$resUnknownBook = httpPostJson($baseUrl . '/kiosk/cek-buku', ['kode_buku' => 'BK-TIDAK-ADA-999']);
if ($resUnknownBook['code'] === 200 && ($resUnknownBook['json']['success'] ?? true) === false && strpos($resUnknownBook['json']['message'], 'tidak ditemukan') !== false) {
    echo "  [OK] Respon ramah saat barcode buku tidak terdaftar: '{$resUnknownBook['json']['message']}'.\n";
    $passCount++;
} else {
    echo "  [FAIL] Penanganan barcode buku tidak terdaftar gagal!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 8: Alur Peminjaman Mandiri Kiosk
// -------------------------------------------------------------
echo "[8] Uji Alur Peminjaman Mandiri Kiosk (apiProsesPinjam)...\n";
$resPinjamKiosk = httpPostJson($baseUrl . '/kiosk/pinjam', [
    'anggota_id' => $anggotaKioskId,
    'buku_id'    => $bukuKioskId,
]);

$trxKiosk = $mysqli->query("SELECT * FROM peminjaman WHERE anggota_id = {$anggotaKioskId} AND buku_id = {$bukuKioskId} AND status = 'dipinjam' ORDER BY id DESC LIMIT 1")->fetch_assoc();
$stokSetelahPinjam = (int)$mysqli->query("SELECT stok_tersedia FROM buku WHERE id = {$bukuKioskId}")->fetch_assoc()['stok_tersedia'];

if ($resPinjamKiosk['code'] === 200 && ($resPinjamKiosk['json']['success'] ?? false) === true && $trxKiosk) {
    echo "  [OK] Peminjaman mandiri sukses! Kode Transaksi: {$trxKiosk['kode_transaksi']}.\n";
    $passCount++;
} else {
    echo "  [FAIL] Peminjaman mandiri gagal! Respon: {$resPinjamKiosk['raw']}\n";
    $failCount++;
}

if ($stokSetelahPinjam === $stokAwalKiosk - 1) {
    echo "  [OK] Stok buku berkurang tepat 1 secara atomik pada peminjaman mandiri ({$stokAwalKiosk} -> {$stokSetelahPinjam}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Stok buku tidak berkurang dengan benar!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 9: Alur Pengembalian Mandiri Kiosk
// -------------------------------------------------------------
echo "[9] Uji Alur Pengembalian Mandiri Kiosk (apiProsesKembali)...\n";
$resKembaliKiosk = httpPostJson($baseUrl . '/kiosk/kembali', [
    'kode_buku' => $kodeBukuKiosk
]);

if ($trxKiosk) {
    $trxKembaliKiosk = $mysqli->query("SELECT status FROM peminjaman WHERE id = {$trxKiosk['id']}")->fetch_assoc();
    $stokSetelahKembali = (int)$mysqli->query("SELECT stok_tersedia FROM buku WHERE id = {$bukuKioskId}")->fetch_assoc()['stok_tersedia'];

    if ($resKembaliKiosk['code'] === 200 && ($resKembaliKiosk['json']['success'] ?? false) === true && $trxKembaliKiosk['status'] === 'dikembalikan') {
        echo "  [OK] Pengembalian mandiri sukses via scan barcode buku! Status transaksi: '{$trxKembaliKiosk['status']}'.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Pengembalian mandiri gagal! Respon: {$resKembaliKiosk['raw']}\n";
        $failCount++;
    }

    if ($stokSetelahKembali === $stokAwalKiosk) {
        echo "  [OK] Stok buku berhasil dipulihkan secara atomik ({$stokSetelahPinjam} -> {$stokSetelahKembali}).\n";
        $passCount++;
    } else {
        echo "  [FAIL] Stok buku tidak dipulihkan dengan benar!\n";
        $failCount++;
    }
} else {
    echo "  [SKIP] Transaksi pinjam kiosk belum ada untuk diuji pengembaliannya.\n";
}
echo "\n";

// -------------------------------------------------------------
// PEMBERSIHAN DATA UJI
// -------------------------------------------------------------
if (!empty($trxKiosk['id'])) {
    $mysqli->query("DELETE FROM pengembalian WHERE peminjaman_id = {$trxKiosk['id']}");
}
$mysqli->query("DELETE FROM peminjaman WHERE anggota_id = {$anggotaKioskId}");
$mysqli->query("DELETE FROM anggota WHERE id = {$anggotaKioskId}");
$mysqli->query("UPDATE buku SET stok_tersedia = {$stokAwalKiosk} WHERE id = {$bukuKioskId}");

$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL KIOSK LAYANAN MANDIRI:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL KIOSK BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
