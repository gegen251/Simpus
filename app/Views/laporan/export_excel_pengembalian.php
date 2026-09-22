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
        <tr><td colspan="10" class="kop-title"><?= esc($config['nama_perpustakaan'] ?? 'PERPUSTAKAAN SEKOLAH DASAR') ?></td></tr>
        <tr><td colspan="10" class="kop-sub"><?= esc($config['alamat_perpustakaan'] ?? 'Sistem Informasi Manajemen Perpustakaan Terpadu') ?></td></tr>
        <tr><td colspan="10"></td></tr>
        <tr><td colspan="10" class="doc-title">LAPORAN PENGEMBALIAN BUKU & DENDA</td></tr>
        <tr><td colspan="10" style="text-align: center; font-size: 9.5pt;">Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></td></tr>
        <tr><td colspan="10"></td></tr>
        <thead>
            <tr>
                <th class="table-header">No</th>
                <th class="table-header">Kode Pinjam</th>
                <th class="table-header">Nama Anggota</th>
                <th class="table-header">Judul Buku</th>
                <th class="table-header">Tgl Pinjam</th>
                <th class="table-header">Jatuh Tempo</th>
                <th class="table-header">Tgl Kembali</th>
                <th class="table-header">Terlambat (Hari)</th>
                <th class="table-header">Denda (Rp)</th>
                <th class="table-header">Status Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr><td colspan="10" class="table-cell-center">Tidak ada rekaman pengembalian pada periode ini.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $r): ?>
                    <tr>
                        <td class="table-cell-center"><?= $no++ ?></td>
                        <td class="table-cell-center" style="mso-number-format:'\@';"><?= esc($r['kode_transaksi']) ?></td>
                        <td class="table-cell"><?= esc($r['nama_anggota']) ?></td>
                        <td class="table-cell"><?= esc($r['judul_buku']) ?></td>
                        <td class="table-cell-center"><?= date('Y-m-d', strtotime($r['tanggal_pinjam'])) ?></td>
                        <td class="table-cell-center"><?= date('Y-m-d', strtotime($r['tanggal_jatuh_tempo'])) ?></td>
                        <td class="table-cell-center"><?= date('Y-m-d', strtotime($r['tanggal_kembali'])) ?></td>
                        <td class="table-cell-center"><?= (int)$r['jumlah_hari_terlambat'] ?></td>
                        <td class="table-cell-right"><?= (float)$r['denda'] ?></td>
                        <td class="table-cell-center"><?= ucfirst(esc($r['status_denda'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="8" style="text-align: right; font-weight: bold; border: 0.5pt solid #000000;">TOTAL KAS DENDA:</td>
                    <td class="table-cell-right" style="font-weight: bold; border: 0.5pt solid #000000;"><?= $totalDenda ?></td>
                    <td style="border: 0.5pt solid #000000;"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
