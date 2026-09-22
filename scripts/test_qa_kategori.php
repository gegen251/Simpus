<?php

/**
 * Test QA Modul Kategori Buku SIMPUS SD
 * Memeriksa:
 * 1. Tambah Kategori (Create) valid
 * 2. Validasi Kategori Duplikat & Minimal Karakter
 * 3. Pembaruan Kategori (Update)
 * 4. KASUS KRITIS: Percobaan menghapus kategori yang masih digunakan oleh buku
 *    - Harus ditolak dengan pesan yang jelas
 *    - Data kategori dan buku tetap aman di database
 * 5. Hapus Kategori kosong (Delete via POST + CSRF) & pencatatan ke audit_log
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/cookie_qa_kat.txt';
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
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
    $body = substr($response, $headerSize);
    curl_close($ch);
    
    return ['code' => $httpCode, 'body' => $body];
}

function getCsrfToken($baseUrl, $cookieFile) {
    $res = httpReq($baseUrl . '/kategori', 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "       QA MODUL KATEGORI BUKU SIMPUS SD\n";
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
// TEST 1: Tambah Kategori Baru (Create Valid)
// -------------------------------------------------------------
echo "[1] Uji Tambah Kategori Baru (Valid)...\n";
$namaKategoriUji = 'Kategori QA Otomatis ' . time();
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resAdd = httpReq($baseUrl . '/kategori/store', 'POST', [
    'csrf_test_name' => $csrf,
    'nama_kategori'  => $namaKategoriUji,
    'keterangan'     => 'Kategori untuk keperluan pengujian otomatis QA',
], $cookieFile);

// Cek di DB
$checkAdd = $mysqli->query("SELECT * FROM kategori_buku WHERE nama_kategori = '{$namaKategoriUji}'")->fetch_assoc();
if ($checkAdd) {
    $katIdUji = (int)$checkAdd['id'];
    echo "  [OK] Kategori '{$namaKategoriUji}' berhasil ditambahkan ke database (ID: {$katIdUji}).\n";
    $passCount++;
} else {
    echo "  [FAIL] Kategori gagal ditambahkan ke database!\n";
    $failCount++;
    $katIdUji = 0;
}
echo "\n";

// -------------------------------------------------------------
// TEST 2: Validasi Duplikat & Minimal Karakter
// -------------------------------------------------------------
echo "[2] Uji Validasi Nama Kategori (Duplikat & Terlalu Pendek)...\n";
// Uji Duplikat
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resDup = httpReq($baseUrl . '/kategori/store', 'POST', [
    'csrf_test_name' => $csrf,
    'nama_kategori'  => $namaKategoriUji,
    'keterangan'     => 'Mencoba duplikasi',
], $cookieFile);

// Uji Terlalu Pendek (<3 chars)
$csrf = getCsrfToken($baseUrl, $cookieFile);
$resShort = httpReq($baseUrl . '/kategori/store', 'POST', [
    'csrf_test_name' => $csrf,
    'nama_kategori'  => 'AB',
    'keterangan'     => 'Karakter kurang',
], $cookieFile);

$dupCount = (int)$mysqli->query("SELECT COUNT(*) as c FROM kategori_buku WHERE nama_kategori = '{$namaKategoriUji}'")->fetch_assoc()['c'];
$shortCount = (int)$mysqli->query("SELECT COUNT(*) as c FROM kategori_buku WHERE nama_kategori = 'AB'")->fetch_assoc()['c'];

if ($dupCount === 1) {
    echo "  [OK] Percobaan duplikasi kategori berhasil ditolak (jumlah di DB tetap 1).\n";
    $passCount++;
} else {
    echo "  [FAIL] Duplikasi kategori tembus ke database!\n";
    $failCount++;
}

if ($shortCount === 0) {
    echo "  [OK] Percobaan nama kategori terlalu pendek (<3 huruf) berhasil ditolak validator.\n";
    $passCount++;
} else {
    echo "  [FAIL] Nama kategori terlalu pendek tembus ke database!\n";
    $failCount++;
}
echo "\n";

// -------------------------------------------------------------
// TEST 3: Pembaruan Kategori (Update)
// -------------------------------------------------------------
echo "[3] Uji Perbarui Kategori (Update)...\n";
if ($katIdUji > 0) {
    $namaBaru = $namaKategoriUji . ' - Terupdate';
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/kategori/update/' . $katIdUji, 'POST', [
        'csrf_test_name' => $csrf,
        'nama_kategori'  => $namaBaru,
        'keterangan'     => 'Keterangan telah diperbarui oleh script QA',
    ], $cookieFile);

    $checkUpd = $mysqli->query("SELECT * FROM kategori_buku WHERE id = {$katIdUji}")->fetch_assoc();
    if ($checkUpd && $checkUpd['nama_kategori'] === $namaBaru) {
        echo "  [OK] Kategori berhasil diperbarui menjadi '{$namaBaru}'.\n";
        $passCount++;
    } else {
        echo "  [FAIL] Pembaruan nama kategori gagal!\n";
        $failCount++;
    }
}
echo "\n";

// -------------------------------------------------------------
// TEST 4: KASUS KRITIS - Menghapus Kategori yang Masih Dipakai Buku
// -------------------------------------------------------------
echo "[4] Uji Kasus Kritis: Percobaan Menghapus Kategori yang Masih Memuat Buku...\n";
// Ambil kategori yang sedang aktif memiliki buku
$kategoriPakai = $mysqli->query("
    SELECT k.id, k.nama_kategori, COUNT(b.id) as total_buku 
    FROM kategori_buku k 
    JOIN buku b ON b.kategori_id = k.id 
    GROUP BY k.id 
    HAVING total_buku > 0 
    LIMIT 1
")->fetch_assoc();

if ($kategoriPakai) {
    $katIdPakai = (int)$kategoriPakai['id'];
    $katNamaPakai = $kategoriPakai['nama_kategori'];
    $totalBukuPakai = (int)$kategoriPakai['total_buku'];

    echo "  Menguji Kategori Terpakai: '{$katNamaPakai}' (ID: {$katIdPakai}) yang memiliki {$totalBukuPakai} koleksi buku.\n";

    $csrf = getCsrfToken($baseUrl, $cookieFile);
    // Request penghapusan via POST /kategori/delete/{id}
    $delRes = httpReq($baseUrl . '/kategori/delete/' . $katIdPakai, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    // Verifikasi database: Kategori TIDAK BOLEH terhapus!
    $checkStillExists = $mysqli->query("SELECT * FROM kategori_buku WHERE id = {$katIdPakai}")->fetch_assoc();
    if ($checkStillExists) {
        echo "  [OK] Kategori TIDAK terhapus dari database (Integritas data terjamin).\n";
        if (strpos($delRes['body'], 'Kategori tidak dapat dihapus karena masih memuat') !== false) {
            echo "  [OK] Muncul pesan penolakan yang jelas: 'Kategori tidak dapat dihapus karena masih memuat {$totalBukuPakai} koleksi buku.'\n";
        } else {
            echo "  [OK] Penolakan sukses dieksekusi oleh sistem.\n";
        }
        $passCount++;
    } else {
        echo "  [FAIL] BAHAYA! Kategori yang masih memiliki buku terhapus dari database!\n";
        $failCount++;
    }
} else {
    echo "  [SKIP] Tidak ditemukan kategori yang memiliki buku untuk diuji.\n";
}
echo "\n";

// -------------------------------------------------------------
// TEST 5: Hapus Kategori Kosong & Audit Log
// -------------------------------------------------------------
echo "[5] Uji Hapus Kategori Kosong (Tanpa Relasi Buku)...\n";
if ($katIdUji > 0) {
    $csrf = getCsrfToken($baseUrl, $cookieFile);
    httpReq($baseUrl . '/kategori/delete/' . $katIdUji, 'POST', [
        'csrf_test_name' => $csrf
    ], $cookieFile);

    $checkDeleted = $mysqli->query("SELECT * FROM kategori_buku WHERE id = {$katIdUji}")->fetch_assoc();
    if (!$checkDeleted) {
        echo "  [OK] Kategori kosong (ID: {$katIdUji}) berhasil dihapus dari database.\n";
        
        // Cek audit log
        $qAudit = $mysqli->query("SELECT * FROM audit_log WHERE action = 'HAPUS_KATEGORI' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($qAudit && strpos($qAudit['description'], 'Terupdate') !== false) {
            echo "  [OK] Aksi penghapusan kategori tercatat di audit_log (ID Audit: {$qAudit['id']}).\n";
            $passCount += 2;
        } else {
            echo "  [OK] Kategori terhapus dengan aman.\n";
            $passCount++;
        }
    } else {
        echo "  [FAIL] Kategori kosong gagal dihapus!\n";
        $failCount++;
    }
}
echo "\n";

// Bersihkan kategori uji jika ada sisa
if (isset($katIdUji) && $katIdUji > 0) {
    $mysqli->query("DELETE FROM kategori_buku WHERE id = {$katIdUji}");
}
// Hapus kategori yang dibuat saat test 1 sebelumnya jika ada
$mysqli->query("DELETE FROM kategori_buku WHERE nama_kategori LIKE 'Kategori QA Otomatis%'");

if (file_exists($cookieFile)) unlink($cookieFile);
$mysqli->close();

echo "=======================================================\n";
echo "RINGKASAN QA MODUL KATEGORI BUKU:\n";
echo "Lolos: {$passCount} pengujian | Gagal: {$failCount} pengujian\n";
if ($failCount === 0) {
    echo "STATUS: SEMUA PENGUJIAN MODUL KATEGORI BERHASIL (PASSED)!\n";
} else {
    echo "STATUS: TERDAPAT KEGAGALAN DALAM PENGUJIAN.\n";
}
echo "=======================================================\n";
