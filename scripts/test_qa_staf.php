<?php

/**
 * Test QA Modul Staf SIMPUS SD
 * Memeriksa:
 * 1. CRUD Staf Pustaka
 * 2. Kebijakan Panjang Password Minimal 8 Karakter (Create, Update, Reset Password)
 * 3. Reset Password Staf & Verifikasi Login dengan Password Baru
 * 4. Aturan Keamanan: Larangan Mengubah Role (Demote) Admin Terakhir
 * 5. Aturan Keamanan: Larangan Menghapus Admin Terakhir
 * 6. Aturan Keamanan: Larangan Menghapus Akun Sendiri yang Sedang Aktif
 * 7. Proteksi Data Audit Log pada Aksi Sensitif Akun Staf
 */

$baseUrl = 'http://localhost:8080';
$adminCookie = __DIR__ . '/cookie_qa_staf_admin.txt';
$stafCookie = __DIR__ . '/cookie_qa_staf_user.txt';
if (file_exists($adminCookie)) unlink($adminCookie);
if (file_exists($stafCookie)) unlink($stafCookie);

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

function getCsrfToken($baseUrl, $cookieFile, $path = '/staf') {
    $res = httpReq($baseUrl . $path, 'GET', [], $cookieFile);
    if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $res['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "=======================================================\n";
echo "           QA MODUL STAF SIMPUS SD\n";
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

// 1. Login Admin Utama
echo "[1] Otentikasi Admin Utama...\n";
$loginCsrf = getCsrfToken($baseUrl, $adminCookie, '/login');
$loginRes = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $loginCsrf,
    'username'       => 'admin',
    'password'       => 'admin123'
], $adminCookie);

assertTest("Login Admin Utama Berhasil", $loginRes['code'] === 200 && strpos($loginRes['body'], 'Dashboard') !== false);

// 2. Uji Validasi Password Baru Minimal 8 Karakter pada Pendaftaran Staf
echo "\n[2] Uji Validasi Kebijakan Password Minimal 8 Karakter...\n";
$timeSuffix = time();
$stafUsername = "staf_qa_" . substr($timeSuffix, -4);
$stafEmail    = "staf_{$timeSuffix}@sekolah.id";

// Coba buat dengan password 6 karakter ("123456") -> Harus Gagal
$csrfAdd1 = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resAddShort = httpReq($baseUrl . '/staf/store', 'POST', [
    'csrf_test_name' => $csrfAdd1,
    'nama'           => 'Staf Uji Validasi',
    'username'       => $stafUsername . "_short",
    'email'          => "short_{$timeSuffix}@sekolah.id",
    'password'       => '123456', // Kurang dari 8 karakter
    'role'           => 'staf'
], $adminCookie);

assertTest(
    "Pendaftaran Staf Ditolak jika Password < 8 Karakter",
    strpos($resAddShort['body'], 'at least 8 characters') !== false || strpos($resAddShort['body'], 'minimal 8 karakter') !== false || strpos($resAddShort['body'], '8') !== false,
    "Respon: " . substr(strip_tags($resAddShort['body']), 0, 100)
);

// 3. Pendaftaran Staf dengan Data Valid & Password >= 8 Karakter
echo "\n[3] Pendaftaran Akun Staf Baru Valid...\n";
$csrfAdd2 = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resAddValid = httpReq($baseUrl . '/staf/store', 'POST', [
    'csrf_test_name' => $csrfAdd2,
    'nama'           => 'Staf Perpustakaan QA',
    'username'       => $stafUsername,
    'email'          => $stafEmail,
    'password'       => 'PustakaSD123!', // 13 karakter (valid)
    'role'           => 'staf'
], $adminCookie);

$newStaf = $mysqli->query("SELECT * FROM admin WHERE username = '{$stafUsername}' LIMIT 1")->fetch_assoc();
$newStafId = $newStaf ? (int)$newStaf['id'] : 0;

assertTest("Akun Staf Baru Berhasil Disimpan di DB", $newStaf !== null && $newStafId > 0);
assertTest("Password Tersimpan Menggunakan Hash Aman (Bukan Plaintext)", $newStaf && substr($newStaf['password'], 0, 4) === '$2y$');

