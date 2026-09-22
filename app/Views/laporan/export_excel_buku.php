<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>
    <style>
        .kop-title { font-size: 14pt; font-weight: bold; text-align: center; }
        .kop-sub { font-size: 10pt; color: #555555; text-align: center; }
        .doc-title { font-size: 12pt; font-weight: bold; text-align: center; }
        .table-header { background-color: #1E293B; color: #FFFFFF; font-weight: bold; text-align: center; border: 0.5pt solid #000000; }
        .table-cell { border: 0.5pt solid #CBD5E1; padding: 4px; font-size: 9.5pt; }
        .table-cell-center { border: 0.5pt solid #CBD5E1; text-align: center; padding: 4px; font-size: 9.5pt; }
        .table-cell-right { border: 0.5pt solid #CBD5E1; text-align: right; padding: 4px; font-size: 9.5pt; mso-number-format:"\#\,\#\#0"; }
        .total-row { background-color: #F1F5F9; font-weight: bold; border: 0.5pt solid #000000; }
    </style>
</head>
<body>
    <table>
        <tr><td colspan="9" class="kop-title"><?= esc($config['nama_perpustakaan'] ?? 'PERPUSTAKAAN SEKOLAH DASAR') ?></td></tr>
        <tr><td colspan="9" class="kop-sub"><?= esc($config['alamat_perpustakaan'] ?? 'Sistem Informasi Manajemen Perpustakaan Terpadu') ?></td></tr>
        <tr><td colspan="9"></td></tr>
        <tr><td colspan="9" class="doc-title">LAPORAN DATA INVENTARIS BUKU</td></tr>
        <tr><td colspan="9" style="text-align: center; font-size: 9.5pt;">Dicetak tanggal: <?= $today ?></td></tr>
        <tr><td colspan="9"></td></tr>
        <thead>
            <tr>
                <th class="table-header">No</th>
                <th class="table-header">Kode Buku</th>
                <th class="table-header">Judul Buku</th>
                <th class="table-header">Kategori</th>
                <th class="table-header">Penulis</th>
                <th class="table-header">Penerbit</th>
                <th class="table-header">Tahun</th>
                <th class="table-header">Rak</th>
                <th class="table-header">Jumlah Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr><td colspan="9" class="table-cell-center">Tidak ada koleksi buku pada kategori ini.</td></tr>
            <?php else: ?>
                <?php $no = 1; $totalEksemplar = 0; foreach ($dataLaporan as $r): $totalEksemplar += (int)$r['jumlah_eksemplar']; ?>
                    <tr>
                        <td class="table-cell-center"><?= $no++ ?></td>
                        <td class="table-cell-center" style="mso-number-format:'\@';"><?= esc($r['kode_buku']) ?></td>
                        <td class="table-cell"><?= esc($r['judul']) ?></td>
                        <td class="table-cell"><?= esc($r['nama_kategori']) ?></td>
                        <td class="table-cell"><?= esc($r['penulis'] ?: '-') ?></td>
                        <td class="table-cell"><?= esc($r['penerbit'] ?: '-') ?></td>
                        <td class="table-cell-center"><?= esc($r['tahun_terbit'] ?: '-') ?></td>
                        <td class="table-cell-center"><?= esc($r['lokasi_rak'] ?: '-') ?></td>
                        <td class="table-cell-right"><?= (int)$r['jumlah_eksemplar'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="8" style="text-align: right; font-weight: bold; border: 0.5pt solid #000000;">TOTAL EKSEMPLAR:</td>
                    <td class="table-cell-right" style="font-weight: bold; border: 0.5pt solid #000000;"><?= $totalEksemplar ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
