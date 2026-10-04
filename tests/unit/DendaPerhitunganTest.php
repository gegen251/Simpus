<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * DendaPerhitunganTest
 * =========================================================================
 * Menguji logika kalkulasi denda keterlambatan pengembalian buku:
 * 1. Pengembalian tepat waktu (denda = Rp 0).
 * 2. Pengembalian sebelum jatuh tempo (denda = Rp 0).
 * 3. Keterlambatan N hari dengan tarif standar (Rp 500 / hari).
 * 4. Keterlambatan dengan tarif kustom (misal Rp 1.000 / hari).
 * 5. Pengecualian hari libur akhir pekan (Sabtu & Minggu) jika disetel aktif.
 * 6. Penanganan status pembayaran denda ('lunas', 'belum_lunas', 'tidak_ada').
 * 7. Integritas nilai denda (tidak boleh bernilai negatif).
 * =========================================================================
 */
final class DendaPerhitunganTest extends CIUnitTestCase
{
    /**
     * Engine kalkulasi denda SIMPUS:
     * Meniru logika di Pengembalian.php & Kiosk.php
     */
    private function hitungDenda(
        string $tglJatuhTempo,
        string $tglKembali,
        float $tarifPerHari = 500.0,
        bool $hitungWeekend = true,
        string $inputStatusDenda = 'belum_lunas'
    ): array {
        $tsTempo = strtotime($tglJatuhTempo);
        $tsKembali = strtotime($tglKembali);

        if ($tsKembali <= $tsTempo) {
            return [
                'hari_terlambat' => 0,
                'denda'          => 0.0,
                'status_denda'   => 'tidak_ada',
            ];
        }

        $hariTerlambat = 0;

        if ($hitungWeekend) {
            // Perhitungan kalender penuh
            $diffSeconds = $tsKembali - $tsTempo;
            $hariTerlambat = (int)ceil($diffSeconds / 86400);
        } else {
            // Hitung hanya hari kerja (Senin - Jumat)
            $curr = $tsTempo + 86400;
            while ($curr <= $tsKembali) {
                $dayOfWeek = (int)date('N', $curr); // 1 = Senin, 7 = Minggu
                if ($dayOfWeek <= 5) {
                    $hariTerlambat++;
                }
                $curr += 86400;
            }
        }

        $denda = max(0.0, (float)($hariTerlambat * $tarifPerHari));
        $status = in_array($inputStatusDenda, ['lunas', 'belum_lunas'], true) ? $inputStatusDenda : 'belum_lunas';

        return [
            'hari_terlambat' => $hariTerlambat,
            'denda'          => $denda,
            'status_denda'   => $status,
        ];
    }

    /**
     * Test: Pengembalian tepat pada tanggal jatuh tempo
     */
    public function testPengembalianTepatWaktuTanpaDenda(): void
    {
        $tempo   = '2026-09-20';
        $kembali = '2026-09-20';

        $res = $this->hitungDenda($tempo, $kembali);

        $this->assertSame(0, $res['hari_terlambat']);
        $this->assertEqualsWithDelta(0.0, $res['denda'], 0.001);
        $this->assertSame('tidak_ada', $res['status_denda']);
    }

    /**
     * Test: Pengembalian lebih awal dari tanggal jatuh tempo
     */
    public function testPengembalianLebihAwalTanpaDenda(): void
    {
        $tempo   = '2026-09-25';
        $kembali = '2026-09-20'; // 5 hari lebih awal

        $res = $this->hitungDenda($tempo, $kembali);

        $this->assertSame(0, $res['hari_terlambat']);
        $this->assertEqualsWithDelta(0.0, $res['denda'], 0.001);
        $this->assertSame('tidak_ada', $res['status_denda']);
    }

    /**
     * Test: Terlambat 3 hari dengan tarif standar Rp 500/hari
     */
    public function testTerlambatTigaHariTarifStandar(): void
    {
        $tempo   = '2026-09-10';
        $kembali = '2026-09-13'; // Terlambat 3 hari

        $res = $this->hitungDenda($tempo, $kembali, 500.0);

        $this->assertSame(3, $res['hari_terlambat']);
        $this->assertEqualsWithDelta(1500.0, $res['denda'], 0.001, 'Denda harus Rp 1.500 (3 hari * 500)');
        $this->assertSame('belum_lunas', $res['status_denda']);
    }

    /**
     * Test: Terlambat dengan tarif kustom Rp 1.000/hari
     */
    public function testTerlambatDenganTarifKustom(): void
    {
        $tempo   = '2026-09-01';
        $kembali = '2026-09-06'; // Terlambat 5 hari

        $res = $this->hitungDenda($tempo, $kembali, 1000.0, true, 'lunas');

        $this->assertSame(5, $res['hari_terlambat']);
        $this->assertEqualsWithDelta(5000.0, $res['denda'], 0.001, 'Denda harus Rp 5.000 (5 hari * 1000)');
        $this->assertSame('lunas', $res['status_denda']);
    }

    /**
     * Test: Pengecualian hari libur akhir pekan (Sabtu & Minggu tidak didenda)
     * Contoh: Jatuh tempo Jumat (2026-09-18), kembali Senin (2026-09-21)
     * Secara kalender: 3 hari (Sabtu, Minggu, Senin).
     * Jika tanpa weekend: hanya Senin (1 hari kerja).
     */
    public function testKalkulasiPengecualianAkhirPekan(): void
    {
        $tempoJumat   = '2026-09-18';
        $kembaliSenin = '2026-09-21';

        // 1. Dengan menghitung weekend: 3 hari
        $resDenganWeekend = $this->hitungDenda($tempoJumat, $kembaliSenin, 500.0, true);
        $this->assertSame(3, $resDenganWeekend['hari_terlambat']);
        $this->assertEqualsWithDelta(1500.0, $resDenganWeekend['denda'], 0.001);

        // 2. Tanpa menghitung weekend (hari kerja saja): 1 hari
        $resTanpaWeekend = $this->hitungDenda($tempoJumat, $kembaliSenin, 500.0, false);
        $this->assertSame(1, $resTanpaWeekend['hari_terlambat'], 'Hanya hari kerja (Senin) yang dihitung');
        $this->assertEqualsWithDelta(500.0, $resTanpaWeekend['denda'], 0.001);
    }

    /**
     * Test: Penanganan validasi input status denda (fallback aman)
     */
    public function testValidasiDanFallbackStatusDenda(): void
    {
        $tempo   = '2026-09-01';
        $kembali = '2026-09-02';

        // Status valid 'lunas'
        $resLunas = $this->hitungDenda($tempo, $kembali, 500.0, true, 'lunas');
        $this->assertSame('lunas', $resLunas['status_denda']);

        // Status valid 'belum_lunas'
        $resBelum = $this->hitungDenda($tempo, $kembali, 500.0, true, 'belum_lunas');
        $this->assertSame('belum_lunas', $resBelum['status_denda']);

        // Input liar / tidak valid -> harus fallback aman ke 'belum_lunas'
        $resFallback = $this->hitungDenda($tempo, $kembali, 500.0, true, 'invalid_status_xyz');
        $this->assertSame('belum_lunas', $resFallback['status_denda']);
    }

    /**
     * Test: Denda tidak boleh menghasilkan angka negatif
     */
    public function testDendaTidakPernahNegatif(): void
    {
        $res = $this->hitungDenda('2026-09-20', '2026-09-10'); // kembali 10 hari sebelum tempo
        $this->assertGreaterThanOrEqual(0.0, $res['denda']);
        $this->assertSame(0, $res['hari_terlambat']);
    }
}
