<?php

/**
 * Test QA Modul Katalog Publik (OPAC) SIMPUS SD
 * Memeriksa:
 * 1. Akses Publik Terbuka Tanpa Login (HTTP 200 pada /katalog dan /katalog/detail/{id})
 * 2. Redirect Rute Root (/) ke /katalog saat unauthenticated
 * 3. Fitur Pencarian Kata Kunci (Judul, Pengarang, ISBN)
 * 4. Filter Kategori Koleksi dan Ketersediaan Stok
 * 5. Penanganan Buku Tidak Ditemukan (404 JSON bersih)
 * 6. Audit Kerahasiaan Data (Anti-PII Leakage):
 *    Memastikan nol (0) data pribadi siswa/anggota (NISN, nomor anggota, nama siswa, nomor HP)
 *    yang bocor pada tampilan publik maupun respon JSON detail buku.
 */

$baseUrl = 'http://localhost:8080';

$mysqli = new mysqli('127.0.0.1', 'root', '', 'perpustakaan', 3307);
if ($mysqli->connect_error) {
    die("Koneksi DB Gagal: " . $mysqli->connect_error);
}

function httpReqNoAuth($url, $method = 'GET', $data = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Cek redirect asli
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
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
echo "      QA MODUL KATALOG PUBLIK OPAC & PRIVASI ANGGOTA\n";
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

// 1. Uji Rute Root (/)
echo "[1] Uji Rute Root (/) Tanpa Login...\n";
$resRoot = httpReqNoAuth($baseUrl . '/');
$isRedirectToKatalog = ($resRoot['code'] === 302 || $resRoot['code'] === 301) && stripos($resRoot['headers'], 'location: ' . $baseUrl . '/katalog') !== false;
assertTest("Root URL (/) Mengarahkan Tamu ke /katalog Tanpa Login", $isRedirectToKatalog, "Code: {$resRoot['code']}");

// 2. Uji Akses Publik Halaman Katalog OPAC
echo "\n[2] Uji Akses Halaman Utama OPAC (/katalog)...\n";
$resKatalog = httpReqNoAuth($baseUrl . '/katalog');
assertTest("Halaman /katalog Dapat Diakses Publik Tanpa Login (HTTP 200)", $resKatalog['code'] === 200);
assertTest("Halaman OPAC Memuat Judul Katalog Resmi SIMPUS", strpos($resKatalog['body'], 'Katalog Perpustakaan Terbuka') !== false || strpos($resKatalog['body'], 'OPAC') !== false);
assertTest("Halaman OPAC Menyediakan Input Form Pencarian", strpos($resKatalog['body'], 'name="q"') !== false);

// 3. Uji Pencarian dan Filter Koleksi Buku
echo "\n[3] Uji Pencarian & Filter Katalog...\n";
// Ambil satu buku contoh dari DB
$sampleBook = $mysqli->query("SELECT * FROM buku WHERE status = 'aktif' LIMIT 1")->fetch_assoc();
$sampleJudul = $sampleBook['judul'] ?? 'Matematika';
$sampleKata  = explode(' ', $sampleJudul)[0];
$sampleKatId = (int)($sampleBook['kategori_id'] ?? 1);

// A. Cari berdasarkan kata kunci judul
$resSearch = httpReqNoAuth($baseUrl . '/katalog?q=' . urlencode($sampleKata));
assertTest(
    "Pencarian Kata Kunci '{$sampleKata}' Menampilkan Hasil Relevan",
    $resSearch['code'] === 200 && stripos($resSearch['body'], htmlspecialchars($sampleKata)) !== false
);

// B. Cari kata kunci tidak ada
$resEmpty = httpReqNoAuth($baseUrl . '/katalog?q=XYZBukuFiktif999TidakAda');
assertTest(
    "Pencarian Tanpa Hasil Menampilkan Pesan Bersahabat Tanpa Error",
    $resEmpty['code'] === 200 && (stripos($resEmpty['body'], 'Belum Ditemukan') !== false || stripos($resEmpty['body'], 'tidak ditemukan') !== false)
);

// C. Filter Kategori
$resKat = httpReqNoAuth($baseUrl . "/katalog?kategori={$sampleKatId}");
assertTest("Filter Kategori Koleksi Berhasil Merespon HTTP 200", $resKat['code'] === 200);

// D. Filter Ketersediaan Stok
$resAvail = httpReqNoAuth($baseUrl . '/katalog?ketersediaan=tersedia');
assertTest("Filter Ketersediaan 'tersedia' Berhasil Merespon HTTP 200", $resAvail['code'] === 200);

// 4. Uji Endpoint Detail Buku (/katalog/detail/{id})
echo "\n[4] Uji Endpoint Detail Buku Tanpa Login...\n";
$bukuId = (int)$sampleBook['id'];
$resDetail = httpReqNoAuth($baseUrl . "/katalog/detail/{$bukuId}");
$jsonDetail = json_decode($resDetail['body'], true);

assertTest("Endpoint /katalog/detail/{id} Merespon JSON 200 Tanpa Login", $resDetail['code'] === 200 && ($jsonDetail['status'] ?? '') === 'success');
assertTest("Detail Buku Memuat Judul yang Benar", ($jsonDetail['data']['judul'] ?? '') === $sampleBook['judul']);
assertTest("Detail Buku Memuat Informasi Rak & Penerbit", isset($jsonDetail['data']['lokasi_rak']) && isset($jsonDetail['data']['penerbit']));

// Uji ID tidak ditemukan (404)
$resDetail404 = httpReqNoAuth($baseUrl . "/katalog/detail/9999999");
$json404 = json_decode($resDetail404['body'], true);
assertTest("Detail Buku ID Fiktif Merespon Status 404 Bersih", $resDetail404['code'] === 404 && ($json404['status'] ?? '') === 'error');

// 5. Uji Kerahasiaan Data Pribadi Anggota (Anti-PII Leakage)
echo "\n[5] Uji Audit Kerahasiaan Data Pribadi Anggota (Anti-PII Leakage)...\n";

// Pastikan ada transaksi peminjaman aktif di DB untuk buku ini
$activeLoan = $mysqli->query("SELECT p.*, a.nama as anggota_nama, a.nomor_anggota, a.no_identitas, a.kontak 
                              FROM peminjaman p 
                              JOIN anggota a ON a.id = p.anggota_id 
                              WHERE p.status = 'dipinjam' LIMIT 1")->fetch_assoc();

if (!$activeLoan) {
    // Buat pinjaman sementara untuk menguji
    $dummyAnggota = $mysqli->query("SELECT * FROM anggota LIMIT 1")->fetch_assoc();
    $mysqli->query("INSERT INTO peminjaman (kode_transaksi, anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status, admin_id)
                    VALUES ('PJ-TEST-PII', {$dummyAnggota['id']}, {$bukuId}, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'dipinjam', 1)");
    $loanIdTemp = $mysqli->insert_id;
    $activeLoan = [
        'anggota_nama'   => $dummyAnggota['nama'],
        'nomor_anggota'  => $dummyAnggota['nomor_anggota'],
        'no_identitas'   => $dummyAnggota['no_identitas'],
        'kontak'         => $dummyAnggota['kontak'],
    ];
}

echo "    Memeriksa kebocoran identitas anggota peminjam:\n";
echo "    - Nama Siswa/Anggota : {$activeLoan['anggota_nama']}\n";
echo "    - Nomor Anggota      : {$activeLoan['nomor_anggota']}\n";
echo "    - No Identitas (NISN): {$activeLoan['no_identitas']}\n";
echo "    - Kontak Anggota     : {$activeLoan['kontak']}\n";

// Cek di halaman /katalog
$katalogHtml = $resKatalog['body'];
$leakInHtml = false;
$leakFieldHtml = '';

if (!empty($activeLoan['nomor_anggota']) && strpos($katalogHtml, $activeLoan['nomor_anggota']) !== false) {
    $leakInHtml = true;
    $leakFieldHtml = 'nomor_anggota';
} elseif (!empty($activeLoan['no_identitas']) && strpos($katalogHtml, $activeLoan['no_identitas']) !== false) {
    $leakInHtml = true;
    $leakFieldHtml = 'no_identitas';
} elseif (!empty($activeLoan['kontak']) && strpos($katalogHtml, $activeLoan['kontak']) !== false) {
    $leakInHtml = true;
    $leakFieldHtml = 'kontak';
} elseif (!empty($activeLoan['anggota_nama']) && strpos($katalogHtml, $activeLoan['anggota_nama']) !== false) {
    $leakInHtml = true;
    $leakFieldHtml = 'anggota_nama';
}

assertTest("Halaman Katalog OPAC Bebas Kebocoran Data Anggota (0 PII Leaked)", !$leakInHtml, "Bocor pada field: {$leakFieldHtml}");

// Cek di JSON Detail Buku
$detailJsonStr = $resDetail['body'];
$leakInJson = false;
$leakFieldJson = '';

if (!empty($activeLoan['nomor_anggota']) && strpos($detailJsonStr, $activeLoan['nomor_anggota']) !== false) {
    $leakInJson = true;
    $leakFieldJson = 'nomor_anggota';
} elseif (!empty($activeLoan['no_identitas']) && strpos($detailJsonStr, $activeLoan['no_identitas']) !== false) {
    $leakInJson = true;
    $leakFieldJson = 'no_identitas';
} elseif (!empty($activeLoan['kontak']) && strpos($detailJsonStr, $activeLoan['kontak']) !== false) {
    $leakInJson = true;
    $leakFieldJson = 'kontak';
} elseif (!empty($activeLoan['anggota_nama']) && strpos($detailJsonStr, $activeLoan['anggota_nama']) !== false) {
    $leakInJson = true;
    $leakFieldJson = 'anggota_nama';
}

assertTest("Endpoint Detail Buku Bebas Kebocoran Data Anggota (0 PII Leaked)", !$leakInJson, "Bocor pada field: {$leakFieldJson}");

// Bersihkan transaksi temp jika ada
if (isset($loanIdTemp)) {
    $mysqli->query("DELETE FROM peminjaman WHERE id = {$loanIdTemp}");
}

echo "\n=======================================================\n";
echo "            RINGKASAN HASIL QA KATALOG OPAC\n";
echo "=======================================================\n";
echo "Total Pengujian : " . ($passCount + $failCount) . "\n";
echo "Passed          : {$passCount}\n";
echo "Failed          : {$failCount}\n";
echo "Status Akhir    : " . ($failCount === 0 ? "SEMUA PENGUJIAN LULUS (SUCCESS)" : "ADA PENGUJIAN GAGAL") . "\n";
echo "=======================================================\n";

exit($failCount === 0 ? 0 : 1);
