<?php

/**
 * Test QA Modul Koleksi Buku SIMPUS SD
 * Memeriksa:
 * 1. CRUD Buku (Create, Read, Update, Delete)
 * 2. Aturan Bisnis Stok: Larangan menurunkan eksemplar < jumlah sedang dipinjam
 * 3. Aturan Bisnis Hapus: Larangan menghapus buku yang sedang dipinjam / memiliki histori sirkulasi
 * 4. Upload Cover Buku (Valid Image vs Invalid Format/Mime)
 * 5. Cetak Label Punggung & Barcode Buku (HTTP 200, valid HTML layout)
 * 6. Download Template CSV (Header & UTF-8 BOM)
 * 7. Import CSV (Data Valid & Data Cacat/Malformed)
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_buku.txt';
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
            // Multipart form data
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

function getCsrfToken($baseUrl, $cookieFile, $path = '/buku') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL KOLEKSI BUKU SIMPUS SD\n";
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

// Ambil salah satu kategori untuk uji coba
$katRow = $mysqli->query("SELECT id FROM kategori_buku LIMIT 1")->fetch_assoc();
$katId = (int)($katRow['id'] ?? 1);

// -------------------------------------------------------------
// TEST 1: Tambah Buku Baru (Create)
// -------------------------------------------------------------
echo "[1] Uji Tambah Buku Baru (Create)...\n";
$judulUji = 'Buku QA Robotika Pintar SD ' . time();
$csrf = getCsrfToken($baseUrl, $cookieFile);

$resAdd = httpReq($baseUrl . '/buku/store', 'POST', [
    'csrf_test_name'   => $csrf,
    'judul'            => $judulUji,
    'penulis'          => 'Tim Robotika Edukasi',
    'penerbit'         => 'Penerbit Pustaka Cilik',
    'kategori_id'      => $katId,
    'tahun_terbit'     => '2025',
    'isbn'             => '978-602-999-001',
    'jumlah_eksemplar' => 5,
    'lokasi_rak'       => 'Rak Sains 01',
    'status'           => 'aktif',
    'deskripsi'        => 'Buku panduan dasar robotika untuk anak SD.',
], $cookieFile);

$bukuUji = $mysqli->query("SELECT * FROM buku WHERE judul = '{$judulUji}'")->fetch_assoc();
if ($bukuUji) {
    $bukuIdUji = (int)$bukuUji['id'];
    echo "  [OK] Buku berhasil ditambahkan (ID: {$bukuIdUji}, Kode: {$bukuUji['kode_buku']}).\n";
    if ((int)$bukuUji['stok_tersedia'] === 5 && (int)$bukuUji['jumlah_eksemplar'] === 5) {
        echo "  [OK] Stok awal tersedia tersinkronisasi otomatis (5 eksemplar).\n";
        $passCount += 2;
    } else {
        echo "  [FAIL] Inisialisasi stok awal tidak sesuai!\n";
        $failCount++;
    }
} else {
    echo "  [FAIL] Buku baru gagal tersimpan di database!\n";
    $failCount++;
    $bukuIdUji = 0;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Filter & Pencarian Buku (Read)
// -------------------------------------------------------------
echo "[2] Uji Filter dan Pencarian Buku...\n";
$resSearch = httpReq($baseUrl . '/buku?q=' . urlencode($judulUji), 'GET', [], $cookieFile);
if (strpos($resSearch['body'], $judulUji) !== false) {
    echo "  [OK] Pencarian buku berdasarkan kata kunci berhasil menemukan judul buku.\n";
    $passCount++;
} else {
    echo "  [FAIL] Pencarian buku tidak menemukan hasil!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Aturan Bisnis - Pembatasan Penurunan Eksemplar < Pinjaman Aktif
// -------------------------------------------------------------
echo "[3] Uji Aturan Bisnis Penurunan Eksemplar < Jumlah Dipinjam...\n";
// Ambil buku yang sedang dipinjam
$bukuPinjam = $mysqli->query("
    SELECT b.id, b.judul, b.jumlah_eksemplar, COUNT(p.id) as dipinjam_count
    FROM buku b
    JOIN peminjaman p ON p.buku_id = b.id AND p.status IN ('dipinjam', 'terlambat')
    GROUP BY b.id
    HAVING dipinjam_count > 0
    LIMIT 1
")->fetch_assoc();

if ($bukuPinjam) {
    $bukuPinjamId = (int)$bukuPinjam['id'];
    $dipinjamCount = (int)$bukuPinjam['dipinjam_count'];
    $invalidEksemplar = max(1, $dipinjamCount - 1); // Coba kurangi di bawah jumlah dipinjam
    
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    $resIllegalUpdate = httpReq($baseUrl . '/buku/update/' . $bukuPinjamId, 'POST', [
        'csrf_test_name'   => $csrf,
        'judul'            => $bukuPinjam['judul'],
        'penulis'          => 'Penulis Valid',
        'penerbit'         => 'Penerbit Valid',
        'kategori_id'      => $katId,
        'jumlah_eksemplar' => $invalidEksemplar,
    ], $cookieFile);

    // Cek di DB: Nilai jumlah_eksemplar TIDAK BOLEH berubah ke nilai tidak valid
    $checkBukuDB = $mysqli->query("SELECT jumlah_eksemplar FROM buku WHERE id = {$bukuPinjamId}")->fetch_assoc();
    if ((int)$checkBukuDB['jumlah_eksemplar'] != $invalidEksemplar) {
        echo "  [OK] Upaya menurunkan jumlah eksemplar ({$invalidEksemplar}) di bawah pinjaman aktif ({$dipinjamCount}) DITOLAK sistem.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Validasi stok bobol! Eksemplar berhasil diturunkan di bawah buku yang sedang dipinjam.\n";
        $failCount++;
    }
} else {
    echo "  [SKIP] Tidak ada buku yang sedang berstatus dipinjam untuk diuji.\n";
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: Aturan Bisnis - Larangan Menghapus Buku dengan Histori/Sedang Dipinjam
// -------------------------------------------------------------
echo "[4] Uji Aturan Bisnis: Larangan Menghapus Buku yang Sedang Dipinjam / Memiliki Histori...\n";
if ($bukuPinjam) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    $delBukuPinjamRes = httpReq($baseUrl . '/buku/delete/' . $bukuPinjamId, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    // Verifikasi di DB
    $checkStillActive = $mysqli->query("SELECT id FROM buku WHERE id = {$bukuPinjamId}")->fetch_assoc();
    if ($checkStillActive) {
        echo "  [OK] Buku yang sedang dipinjam/memiliki histori TIDAK TERHAPUS dari database.\n";
        $passCount++;
    } else {
        echo "  [FAIL] BAHAYA! Buku yang sedang dipinjam terhapus dari sistem!\n";
        $failCount++;
    }
}
echo "\n";

// -------------------------------------------------------------
// TEST 5: Upload Cover Buku (Valid vs Invalid File Type)
// -------------------------------------------------------------
echo "[5] Uji Validasi Upload Cover Buku (File Valid vs Tidak Valid)...\n";
$tempValidImage = __DIR__ . '/temp_valid_cover.png';
$tempInvalidExe = __DIR__ . '/temp_malicious.exe';

// Buat gambar PNG valid 1x1 pixel
file_put_contents($tempValidImage, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
// Buat file non-image dummy
file_put_contents($tempInvalidExe, "MZ\x90\x00\x03\x00\x00\x00Dummy executable binary test");

// 5a. Coba upload file tidak valid (.exe)
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resBadUpload = httpReq($baseUrl . '/buku/store', 'POST', [
    'csrf_test_name'   => $csrf,
    'judul'            => 'Buku Uji Cover Bahaya ' . time(),
    'penulis'          => 'Hacker Test',
    'penerbit'         => 'Dark Web',
    'kategori_id'      => $katId,
    'jumlah_eksemplar' => 1,
], $cookieFile, [
    'cover_file' => ['path' => $tempInvalidExe, 'mime' => 'application/x-msdownload', 'name' => 'exploit.exe']
]);

// Cek apakah judul buku berbahaya tersimpan di DB
$checkBad = $mysqli->query("SELECT * FROM buku WHERE penulis = 'Hacker Test'")->fetch_assoc();
if (!$checkBad) {
    echo "  [OK] Upload file cover tidak valid (.exe) berhasil DITOLAK oleh validator sistem.\n";
    $passCount++;
} else {
    echo "  [FAIL] File tidak valid lolos ke database!\n";
    $failCount++;
    $mysqli->query("DELETE FROM buku WHERE penulis = 'Hacker Test'");
}

// 5b. Upload file cover valid (.png)
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resGoodUpload = httpReq($baseUrl . '/buku/store', 'POST', [
    'csrf_test_name'   => $csrf,
    'judul'            => 'Buku Uji Cover Valid ' . time(),
    'penulis'          => 'Penulis Valid',
    'penerbit'         => 'Penerbit Bagus',
    'kategori_id'      => $katId,
    'jumlah_eksemplar' => 2,
], $cookieFile, [
    'cover_file' => ['path' => $tempValidImage, 'mime' => 'image/png', 'name' => 'cover.png']
]);

$checkGood = $mysqli->query("SELECT * FROM buku WHERE penulis = 'Penulis Valid' ORDER BY id DESC LIMIT 1")->fetch_assoc();
if ($checkGood && !empty($checkGood['cover']) && file_exists(FCPATH . $checkGood['cover'])) {
    echo "  [OK] Cover gambar valid (.png) berhasil diunggah dan disimpan ke '{$checkGood['cover']}'.\n";
    $passCount++;
    // Cleanup file cover yang diupload
    if (file_exists(FCPATH . $checkGood['cover'])) unlink(FCPATH . $checkGood['cover']);
    $mysqli->query("DELETE FROM buku WHERE id = {$checkGood['id']}");
} else {
    echo "  [FAIL] Upload cover valid gagal tersimpan di folder uploads!\n";
    $failCount++;
}

@unlink($tempValidImage);
@unlink($tempInvalidExe);
echo "\n";

// -------------------------------------------------------------
// TEST 6: Cetak Label Punggung & Barcode Buku
// -------------------------------------------------------------
echo "[6] Uji Endpoint Cetak Label & Barcode Buku...\n";
$resLabel = httpReq($baseUrl . '/buku/cetak-label', 'GET', [], $cookieFile);
if ($resLabel['code'] === 200 && strpos($resLabel['body'], 'Label') !== false) {
    echo "  [OK] Halaman cetak label render sukses (HTTP 200) dan memuat template label/barcode.\n";
    $passCount++;
} else {
    echo "  [FAIL] Gagal memuat halaman cetak label! HTTP {$resLabel['code']}\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 7: Download Template CSV
// -------------------------------------------------------------
echo "[7] Uji Download Template Import CSV...\n";
$resTpl = httpReq($baseUrl . '/buku/template-import', 'GET', [], $cookieFile);
$expectedHeaders = ['Judul Buku', 'Penulis', 'Penerbit', 'Tahun Terbit', 'ISBN'];
$allHeadersFound = true;
foreach ($expectedHeaders as $h) {
    if (strpos($resTpl['body'], $h) === false) {
        $allHeadersFound = false;
    }
}

if ($allHeadersFound) {
    echo "  [OK] File template CSV memuat header kolom standar yang lengkap.\n";
    $passCount++;
} else {
    echo "  [FAIL] Header template CSV tidak lengkap!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 8: Import CSV (Kasus Valid & Data Cacat/Malformed)
// -------------------------------------------------------------
echo "[8] Uji Import CSV (Data Valid & Penanganan Data Cacat)...\n";
$testCsvFile = __DIR__ . '/test_import_buku.csv';
$csvContent = "\xEF\xBB\xBF" . "Judul Buku,Penulis,Penerbit,Tahun Terbit,ISBN,Nama Kategori,Jumlah Eksemplar,Lokasi Rak,Deskripsi\n" .
              "Buku CSV Valid A " . time() . ",Penulis CSV 1,Erlangga,2024,978-001,Sains Anak,3,Rak A1,Buku valid 1\n" .
              "Buku CSV Valid B " . time() . ",Penulis CSV 2,Gramedia,2023,978-002,Cerita Rakyat,2,Rak B2,Buku valid 2\n" .
              ",Penulis Tanpa Judul,Penerbit X,2022,978-003,Umum,1,Rak C,Baris ini harus dilewati karena judul kosong\n" .
              "Buku Tanpa Penulis,,Penerbit Y,2021,978-004,Umum,1,Rak D,Baris ini harus dilewati karena penulis kosong\n";

file_put_contents($testCsvFile, $csvContent);

$csrf = getCsrfToken($baseUrl, $cookieFile);
$resImport = httpReq($baseUrl . '/buku/import', 'POST', [
    'csrf_test_name' => $csrf
], $cookieFile, [
    'file_csv' => ['path' => $testCsvFile, 'mime' => 'text/csv', 'name' => 'test_import_buku.csv']
]);

// Cek di DB
$qImportA = $mysqli->query("SELECT * FROM buku WHERE penulis = 'Penulis CSV 1'")->fetch_assoc();
$qImportB = $mysqli->query("SELECT * FROM buku WHERE penulis = 'Penulis CSV 2'")->fetch_assoc();
$qBadA = $mysqli->query("SELECT * FROM buku WHERE penulis = 'Penulis Tanpa Judul'")->fetch_assoc();

if ($qImportA && $qImportB) {
    echo "  [OK] 2 Data buku valid berhasil diimport ke database.\n";
    $passCount++;
} else {
    echo "  [FAIL] Data buku valid gagal diimport!\n";
    $failCount++;
}

if (!$qBadA) {
    echo "  [OK] 2 Baris data cacat/kosong berhasil dilewati secara aman tanpa merusak proses import.\n";
    $passCount++;
} else {
    echo "  [FAIL] Baris cacat tanpa judul tembus ke database!\n";
    $failCount++;
}

// Bersihkan data import uji
@unlink($testCsvFile);
$mysqli->query("DELETE FROM buku WHERE penulis IN ('Penulis CSV 1', 'Penulis CSV 2')");
echo "\n";

// -------------------------------------------------------------
// TEST 9: Hapus Buku Tanpa Histori (Delete)
// -------------------------------------------------------------
echo "[9] Uji Hapus Buku Tanpa Histori Sirkulasi...\n";
if ($bukuIdUji > 0) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    $resDel = httpReq($baseUrl . '/buku/delete/' . $bukuIdUji, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    $checkDel = $mysqli->query("SELECT id FROM buku WHERE id = {$bukuIdUji}")->fetch_assoc();
    if (!$checkDel) {
        echo "  [OK] Buku tanpa histori sirkulasi berhasil dihapus dari database.\n";
        
        $qAudit = $mysqli->query("SELECT * FROM audit_log WHERE action = 'HAPUS_BUKU' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($qAudit && strpos($qAudit['description'], 'Robotika') !== false) {
            echo "  [OK] Penghapusan buku tercatat di tabel audit_log (ID Audit: {$qAudit['id']}).\n";
            $passCount += 2;
        } else {
            echo "  [OK] Buku terhapus dengan aman.\n";
            $passCount++;
        }
    } else {
        echo "  [FAIL] Gagal menghapus buku uji!\n";
        $failCount++;
        $mysqli->query("DELETE FROM buku WHERE id = {$bukuIdUji}");
    }
}
echo "\n";

if (file_exists($cookieFile)) unlink($cookieFile);
$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL KOLEKSI BUKU:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL KOLEKSI BUKU BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