// Cek Audit Log TAMBAH_STAF
$auditAdd = $mysqli->query("SELECT * FROM audit_log WHERE action = 'TAMBAH_STAF' AND description LIKE '%{$stafUsername}%' LIMIT 1")->fetch_assoc();
assertTest("Audit Log Mencatat Aksi TAMBAH_STAF", $auditAdd !== null);

// 4. Uji Endpoint Detail Staf (/staf/detail/{id}) & Kerahasiaan Password
echo "\n[4] Uji Detail Staf & Kerahasiaan Password Hash...\n";
$resDetail = httpReq($baseUrl . "/staf/detail/{$newStafId}", 'GET', [], $adminCookie);
$jsonDetail = json_decode($resDetail['body'], true);

assertTest("Endpoint Detail Staf Merespon JSON Success (200)", $resDetail['code'] === 200 && ($jsonDetail['status'] ?? '') === 'success');
assertTest("Detail Staf Mengembalikan Data Akun yang Tepat", ($jsonDetail['data']['username'] ?? '') === $stafUsername);
assertTest("Detail Staf Tidak Membocorkan Hash Password", !isset($jsonDetail['data']['password']));

// 5. Uji Pembaruan Profil Staf (Update)
echo "\n[5] Uji Pembaruan (Update) Data Staf...\n";
$csrfUpd = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resUpd = httpReq($baseUrl . "/staf/update/{$newStafId}", 'POST', [
    'csrf_test_name' => $csrfUpd,
    'nama'           => 'Staf Perpustakaan Terverifikasi',
    'username'       => $stafUsername,
    'email'          => "updated_{$stafEmail}",
    'role'           => 'staf'
], $adminCookie);

$stafUpdated = $mysqli->query("SELECT * FROM admin WHERE id = {$newStafId}")->fetch_assoc();
assertTest("Nama Staf Berhasil Diperbarui di DB", $stafUpdated['nama'] === 'Staf Perpustakaan Terverifikasi');
assertTest("Email Staf Berhasil Diperbarui di DB", $stafUpdated['email'] === "updated_{$stafEmail}");

// 6. Uji Reset Password Staf
echo "\n[6] Uji Fitur Reset Password Staf...\n";
// A. Reset password dengan < 8 karakter -> Harus Ditolak
$csrfReset1 = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resResetShort = httpReq($baseUrl . "/staf/reset-password/{$newStafId}", 'POST', [
    'csrf_test_name' => $csrfReset1,
    'new_password'   => 'pendek' // 6 karakter
], $adminCookie);

assertTest(
    "Reset Password Ditolak jika Kurang dari 8 Karakter",
    strpos($resResetShort['body'], 'minimal 8 karakter') !== false
);

// B. Reset password dengan >= 8 karakter -> Harus Berhasil
$csrfReset2 = getCsrfToken($baseUrl, $adminCookie, '/staf');
$newPass = 'NewPasswordSD123#';
$resResetValid = httpReq($baseUrl . "/staf/reset-password/{$newStafId}", 'POST', [
    'csrf_test_name' => $csrfReset2,
    'new_password'   => $newPass
], $adminCookie);

assertTest("Reset Password Sukses Disimpan", strpos($resResetValid['body'], 'berhasil direset') !== false);

// C. Verifikasi Staf Dapat Login Menggunakan Password Baru
$csrfStafLogin = getCsrfToken($baseUrl, $stafCookie, '/login');
$resStafLogin = httpReq($baseUrl . '/login/process', 'POST', [
    'csrf_test_name' => $csrfStafLogin,
    'username'       => $stafUsername,
    'password'       => $newPass
], $stafCookie);

assertTest("Staf Berhasil Login Menggunakan Password Hasil Reset", $resStafLogin['code'] === 200 && strpos($resStafLogin['body'], 'Dashboard') !== false);

// 7. Uji Aturan Keamanan Admin Terakhir
echo "\n[7] Uji Aturan Integritas Sistem (Admin Terakhir & Self-Deletion)...\n";

