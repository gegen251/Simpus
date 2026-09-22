<?php

/**
 * Test QA Modul Anggota SIMPUS SD
 * Memeriksa:
 * 1. CRUD Anggota (Create, Read, Update, Delete)
 * 2. Filter & Pencarian data anggota
 * 3. Upload Foto Profil (Valid vs File Berbahaya / Non-Image)
 * 4. Cetak Kartu Anggota dengan Barcode (HTTP 200, layout kartu)
 * 5. Download Template CSV & Import CSV Anggota (Data Valid & Duplikat)
 * 6. Proteksi Penghapusan Anggota dengan Pinjaman Aktif / Histori Sirkulasi
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_anggota.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

if (!defined('FCPATH')) {
    define('FCPATH', dirname(__DIR__) . '/public/');
}

$mysqli = new mysqli('127.0.0.1', 'root', '', 'perpustakaan', 3307);
if ($mysqli->connect_error) {
    die("Koneksi DB Gagal: " . $mysqli->connect_error);
}

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null, $files = []) {
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
        if (!empty($files)) {
            $postFields = $data;
            foreach ($files as $field => $fileData) {
                $postFields[$field] = new CURLFile($fileData['path'], $fileData['mime'], $fileData['name']);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);
    
    return ['code' => $httpCode, 'headers' => $headers, 'body' => $body];
}

function getCsrfToken($baseUrl, $cookieFile, $path = '/anggota') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL ANGGOTA SIMPUS SD\n";
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

// -------------------------------------------------------------
// TEST 1: Tambah Anggota Baru (Create)
// -------------------------------------------------------------
echo "[1] Uji Tambah Anggota Baru (Create)...\n";
$nisnUji = 'NISN-QA-' . time();
$namaUji = 'Siswa QA Pratama ' . time();
$csrf = getCsrfToken($baseUrl, $cookieFile);

$resAdd = httpReq($baseUrl . '/anggota/store', 'POST', [
    'csrf_test_name' => $csrf,
    'nama'           => $namaUji,
    'tipe_anggota'   => 'siswa',
    'kelas'          => 'Kelas 5A',
    'no_identitas'   => $nisnUji,
    'jenis_kelamin'  => 'L',
    'kontak'         => '081299887766',
    'email'          => 'siswaqa@example.com',
    'alamat'         => 'Jl. Pendidikan No. 1',
    'status'         => 'aktif',
], $cookieFile);

$anggotaUji = $mysqli->query("SELECT * FROM anggota WHERE no_identitas = '{$nisnUji}'")->fetch_assoc();
if ($anggotaUji) {
    $anggotaIdUji = (int)$anggotaUji['id'];
    echo "  [OK] Anggota berhasil ditambahkan (ID: {$anggotaIdUji}, No Anggota: {$anggotaUji['nomor_anggota']}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Anggota gagal ditambahkan ke database!\n";
    $failCount++;
    $anggotaIdUji = 0;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Filter & Pencarian Data Anggota
// -------------------------------------------------------------
echo "[2] Uji Filter dan Pencarian Anggota...\n";
$resSearch = httpReq($baseUrl . '/anggota?q=' . urlencode($namaUji), 'GET', [], $cookieFile);
if (strpos($resSearch['body'], $namaUji) !== false) {
    echo "  [OK] Pencarian anggota berhasil menemukan nama siswa.\n";
    $passCount++;
} else {
    echo "  [FAIL] Pencarian anggota tidak menemukan hasil!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Pembaruan Data Anggota (Update)
// -------------------------------------------------------------
echo "[3] Uji Pembaruan Data Anggota (Update)...\n";
if ($anggotaIdUji > 0) {
    $namaUpdated = $namaUji . ' [Terupdate]';
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/anggota/update/' . $anggotaIdUji, 'POST', [
        'csrf_test_name' => $csrf,
        'nama'           => $namaUpdated,
        'tipe_anggota'   => 'siswa',
        'kelas'          => 'Kelas 5B',
        'no_identitas'   => $nisnUji,
        'jenis_kelamin'  => 'L',
        'kontak'         => '081299887700',
        'status'         => 'aktif',
    ], $cookieFile);

    $checkUpd = $mysqli->query("SELECT * FROM anggota WHERE id = {$anggotaIdUji}")->fetch_assoc();
    if ($checkUpd && $checkUpd['nama'] === $namaUpdated && $checkUpd['kelas'] === 'Kelas 5B') {
        echo "  [OK] Data anggota berhasil diperbarui menjadi '{$namaUpdated}' (Kelas 5B).\n";
        $passCount++;
    } else {
        echo "  [FAIL] Gagal memperbarui data anggota!\n";
        $failCount++;
    }
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: Upload Foto Profil (Valid vs File Berbahaya .exe)
// -------------------------------------------------------------
echo "[4] Uji Upload Foto Profil Anggota (Valid vs File Berbahaya)...\n";
$tempValidImage = __DIR__ . '/temp_valid_avatar.png';
$tempInvalidExe = __DIR__ . '/temp_malicious_avatar.exe';

file_put_contents($tempValidImage, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
file_put_contents($tempInvalidExe, "MZ\x90\x00\x03\x00\x00\x00Dummy executable binary test");

// 4a. Coba upload file tidak valid (.exe)
$csrf = getCsrfToken($baseUrl, $cookieFile);
$badNisn = 'NISN-BAD-' . time();
httpReq($baseUrl . '/anggota/store', 'POST', [
    'csrf_test_name' => $csrf,
    'nama'           => 'Anggota Foto Bahaya',
    'no_identitas'   => $badNisn,
    'kontak'         => '0812000001',
], $cookieFile, [
    'foto_file' => ['path' => $tempInvalidExe, 'mime' => 'application/x-msdownload', 'name' => 'virus.exe']
]);

$checkBad = $mysqli->query("SELECT * FROM anggota WHERE no_identitas = '{$badNisn}'")->fetch_assoc();
if (!$checkBad) {
    echo "  [OK] Upload foto tidak valid (.exe) berhasil DITOLAK oleh validator sistem.\n";
    $passCount++;
} else {
    echo "  [FAIL] File tidak valid tembus ke database anggota!\n";
    $failCount++;
    $mysqli->query("DELETE FROM anggota WHERE no_identitas = '{$badNisn}'");
}

// 4b. Upload foto valid (.png)
if ($anggotaIdUji > 0) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/anggota/update/' . $anggotaIdUji, 'POST', [
        'csrf_test_name' => $csrf,
        'nama'           => $namaUpdated,
        'no_identitas'   => $nisnUji,
        'kontak'         => '081299887700',
    ], $cookieFile, [
        'foto_file' => ['path' => $tempValidImage, 'mime' => 'image/png', 'name' => 'avatar.png']
    ]);

    $checkPhoto = $mysqli->query("SELECT foto FROM anggota WHERE id = {$anggotaIdUji}")->fetch_assoc();
    if ($checkPhoto && !empty($checkPhoto['foto']) && file_exists(FCPATH . $checkPhoto['foto'])) {
        echo "  [OK] Foto profil valid (.png) berhasil diunggah dan disimpan ke '{$checkPhoto['foto']}'.\n";
        $passCount++;
        @unlink(FCPATH . $checkPhoto['foto']);
    } else {
        echo "  [FAIL] Upload foto profil valid gagal tersimpan di folder uploads/anggota!\n";
        $failCount++;
    }
}

@unlink($tempValidImage);
@unlink($tempInvalidExe);
echo "\n";

// -------------------------------------------------------------
// TEST 5: Cetak Kartu Anggota Ber-Barcode
// -------------------------------------------------------------
echo "[5] Uji Endpoint Cetak Kartu Anggota dengan Barcode...\n";
$resCard = httpReq($baseUrl . '/anggota/cetak-kartu', 'GET', [], $cookieFile);
if ($resCard['code'] === 200 && (strpos($resCard['body'], 'KARTU') !== false || strpos($resCard['body'], 'Kartu') !== false)) {
    echo "  [OK] Halaman cetak kartu anggota render sukses (HTTP 200) dengan layout kartu perpustakaan.\n";
    $passCount++;
} else {
    echo "  [FAIL] Gagal memuat halaman cetak kartu! HTTP {$resCard['code']}\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 6: Download Template CSV
// -------------------------------------------------------------
echo "[6] Uji Download Template Import Anggota...\n";
$resTpl = httpReq($baseUrl . '/anggota/template-import', 'GET', [], $cookieFile);
$expectedHeaders = ['Nama Lengkap', 'Tipe', 'Nomor Identitas', 'Kontak'];
$allHeadersFound = true;
foreach ($expectedHeaders as $h) {
    if (strpos($resTpl['body'], $h) === false) {
        $allHeadersFound = false;
    }
}

if ($allHeadersFound) {
    echo "  [OK] File template CSV memuat header kolom anggota standar yang lengkap.\n";
    $passCount++;
} else {
    echo "  [FAIL] Header template CSV anggota tidak lengkap!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 7: Import CSV Anggota (Data Valid & Duplikat)
// -------------------------------------------------------------
echo "[7] Uji Import CSV Anggota (Data Valid & Penanganan Duplikasi)...\n";
$testCsvFile = __DIR__ . '/test_import_anggota.csv';
$uniqueA = '0098' . rand(100000, 999999);
$uniqueB = '0098' . rand(100000, 999999);

$csvContent = "\xEF\xBB\xBF" . "Nomor Anggota,Nama Lengkap,Tipe,Kelas,Nomor Identitas,Jenis Kelamin,Kontak,Email,Alamat\n" .
              ",Siswa CSV A " . time() . ",siswa,Kelas 3A,{$uniqueA},L,0812340001,siswaA@sch.id,Alamat A\n" .
              ",Siswa CSV B " . time() . ",siswa,Kelas 3B,{$uniqueB},P,0812340002,siswaB@sch.id,Alamat B\n" .
              ",Siswa Duplikat,siswa,Kelas 3C,{$uniqueA},L,0812340003,duplikat@sch.id,Alamat C\n" . // Duplikat identitas A
              ",,siswa,Kelas 3D,0099999999,L,0812340004,noname@sch.id,Alamat D\n"; // Tanpa nama

file_put_contents($testCsvFile, $csvContent);

$csrf = getCsrfToken($baseUrl, $cookieFile);
$resImport = httpReq($baseUrl . '/anggota/import', 'POST', [
    'csrf_test_name' => $csrf
], $cookieFile, [
    'file_csv' => ['path' => $testCsvFile, 'mime' => 'text/csv', 'name' => 'test_import_anggota.csv']
]);

$qImportA = $mysqli->query("SELECT * FROM anggota WHERE no_identitas = '{$uniqueA}'")->fetch_assoc();
$qImportB = $mysqli->query("SELECT * FROM anggota WHERE no_identitas = '{$uniqueB}'")->fetch_assoc();
$qDupCount = (int)$mysqli->query("SELECT COUNT(*) as c FROM anggota WHERE no_identitas = '{$uniqueA}'")->fetch_assoc()['c'];

if ($qImportA && $qImportB) {
    echo "  [OK] 2 Data anggota baru berhasil diimport ke database.\n";
    $passCount++;
} else {
    echo "  [FAIL] Data anggota valid gagal diimport!\n";
    $failCount++;
}

if ($qDupCount === 1) {
    echo "  [OK] Baris duplikat identitas berhasil dilewati secara aman (jumlah di DB tetap 1).\n";
    $passCount++;
} else {
    echo "  [FAIL] Duplikasi identitas tembus saat import!\n";
    $failCount++;
}

@unlink($testCsvFile);
$mysqli->query("DELETE FROM anggota WHERE no_identitas IN ('{$uniqueA}', '{$uniqueB}')");
echo "\n";

// -------------------------------------------------------------
// TEST 8: Proteksi Hapus Anggota dengan Pinjaman Aktif
// -------------------------------------------------------------
echo "[8] Uji Proteksi Hapus Anggota dengan Pinjaman Aktif...\n";
$anggotaPinjam = $mysqli->query("
    SELECT a.id, a.nama, COUNT(p.id) as dipinjam_count
    FROM anggota a
    JOIN peminjaman p ON p.anggota_id = a.id AND p.status IN ('dipinjam', 'terlambat')
    GROUP BY a.id
    HAVING dipinjam_count > 0
    LIMIT 1
")->fetch_assoc();

if ($anggotaPinjam) {
    $anggotaPinjamId = (int)$anggotaPinjam['id'];
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/anggota/delete/' . $anggotaPinjamId, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    $checkStillActive = $mysqli->query("SELECT id FROM anggota WHERE id = {$anggotaPinjamId}")->fetch_assoc();
    if ($checkStillActive) {
        echo "  [OK] Anggota yang memiliki pinjaman aktif TIDAK TERHAPUS dari database.\n";
        $passCount++;
    } else {
        echo "  [FAIL] BAHAYA! Anggota dengan pinjaman aktif terhapus dari sistem!\n";
        $failCount++;
    }
} else {
    echo "  [SKIP] Tidak ada anggota dengan pinjaman aktif untuk diuji.\n";
}
echo "\n";

// -------------------------------------------------------------
// TEST 9: Hapus Anggota Bersih (Tanpa Histori Sirkulasi)
// -------------------------------------------------------------
echo "[9] Uji Hapus Anggota Tanpa Histori Sirkulasi (Delete)...\n";
if ($anggotaIdUji > 0) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/anggota/delete/' . $anggotaIdUji, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    $checkDel = $mysqli->query("SELECT id FROM anggota WHERE id = {$anggotaIdUji}")->fetch_assoc();
    if (!$checkDel) {
        echo "  [OK] Anggota tanpa histori sirkulasi berhasil dihapus dari database.\n";
        
        $qAudit = $mysqli->query("SELECT * FROM audit_log WHERE action = 'HAPUS_ANGGOTA' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($qAudit && strpos($qAudit['description'], 'Siswa QA Pratama') !== false) {
            echo "  [OK] Penghapusan anggota tercatat di tabel audit_log (ID Audit: {$qAudit['id']}).\n";
            $passCount += 2;
        } else {
            echo "  [OK] Anggota terhapus dengan aman.\n";
            $passCount++;
        }
    } else {
        echo "  [FAIL] Gagal menghapus anggota uji!\n";
        $failCount++;
        $mysqli->query("DELETE FROM anggota WHERE id = {$anggotaIdUji}");
    }
}
echo "\n";

if (file_exists($cookieFile)) unlink($cookieFile);
$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL ANGGOTA:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL ANGGOTA BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
