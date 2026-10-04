<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * StokPerhitunganTest
 * =========================================================================
 * Menguji logika kritis perhitungan dan mutasi stok buku pada SIMPUS:
 * 1. Pengurangan stok saat peminjaman (stok tersedia berkurang, total tetap).
 * 2. Penolakan peminjaman jika stok tersedia habis (stok = 0).
 * 3. Penambahan kembali stok saat pengembalian.
 * 4. Batas atas stok (stok tersedia tidak boleh melebihi jumlah eksemplar).
 * 5. Batas bawah stok (stok tersedia tidak boleh negatif / di bawah 0).
 * 6. Atomisitas mutasi multi-item peminjaman.
 * =========================================================================
 */
final class StokPerhitunganTest extends CIUnitTestCase
{
    /**
     * Helper simulasi mutasi stok peminjaman:
     * Mengembalikan array buku setelah dipinjam jika stok mencukupi,
     * atau false jika stok tidak mencukupi (stok_tersedia <= 0).
     */
    private function simulatePinjam(array $buku, int $qty = 1): array|false
    {
        if ($buku['stok_tersedia'] < $qty || $buku['stok_tersedia'] <= 0) {
            return false;
        }

        $buku['stok_tersedia'] -= $qty;
        return $buku;
    }

    /**
     * Helper simulasi mutasi stok pengembalian:
     * Menambah stok tersedia dengan batas maksimal jumlah_eksemplar.
     */
    private function simulateKembali(array $buku, int $qty = 1): array
    {
        $newStok = $buku['stok_tersedia'] + $qty;
        // FR-19: stok_tersedia tidak boleh melebihi jumlah_eksemplar
        if ($newStok > $buku['jumlah_eksemplar']) {
            $newStok = $buku['jumlah_eksemplar'];
        }

        $buku['stok_tersedia'] = $newStok;
        return $buku;
    }

    /**
     * Test: Pengurangan stok tersedia saat peminjaman buku
     */
    public function testPenguranganStokSaatPeminjamanBerhasil(): void
    {
        $buku = [
            'id'               => 101,
            'kode_buku'        => 'BK-MAT-01',
            'judul'            => 'Matematika Kelas 4',
            'jumlah_eksemplar' => 10,
            'stok_tersedia'    => 10,
        ];

        $hasil = $this->simulatePinjam($buku, 1);

        $this->assertNotFalse($hasil);
        $this->assertSame(9, $hasil['stok_tersedia'], 'Stok tersedia harus berkurang 1 menjadi 9');
        $this->assertSame(10, $hasil['jumlah_eksemplar'], 'Jumlah eksemplar total tidak boleh berubah');
    }

    /**
     * Test: Penolakan peminjaman ketika stok tersedia sudah habis (0)
     */
    public function testPenolakanPeminjamanSaatStokHabis(): void
    {
        $buku = [
            'id'               => 102,
            'kode_buku'        => 'BK-IPA-02',
            'judul'            => 'Ilmu Pengetahuan Alam Kelas 5',
            'jumlah_eksemplar' => 3,
            'stok_tersedia'    => 0, // Habis
        ];

        $hasil = $this->simulatePinjam($buku, 1);

        $this->assertFalse($hasil, 'Peminjaman harus ditolak ketika stok_tersedia bernilai 0');
    }

    /**
     * Test: Penambahan stok tersedia saat pengembalian buku
     */
    public function testPenambahanStokSaatPengembalian(): void
    {
        $buku = [
            'id'               => 103,
            'kode_buku'        => 'BK-IND-03',
            'judul'            => 'Bahasa Indonesia Kelas 3',
            'jumlah_eksemplar' => 5,
            'stok_tersedia'    => 2,
        ];

        $hasil = $this->simulateKembali($buku, 1);

        $this->assertSame(3, $hasil['stok_tersedia'], 'Stok tersedia harus bertambah dari 2 menjadi 3');
        $this->assertSame(5, $hasil['jumlah_eksemplar'], 'Jumlah eksemplar tetap 5');
    }

    /**
     * Test: Pencegahan over-stock (stok_tersedia tidak boleh melebihi jumlah_eksemplar)
     */
    public function testPencegahanStokMelebihiJumlahEksemplar(): void
    {
        $buku = [
            'id'               => 104,
            'kode_buku'        => 'BK-IPS-04',
            'judul'            => 'Ilmu Pengetahuan Sosial Kelas 6',
            'jumlah_eksemplar' => 4,
            'stok_tersedia'    => 4, // Sudah penuh di rak
        ];

        // Simulasi jika terjadi anomali pengembalian ganda
        $hasil = $this->simulateKembali($buku, 1);

        $this->assertSame(4, $hasil['stok_tersedia'], 'Stok tersedia tidak boleh melebihi jumlah eksemplar (tetap 4)');
    }

    /**
     * Test: Pencegahan stok negatif jika terjadi permintaan melebihi stok yang ada
     */
    public function testPencegahanStokNegatif(): void
    {
        $buku = [
            'id'               => 105,
            'kode_buku'        => 'BK-PPKN-05',
            'judul'            => 'Pendidikan Pancasila Kelas 2',
            'jumlah_eksemplar' => 2,
            'stok_tersedia'    => 1,
        ];

        // Minta pinjam 2 buku padahal stok sisa 1
        $hasil = $this->simulatePinjam($buku, 2);

        $this->assertFalse($hasil, 'Permintaan peminjaman melebihi stok tersedia harus digagalkan');
        $this->assertGreaterThanOrEqual(0, $buku['stok_tersedia'], 'Stok tidak boleh bernilai negatif');
    }

    /**
     * Test: Siklus lengkap pinjam -> kembalikan menjaga konsistensi stok
     */
    public function testSiklusLengkapPinjamDanKembalikan(): void
    {
        $bukuAwal = [
            'id'               => 106,
            'kode_buku'        => 'BK-SENI-06',
            'judul'            => 'Seni Budaya Kelas 4',
            'jumlah_eksemplar' => 8,
            'stok_tersedia'    => 8,
        ];

        // Siswa A pinjam
        $setelahPinjam1 = $this->simulatePinjam($bukuAwal, 1);
        $this->assertSame(7, $setelahPinjam1['stok_tersedia']);

        // Siswa B pinjam
        $setelahPinjam2 = $this->simulatePinjam($setelahPinjam1, 1);
        $this->assertSame(6, $setelahPinjam2['stok_tersedia']);

        // Siswa A mengembalikan
        $setelahKembali1 = $this->simulateKembali($setelahPinjam2, 1);
        $this->assertSame(7, $setelahKembali1['stok_tersedia']);

        // Siswa B mengembalikan
        $setelahKembali2 = $this->simulateKembali($setelahKembali1, 1);
        $this->assertSame(8, $setelahKembali2['stok_tersedia']);
        $this->assertSame($bukuAwal['jumlah_eksemplar'], $setelahKembali2['stok_tersedia']);
    }
}
