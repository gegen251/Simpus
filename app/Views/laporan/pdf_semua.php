<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lengkap Sirkulasi Perpustakaan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #232323;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1B2A4A;
            padding-bottom: 10px;
            margin-bottom: 12px;
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
            margin-bottom: 12px;
        }
        .doc-title h2 {
            font-size: 13px;
            font-weight: bold;
            color: #1B2A4A;
            margin: 0 0 3px 0;
            text-decoration: underline;
        }
        .doc-title span {
            font-size: 9.5px;
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
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-dipinjam { background-color: #FEF3C7; color: #92400E; }
        .badge-terlambat { background-color: #FFE4E6; color: #9F1239; }
        .badge-dikembalikan { background-color: #D1FAE5; color: #065F46; }
        .signatures {
            width: 100%;
            margin-top: 25px;
            border: none;
        }
        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9.5px;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Resmi -->
    <div class="header">
        <h1><?= esc($config['nama_perpustakaan'] ?? 'PERPUSTAKAAN SEKOLAH DASAR') ?></h1>
        <p><?= esc($config['alamat_perpustakaan'] ?? 'Sistem Informasi Manajemen Perpustakaan Terpadu') ?></p>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>REKAPITULASI SEMUA TRANSAKSI SIRKULASI</h2>
        <span>Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></span>
    </div>

    <!-- Tabel Data Sirkulasi -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 85px;">Kode & Tgl Pinjam</th>
                <th>Nama Anggota (Siswa/Guru)</th>
                <th>Buku Bacaan & Kategori</th>
                <th class="text-center" style="width: 65px;">Jatuh Tempo</th>
                <th class="text-center" style="width: 65px;">Tgl Kembali</th>
                <th class="text-center" style="width: 70px;">Status</th>
                <th class="text-right" style="width: 65px;">Denda (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748B;">Tidak ada rekaman transaksi sirkulasi pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($dataLaporan as $row): ?>
                    <tr>
                        <td class="text-center font-mono"><?= $no++ ?></td>
                        <td>
                            <strong class="font-mono"><?= esc($row['kode_transaksi']) ?></strong><br>
                            <span style="font-size: 8px; color: #64748B;"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></span>
                        </td>
                        <td>
                            <strong><?= esc($row['nama_anggota']) ?></strong><br>
                            <span style="font-size: 8px; color: #64748B;">No: <?= esc($row['nomor_anggota']) ?> <?= !empty($row['identitas_anggota']) ? ' | ' . esc($row['identitas_anggota']) : '' ?></span>
                        </td>
                        <td>
                            <strong><?= esc($row['judul_buku']) ?></strong><br>
                            <span style="font-size: 8px; color: #64748B;"><?= esc($row['kode_buku']) ?> &bull; <?= esc($row['nama_kategori']) ?></span>
                        </td>
                        <td class="text-center font-mono"><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                        <td class="text-center font-mono">
                            <?= !empty($row['tanggal_kembali']) ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] === 'dipinjam'): ?>
                                <span class="badge badge-dipinjam">Dipinjam</span>
                            <?php elseif ($row['status'] === 'terlambat'): ?>
                                <span class="badge badge-terlambat">Terlambat</span>
                            <?php else: ?>
                                <span class="badge badge-dikembalikan">Kembali</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right font-mono">
                            <?= (!empty($row['denda']) && (float)$row['denda'] > 0) ? number_format($row['denda'], 0, ',', '.') : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7" class="text-right" style="font-weight: bold; background-color: #F1F5F9;">TOTAL KAS DENDA TERKUMPUL:</td>
                    <td class="text-right font-mono" style="font-weight: bold; background-color: #F1F5F9; color: #9F1239;">
                        Rp <?= number_format($totalDenda, 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi -->
    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah / Perpustakaan<br><br><br><br><br>
                <strong><u><?= esc($config['kepala_perpustakaan'] ?? 'Dra. Hj. Sri Wahyuni, M.Pd') ?></u></strong><br>
                NIP. <?= esc($config['nip_kepala'] ?? '19720315 199702 2 001') ?>
            </td>
            <td>
                Dicetak pada: <?= $today ?><br>
                Petugas Administrator Pustaka<br><br><br><br><br>
                <strong><u><?= esc($adminNama) ?></u></strong><br>
                Petugas SIMPUS SD
            </td>
        </tr>
    </table>
</body>
</html>
