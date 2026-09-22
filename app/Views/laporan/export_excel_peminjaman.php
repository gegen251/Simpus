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
    </style>
</head>
<body>
    <table>
        <tr><td colspan="9" class="kop-title"><?= esc($config['nama_perpustakaan'] ?? 'PERPUSTAKAAN SEKOLAH DASAR') ?></td></tr>
        <tr><td colspan="9" class="kop-sub"><?= esc($config['alamat_perpustakaan'] ?? 'Sistem Informasi Manajemen Perpustakaan Terpadu') ?></td></tr>
        <tr><td colspan="9"></td></tr>
        <tr><td colspan="9" class="doc-title">LAPORAN TRANSAKSI PEMINJAMAN BUKU</td></tr>
        <tr><td colspan="9" style="text-align: center; font-size: 9.5pt;">Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></td></tr>
        <tr><td colspan="9"></td></tr>
        <thead>
            <tr>
                <th class="table-header">No</th>
                <th class="table-header">Kode Transaksi</th>
                <th class="table-header">Tanggal Pinjam</th>
                <th class="table-header">Nama Anggota</th>
                <th class="table-header">Nomor Anggota</th>
                <th class="table-header">Judul Buku</th>
                <th class="table-header">Kategori</th>
                <th class="table-header">Jatuh Tempo</th>
                <th class="table-header">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr><td colspan="9" class="table-cell-center">Tidak ada data peminjaman pada periode ini.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $r): ?>
                    <tr>
                        <td class="table-cell-center"><?= $no++ ?></td>
                        <td class="table-cell-center" style="mso-number-format:'\@';"><?= esc($r['kode_transaksi']) ?></td>
                        <td class="table-cell-center"><?= date('Y-m-d', strtotime($r['tanggal_pinjam'])) ?></td>
                        <td class="table-cell"><?= esc($r['nama_anggota']) ?></td>
                        <td class="table-cell-center" style="mso-number-format:'\@';"><?= esc($r['nomor_anggota']) ?></td>
                        <td class="table-cell"><?= esc($r['judul_buku']) ?></td>
                        <td class="table-cell"><?= esc($r['nama_kategori']) ?></td>
                        <td class="table-cell-center"><?= date('Y-m-d', strtotime($r['tanggal_jatuh_tempo'])) ?></td>
                        <td class="table-cell-center"><?= ucfirst(esc($r['status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
