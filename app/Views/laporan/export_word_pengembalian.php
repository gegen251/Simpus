<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengembalian & Denda</title>
    <!--[if gte mso 9]>
    <xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>
    <![endif]-->
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        .kop h2 { font-size: 14pt; margin: 0 0 4px 0; text-transform: uppercase; }
        .kop p { font-size: 10pt; margin: 0; color: #333; }
        .title { text-align: center; margin-bottom: 16px; }
        .title h3 { font-size: 12pt; margin: 0 0 2px 0; text-decoration: underline; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; font-size: 9.5pt; }
        table.data-table th { background-color: #E2E8F0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signatures { width: 100%; border: none; margin-top: 30px; }
        .signatures td { border: none; width: 50%; text-align: center; vertical-align: top; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="kop">
        <h2><?= esc($config['nama_perpustakaan'] ?? 'PERPUSTAKAAN SEKOLAH DASAR') ?></h2>
        <p><?= esc($config['alamat_perpustakaan'] ?? 'Sistem Informasi Manajemen Perpustakaan Terpadu') ?></p>
    </div>

    <div class="title">
        <h3>LAPORAN PENGEMBALIAN BUKU & DENDA</h3>
        <span>Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kode Pinjam</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl Kembali</th>
                <th>Terlambat</th>
                <th>Denda (Rp)</th>
                <th>Status Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr>
                    <td colspan="10" class="text-center" style="padding: 15px;">Tidak ada rekaman pengembalian buku pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $r): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><b><?= esc($r['kode_transaksi']) ?></b></td>
                        <td><?= esc($r['nama_anggota']) ?></td>
                        <td><?= esc($r['judul_buku']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($r['tanggal_pinjam'])) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($r['tanggal_jatuh_tempo'])) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($r['tanggal_kembali'])) ?></td>
                        <td class="text-center"><?= (int)$r['jumlah_hari_terlambat'] ?> Hari</td>
                        <td class="text-right"><?= number_format($r['denda'], 0, ',', '.') ?></td>
                        <td class="text-center"><?= ucfirst(esc($r['status_denda'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="8" class="text-right" style="font-weight: bold; background-color: #F1F5F9;">TOTAL KAS DENDA:</td>
                    <td colspan="2" class="text-left" style="font-weight: bold; background-color: #F1F5F9;">Rp <?= number_format($totalDenda, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah / Perpustakaan<br><br><br><br><br>
                <b><u><?= esc($config['kepala_perpustakaan'] ?? 'Dra. Hj. Sri Wahyuni, M.Pd') ?></u></b><br>
                NIP. <?= esc($config['nip_kepala'] ?? '19720315 199702 2 001') ?>
            </td>
            <td>
                Dicetak pada: <?= $today ?><br>
                Petugas Administrator Perpustakaan<br><br><br><br><br>
                <b><u><?= esc($adminNama) ?></u></b><br>
                Petugas SIMPUS SD
            </td>
        </tr>
    </table>
</body>
</html>
