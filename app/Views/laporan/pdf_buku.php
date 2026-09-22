<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #232323;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1B2A4A;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 15px;
            font-weight: bold;
            color: #1B2A4A;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9px;
            color: #555;
            margin: 0;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title h2 {
            font-size: 12px;
            font-weight: bold;
            color: #1B2A4A;
            margin: 0 0 3px 0;
            text-decoration: underline;
        }
        .doc-title span {
            font-size: 9px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #CBD5E1;
        }
        th {
            background-color: #1B2A4A;
            color: #FFFFFF;
            font-weight: bold;
            padding: 5px 6px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            padding: 4px 6px;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', monospace; }
        .signature-table {
            width: 100%;
            border: none;
            margin-top: 25px;
        }
        .signature-table td {
            border: none;
            background: transparent !important;
            padding: 0;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi -->
    <?php 
    $logoPath = FCPATH . 'images/logo.png';
    $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    ?>
    <table style="width: 100%; border: none; margin-bottom: 12px; border-bottom: 2px solid #1B2A4A; padding-bottom: 8px;">
        <tr>
            <?php if ($logoData): ?>
            <td style="width: 65px; text-align: center; vertical-align: middle; border: none; padding: 0;">
                <img src="<?= $logoData ?>" style="width: 50px; height: 50px;" alt="Logo SIMPUS">
            </td>
            <?php endif; ?>
            <td style="text-align: center; vertical-align: middle; border: none; padding: 0; padding-right: <?= $logoData ? '65px' : '0' ?>;">
                <h1 style="font-family: 'Times New Roman', serif; font-size: 15pt; margin: 0; color: #1B2A4A; letter-spacing: 1px;">SIMPUS SD</h1>
                <p style="font-size: 8.5pt; color: #333; margin: 2px 0 0 0; font-weight: bold;"><?= esc($config['nama_perpustakaan'] ?? 'SD Negeri 12 Sumbawa') ?></p>
                <p style="font-size: 7.5pt; color: #666; margin: 1px 0 0 0;"><?= esc($config['alamat_perpustakaan'] ?? 'Jl. Sumbawa, Sungai Duri, Kec. Sungai Raya, Kabupaten Bengkayang, Kalimantan Barat 79271') ?></p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>LAPORAN DATA INVENTARIS KOLEKSI BUKU SD</h2>
        <span>Kondisi Stok Fisik &bull; Tanggal Cetak: <?= $today ?></span>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 20px;" class="text-center">No</th>
                <th style="width: 65px;">Kode Buku</th>
                <th>Judul Buku & Penulis</th>
                <th style="width: 100px;">Kategori</th>
                <th style="width: 85px;">Penerbit / Thn</th>
                <th style="width: 45px;" class="text-center">Fisik</th>
                <th style="width: 45px;" class="text-center">Stok</th>
                <th style="width: 45px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94A3B8;">Tidak ada data buku yang sesuai.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $b): ?>
                    <tr>
                        <td class="text-center font-mono"><?= $no++ ?></td>
                        <td class="font-mono" style="font-weight: bold;"><?= esc($b['kode_buku']) ?></td>
                        <td>
                            <strong><?= esc($b['judul']) ?></strong><br>
                            <span style="font-size: 8px; color: #64748B;">Penulis: <?= esc($b['penulis']) ?></span>
                        </td>
                        <td><?= esc($b['nama_kategori'] ?: 'Umum') ?></td>
                        <td>
                            <?= esc($b['penerbit']) ?> (<?= esc($b['tahun_terbit'] ?: '-') ?>)
                        </td>
                        <td class="text-center font-mono font-bold"><?= $b['jumlah_eksemplar'] ?></td>
                        <td class="text-center font-mono font-bold" style="color: <?= $b['stok_tersedia'] > 0 ? '#15803D' : '#B91C1C' ?>;">
                            <?= $b['stok_tersedia'] ?>
                        </td>
                        <td class="text-center font-mono"><?= ucfirst($b['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Pengelola -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                <p>Petugas Inventaris,</p>
                <br><br><br>
                <p><strong><?= esc($adminNama) ?></strong><br><span style="font-size: 8px; color: #64748B;">Staf Perpustakaan SD</span></p>
            </td>
            <td style="width: 50%; text-align: right;">
                <p>Jakarta, <?= $today ?><br>Mengetahui, Kepala Sekolah</p>
                <br><br><br>
                <p><strong><?= esc($config['kepala_perpustakaan'] ?? 'Dra. Hj. Sri Wahyuni, M.Pd') ?></strong><br><span style="font-size: 8px; color: #64748B;">NIP. <?= esc($config['nip_kepala'] ?? '19720315 199702 2 001') ?></span></p>
            </td>
        </tr>
    </table>

</body>
</html>
