<?php

$scripts = [
    "test_qa_auth.php",
    "test_qa_dashboard.php",
    "test_qa_kategori.php",
    "test_qa_buku.php",
    "test_qa_anggota.php",
    "test_qa_peminjaman.php",
    "test_qa_pengembalian.php",
    "test_qa_kiosk.php",
    "test_qa_laporan.php",
    "test_qa_pengaturan.php",
    "test_qa_staf.php",
    "test_qa_katalog.php",
    "test_qa_uiux.php",
];

$totalPassed = 0;
$totalFailed = 0;
$summary = [];

echo "=======================================================================\n";
echo "              SIMPUS SD - MASTER QA TEST SUITE RUNNER\n";
echo "=======================================================================\n";

foreach ($scripts as $s) {
    echo ">> Running {$s} ... ";
    $output = [];
    $ret = 0;
    exec("\"C:\\xampp\\php\\php.exe\" " . escapeshellarg(__DIR__ . '/' . $s) . " 2>&1", $output, $ret);
    
    $outText = implode("\n", $output);
    preg_match('/Total Pengujian\s*:\s*(\d+)/i', $outText, $mTotal);
    preg_match('/Passed\s*:\s*(\d+)/i', $outText, $mPass);
    preg_match('/Failed\s*:\s*(\d+)/i', $outText, $mFail);
    
    $t = $mTotal[1] ?? '?';
    $p = $mPass[1] ?? '?';
    $f = $mFail[1] ?? ($ret === 0 ? '0' : '1');
    
    if ($ret === 0) {
        echo "PASSED ({$p}/{$t} assertions)\n";
        $totalPassed++;
        $summary[] = ['script' => $s, 'status' => 'PASS', 'assertions' => "{$p}/{$t}"];
    } else {
        echo "FAILED\n";
        echo "   " . substr(strip_tags($outText), -300) . "\n";
        $totalFailed++;
        $summary[] = ['script' => $s, 'status' => 'FAIL', 'assertions' => "{$p}/{$t}"];
    }
}

echo "\n=======================================================================\n";
echo "                      MASTER QA RESULTS TABLE\n";
echo "=======================================================================\n";
printf("%-28s | %-8s | %-12s\n", "Modul / Script Test", "Status", "Assertions");
echo str_repeat("-", 55) . "\n";
foreach ($summary as $item) {
    printf("%-28s | %-8s | %-12s\n", $item['script'], $item['status'], $item['assertions']);
}
echo str_repeat("=", 55) . "\n";
echo "Total Modul QA : " . count($scripts) . "\n";
echo "Modul Lulus    : {$totalPassed}\n";
echo "Modul Gagal    : {$totalFailed}\n";
echo "Status Akhir   : " . ($totalFailed === 0 ? "SELURUH MODUL 100% LULUS (ALL TESTS GREEN)" : "ADA MODUL YANG GAGAL") . "\n";
echo "=======================================================================\n";

exit($totalFailed === 0 ? 0 : 1);
