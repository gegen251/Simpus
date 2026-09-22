<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengembalian Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            font-size: 16px;
            font-weight: bold;
            color: #1B2A4A;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10px;
            color: #555;
            margin: 0;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title h2 {
            font-size: 13px;
            font-weight: bold;
            color: #1B2A4A;
            margin: 0 0 3px 0;
            text-decoration: underline;
        }
        .doc-title span {
            font-size: 10px;
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
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 5px 8px;
            font-size: 10px;
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
            margin-top: 30px;
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
        <h2>LAPORAN PENGEMBALIAN BUKU & PENERIMAAN DENDA</h2>
        <span>Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?> &bull; Tanggal Cetak: <?= $today ?></span>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 100px;">Kode Transaksi</th>
                <th style="width: 130px;">Nama Peminjam</th>
                <th>Judul Buku</th>
                <th style="width: 70px;" class="text-center">Tgl Pinjam</th>
                <th style="width: 70px;" class="text-center">Tgl Kembali</th>
                <th style="width: 55px;" class="text-center">Terlambat</th>
                <th style="width: 80px;" class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94A3B8;">Tidak ada data pengembalian buku pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $r): ?>
                    <tr>
                        <td class="text-center font-mono"><?= $no++ ?></td>
                        <td class="font-mono" style="font-weight: bold;"><?= esc($r['kode_transaksi']) ?></td>
                        <td>
                            <strong><?= esc($r['nama_anggota']) ?></strong><br>
                            <span style="font-size: 9px; color: #64748B;"><?= esc($r['nomor_anggota']) ?></span>
                        </td>
                        <td><?= esc($r['judul_buku']) ?></td>
                        <td class="text-center font-mono"><?= date('d/m/Y', strtotime($r['tanggal_pinjam'])) ?></td>
                        <td class="text-center font-mono"><?= date('d/m/Y', strtotime($r['tanggal_kembali'])) ?></td>
                        <td class="text-center font-mono">
                            <?= $r['jumlah_hari_terlambat'] > 0 ? $r['jumlah_hari_terlambat'] . ' hari' : '0' ?>
                        </td>
                        <td class="text-right font-mono" style="font-weight: bold;">
                            <?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : 'Rp 0' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total Row -->
                <tr style="background-color: #F1F5F9; font-weight: bold;">
                    <td colspan="7" class="text-right" style="padding: 7px 8px;">TOTAL PENERIMAAN DENDA:</td>
                    <td class="text-right font-mono" style="padding: 7px 8px; color: #B91C1C;">
                        Rp <?= number_format($totalDenda, 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Pengelola -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                <p>Petugas Administrasi,</p>
                <br><br><br>
                <p><strong><?= esc($adminNama) ?></strong><br><span style="font-size: 9px; color: #64748B;">Staff Sirkulasi</span></p>
            </td>
            <td style="width: 50%; text-align: right;">
                <p>Jakarta, <?= $today ?><br>Mengetahui, Kepala Sekolah</p>
                <br><br><br>
                <p><strong><?= esc($config['kepala_perpustakaan'] ?? 'Dra. Hj. Sri Wahyuni, M.Pd') ?></strong><br><span style="font-size: 9px; color: #64748B;">NIP. <?= esc($config['nip_kepala'] ?? '19720315 199702 2 001') ?></span></p>
            </td>
        </tr>
    </table>

</body>
</html>