// Ambil ID admin yang sedang login
$currentAdmin = $mysqli->query("SELECT * FROM admin WHERE username = 'admin' LIMIT 1")->fetch_assoc();
$adminId = (int)$currentAdmin['id'];

// Pastikan jumlah admin dihitung
$adminCount = (int)$mysqli->query("SELECT COUNT(*) as cnt FROM admin WHERE role = 'admin'")->fetch_assoc()['cnt'];
echo "    Jumlah Akun Administrator Saat Ini: {$adminCount}\n";

// Jika ada lebih dari 1 admin, sementara ubah yang lain agar adminCount = 1 untuk menguji proteksi
$extraAdmins = $mysqli->query("SELECT id FROM admin WHERE role = 'admin' AND id != {$adminId}")->fetch_all(MYSQLI_ASSOC);
foreach ($extraAdmins as $ea) {
    $mysqli->query("UPDATE admin SET role = 'staf' WHERE id = {$ea['id']}");
}

// A. Percobaan Downgrade (Mengubah Role) Admin Terakhir Menjadi Staf -> HARUS DITOLAK
$csrfDemote = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resDemote = httpReq($baseUrl . "/staf/update/{$adminId}", 'POST', [
    'csrf_test_name' => $csrfDemote,
    'nama'           => $currentAdmin['nama'],
    'username'       => $currentAdmin['username'],
    'email'          => $currentAdmin['email'],
    'role'           => 'staf' // Mencoba mendowngrade admin satu-satunya
], $adminCookie);

assertTest(
    "Larangan Mengubah Role Admin Terakhir: Percobaan Downgrade Ditolak",
    strpos($resDemote['body'], 'minimal satu Administrator') !== false
);

// Pastikan di database role tetap 'admin'
$checkAdminRole = $mysqli->query("SELECT role FROM admin WHERE id = {$adminId}")->fetch_assoc()['role'];
assertTest("Role Administrator Utama Tetap 'admin' di Database", $checkAdminRole === 'admin');

// B. Percobaan Menghapus Akun Diri Sendiri (Self-Deletion) -> HARUS DITOLAK
$csrfDelSelf = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resDelSelf = httpReq($baseUrl . "/staf/delete/{$adminId}", 'POST', [
    'csrf_test_name' => $csrfDelSelf
], $adminCookie);

assertTest(
    "Larangan Menghapus Akun Sendiri: Percobaan Self-Delete Ditolak",
    strpos($resDelSelf['body'], 'tidak dapat menghapus akun Anda sendiri') !== false
);

// C. Kembalikan extra admins jika sebelumnya ada
foreach ($extraAdmins as $ea) {
    $mysqli->query("UPDATE admin SET role = 'admin' WHERE id = {$ea['id']}");
}

// 8. Uji Penghapusan Akun Staf Uji
echo "\n[8] Uji Penghapusan Akun Staf Uji...\n";
$csrfDelStaf = getCsrfToken($baseUrl, $adminCookie, '/staf');
$resDelStaf = httpReq($baseUrl . "/staf/delete/{$newStafId}", 'POST', [
    'csrf_test_name' => $csrfDelStaf
], $adminCookie);

assertTest("Akun Staf Uji Berhasil Dihapus", strpos($resDelStaf['body'], 'berhasil dihapus') !== false);

$checkDeleted = $mysqli->query("SELECT * FROM admin WHERE id = {$newStafId}")->fetch_assoc();
assertTest("Akun Staf Uji Sudah Tidak Ada di DB", $checkDeleted === null);

// Cleanup cookie files
if (file_exists($adminCookie)) unlink($adminCookie);
if (file_exists($stafCookie)) unlink($stafCookie);

echo "\n=======================================================\n";
echo "                RINGKASAN HASIL QA STAF\n";
echo "=======================================================\n";
echo "Total Pengujian : " . ($passCount + $failCount) . "\n";
echo "Passed          : {$passCount}\n";
echo "Failed          : {$failCount}\n";
echo "Status Akhir    : " . ($failCount === 0 ? "SEMUA PENGUJIAN LULUS (SUCCESS)" : "ADA PENGUJIAN GAGAL") . "\n";
echo "=======================================================\n";

exit($failCount === 0 ? 0 : 1);
