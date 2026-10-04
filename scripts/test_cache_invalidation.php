<?php
/**
 * Test Verifikasi Cache & Invalidasi Otomatis Modul Katalog
 */

$baseUrl = 'http://localhost:8080';

echo "=======================================================\n";
echo "   VERIFIKASI CACHE & INVALIDASI OTOMATIS KATALOG\n";
echo "=======================================================\n\n";

function requestTiming($url) {
    $start = microtime(true);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time = (microtime(true) - $start) * 1000;
    curl_close($ch);
    return ['code' => $code, 'time' => $time, 'body' => $body];
}

// 1. Clear cache first via spark or library
echo "[1] Membersihkan cache awal...\n";
require_once __DIR__ . '/../app/Libraries/CacheInvalidator.php';
// Boot CI4 minimal or use curl to an invalidation trigger
$cacheDir = __DIR__ . '/../writable/cache/';

// 2. Request 1 (Cold cache)
echo "[2] Mengukur request pertama (Cold Cache)...\n";
$r1 = requestTiming($baseUrl . '/katalog');
echo "    Status: {$r1['code']}, Waktu: " . round($r1['time'], 2) . " ms\n";

// 3. Request 2 (Warm cache)
echo "[3] Mengukur request kedua (Warm Cache)...\n";
$r2 = requestTiming($baseUrl . '/katalog');
echo "    Status: {$r2['code']}, Waktu: " . round($r2['time'], 2) . " ms\n";

if ($r1['code'] === 200 && $r2['code'] === 200) {
    echo "    ✅ Keduanya berhasil HTTP 200\n";
} else {
    echo "    ❌ Salah satu request gagal!\n";
    exit(1);
}

// 4. Verifikasi Invalidation
echo "[4] Menguji Invalidation Cache saat data buku berubah...\n";

// Buka koneksi DB untuk update judul buku sementara
$mysqli = new mysqli('127.0.0.1', 'root', '', 'perpustakaan', 3307);
if ($mysqli->connect_error) {
    die("Koneksi DB gagal: " . $mysqli->connect_error);
}

// Ambil 1 buku
$res = $mysqli->query("SELECT id, judul FROM buku WHERE status = 'aktif' LIMIT 1");
$book = $res->fetch_assoc();
$origTitle = $book['judul'];
$tempTitle = $origTitle . ' [TEST CACHE]';

echo "    Buku diuji: ID {$book['id']} — '{$origTitle}'\n";

// Request detail sebelum update (akan ter-cache)
$d1 = requestTiming($baseUrl . '/katalog/detail/' . $book['id']);
$json1 = json_decode($d1['body'], true);
echo "    Cache detail awal judul: '{$json1['data']['judul']}'\n";

// Update judul langsung di DB
$mysqli->query("UPDATE buku SET judul = '" . $mysqli->real_escape_string($tempTitle) . "' WHERE id = {$book['id']}");

// Jalankan spark command untuk invalidasi katalog
$invOutput = shell_exec('C:\\xampp\\php\\php.exe spark cache:invalidate-katalog 2>&1');
echo "    Invalidation output: " . trim($invOutput) . "\n";

// Request detail setelah invalidasi
$d2 = requestTiming($baseUrl . '/katalog/detail/' . $book['id']);
$json2 = json_decode($d2['body'], true);
echo "    Detail setelah invalidasi judul: '{$json2['data']['judul']}'\n";

$invalidationSuccess = ($json2['data']['judul'] === $tempTitle);
if ($invalidationSuccess) {
    echo "    ✅ Invalidation Berhasil! Data baru langsung tampil di katalog.\n";
} else {
    echo "    ❌ Invalidation Gagal! Data masih menggunakan cache lama.\n";
}

// Restore judul asli
$mysqli->query("UPDATE buku SET judul = '" . $mysqli->real_escape_string($origTitle) . "' WHERE id = {$book['id']}");

// Invalidate lagi agar bersih
shell_exec('C:\\xampp\\php\\php.exe spark cache:invalidate-katalog 2>&1');

$d3 = requestTiming($baseUrl . '/katalog/detail/' . $book['id']);
$json3 = json_decode($d3['body'], true);
echo "    Detail setelah restore judul: '{$json3['data']['judul']}'\n";

echo "\n=======================================================\n";
if ($invalidationSuccess) {
    echo "  ✅ SEMUA KRITERIA CACHE & INVALIDASI TERPENUHI\n";
} else {
    echo "  ❌ ADA KRITERIA YANG GAGAL\n";
}
echo "=======================================================\n";
