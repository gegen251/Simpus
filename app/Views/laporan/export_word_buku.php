<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Laporan Koleksi Data Buku</title>
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
        <h3>LAPORAN INVENTARIS KOLEKSI DATA BUKU</h3>
        <span>Dicetak tanggal: <?= $today ?></span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kode Buku</th>
                <th>Judul Buku</th>
                <th>Kategori</th>
                <th>Penulis / Pengarang</th>
                <th>Penerbit & Thn</th>
                <th>Lokasi Rak</th>
                <th>Eksemplar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px;">Tidak ada koleksi buku pada kategori ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; $totalEksemplar = 0; foreach ($dataLaporan as $r): $totalEksemplar += (int)$r['jumlah_eksemplar']; ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><b><?= esc($r['kode_buku']) ?></b></td>
                        <td><?= esc($r['judul']) ?></td>
                        <td><?= esc($r['nama_kategori']) ?></td>
                        <td><?= esc($r['penulis'] ?: '-') ?></td>
                        <td><?= esc($r['penerbit'] ?: '-') ?><?= !empty($r['tahun_terbit']) ? ' (' . esc($r['tahun_terbit']) . ')' : '' ?></td>
                        <td class="text-center"><?= esc($r['lokasi_rak'] ?: '-') ?></td>
                        <td class="text-center"><?= (int)$r['jumlah_eksemplar'] ?></td>
                        <td class="text-center"><?= ucfirst(esc($r['status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7" class="text-right" style="font-weight: bold; background-color: #F1F5F9;">TOTAL EKSEMPLAR BUKU:</td>
                    <td class="text-center" style="font-weight: bold; background-color: #F1F5F9;"><?= $totalEksemplar ?></td>
                    <td style="background-color: #F1F5F9;"></td>
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
