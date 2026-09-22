<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="layers" class="w-5 h-5 text-amber-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Cetak Laporan &amp; Rekapitulasi Sirkulasi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rekapitulasi perpustakaan: inventaris buku, anggota, transaksi pinjam-kembali, serta pendapatan denda</p>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="<?= site_url('/laporan/semua') ?>" class="px-3.5 py-2 bg-navy-900 text-white text-xs font-semibold rounded-lg shadow-xs">
                Semua Laporan
            </a>
            <a href="<?= site_url('/laporan/peminjaman') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Laporan Peminjaman
            </a>
            <a href="<?= site_url('/laporan/pengembalian') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Laporan Pengembalian
            </a>
            <a href="<?= site_url('/laporan/buku') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Laporan Koleksi Buku
            </a>
        </div>
    </div>

    <!-- KPI Aggregate Statistic Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Judul Buku -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Judul Buku</span>
                <i data-lucide="book" class="w-4 h-4"></i>
            </div>
            <div class="text-lg font-bold text-slate-900 dark:text-white font-mono"><?= number_format($stats['totalBuku']) ?></div>
            <div class="text-[10px] text-slate-400 mt-0.5"><?= number_format($stats['totalEksemplar']) ?> Eks</div>
        </div>

        <!-- Total Anggota -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Anggota Aktif</span>
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <div class="text-lg font-bold text-slate-900 dark:text-white font-mono"><?= number_format($stats['totalAnggota']) ?></div>
            <div class="text-[10px] text-slate-400 mt-0.5">Siswa &amp; Guru SD</div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Peminjaman</span>
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </div>
            <div class="text-lg font-bold text-slate-900 dark:text-white font-mono"><?= number_format($stats['totalPinjam']) ?></div>
            <div class="text-[10px] text-slate-400 mt-0.5">Periode Terpilih</div>
        </div>

        <!-- Buku Sedang Dipinjam -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sedang Dipinjam</span>
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
            <div class="text-lg font-bold text-amber-700 dark:text-amber-400 font-mono"><?= number_format($stats['totalAktif'] + $stats['totalTerlambat']) ?></div>
            <div class="text-[10px] text-amber-600 mt-0.5"><?= $stats['totalTerlambat'] ?> Terlambat</div>
        </div>

        <!-- Selesai Dikembalikan -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Dikembalikan</span>
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
            <div class="text-lg font-bold text-emerald-700 dark:text-emerald-400 font-mono"><?= number_format($stats['totalKembali']) ?></div>
            <div class="text-[10px] text-slate-400 mt-0.5">Transaksi Selesai</div>
        </div>

        <!-- Kas Denda Terkumpul -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-rose-600 dark:text-rose-400 mb-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kas Denda</span>
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </div>
            <div class="text-base font-bold text-rose-600 dark:text-rose-400 font-mono">Rp <?= number_format($stats['totalDenda'], 0, ',', '.') ?></div>
            <div class="text-[10px] text-slate-400 mt-0.5">Kas Perpustakaan</div>
        </div>
    </div>

    <!-- Filter & Action Card (Date Filter + 3 Export Buttons) -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="<?= site_url('/laporan/semua') ?>" method="GET" class="flex flex-col lg:flex-row lg:items-end justify-between gap-3.5">
            <!-- Date Filters -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal Pinjam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <input type="date" name="start_date" value="<?= esc($startDate) ?>" 
                               class="w-full pl-9 pr-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    </div>
                </div>

                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal Pinjam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <input type="date" name="end_date" value="<?= esc($endDate) ?>" 
                               class="w-full pl-9 pr-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    </div>
                </div>

                <div class="pt-1 sm:pt-5">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-amber-300"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <!-- Export Buttons: PDF, Word, Excel -->
            <div class="flex items-center gap-2 pt-1 lg:pt-0 flex-wrap">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 mr-1 hidden sm:inline">Export:</span>
                
                <!-- PDF Export -->
                <a href="<?= site_url('/laporan/pdf/semua?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Laporan Format PDF (Kop Resmi)">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>PDF</span>
                </a>

                <!-- Word Export -->
                <a href="<?= site_url('/laporan/word/semua?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Laporan Format Microsoft Word (.doc)">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Word</span>
                </a>

                <!-- Excel Export -->
                <a href="<?= site_url('/laporan/excel/semua?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Laporan Format Microsoft Excel (.xls)">
                    <i data-lucide="sheet" class="w-3.5 h-3.5"></i>
                    <span>Excel</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Preview Section -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h4 class="font-serif font-bold text-sm text-navy-900 dark:text-slate-100">Tabel Rekapitulasi Sirkulasi</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?> &bull; Total <?= count($dataLaporan) ?> Rekaman Transaksi</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No</th>
                        <th class="px-4 py-3.5">Kode &amp; Waktu Pinjam</th>
                        <th class="px-4 py-3.5">Peminjam</th>
                        <th class="px-4 py-3.5">Buku</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-center">Tgl Kembali</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-right">Denda</th>
                        <th class="px-4 py-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($dataLaporan)): ?>
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <i data-lucide="inbox" class="w-6 h-6"></i>
                                </div>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada riwayat transaksi pada rentang tanggal ini</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan pilihan rentang tanggal filter Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dataLaporan as $row): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-mono text-xs"><?= $no++ ?></td>
                                
                                <td class="px-4 py-3.5">
                                    <span class="font-mono font-bold text-slate-900 dark:text-white"><?= esc($row['kode_transaksi']) ?></span>
                                    <span class="block text-[11px] text-slate-400 font-mono"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200"><?= esc($row['nama_anggota']) ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= esc($row['nomor_anggota']) ?></div>
                                </td>

                                <td class="px-4 py-3.5 max-w-xs">
                                    <div class="font-medium text-slate-800 dark:text-slate-200 truncate"><?= esc($row['judul_buku']) ?></div>
                                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono"><?= esc($row['kode_buku']) ?></span>
                                        <?php if (!empty($row['nama_kategori'])): ?>
                                            <span>&bull;</span>
                                            <span class="text-navy-800 dark:text-sky-300 truncate"><?= esc($row['nama_kategori']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center font-mono text-slate-600 dark:text-slate-400 text-xs">
                                    <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                                </td>

                                <td class="px-4 py-3.5 text-center font-mono text-slate-600 dark:text-slate-400 text-xs">
                                    <?= !empty($row['tanggal_kembali']) ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '<span class="text-slate-300 dark:text-slate-600">-</span>' ?>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if ($row['status'] === 'dipinjam'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                            Dipinjam
                                        </span>
                                    <?php elseif ($row['status'] === 'terlambat'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Terlambat
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Dikembalikan
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3.5 text-right font-mono font-semibold text-xs">
                                    <?php if (!empty($row['denda']) && (float)$row['denda'] > 0): ?>
                                        <span class="text-rose-600 dark:text-rose-400">Rp <?= number_format($row['denda'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">Rp 0</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Tombol View Detail Transaksi Modal -->
                                        <button type="button" 
                                                onclick='viewDetailTransaksi(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                                title="Lihat Detail Transaksi">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <!-- Tombol Buka Bukti Struk -->
                                        <a href="<?= site_url('/peminjaman/detail/' . $row['id']) ?>" 
                                           target="_blank"
                                           class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                           title="Buka / Cetak Struk Peminjaman">
                                            <i data-lucide="receipt" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL VIEW DETAIL TRANSAKSI SIRKULASI                                     -->
<!-- ========================================================================= -->
<div id="modalDetailTransaksiLaporan" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        
        <!-- Modal Header -->
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Detail Transaksi Sirkulasi</h4>
                    <p class="text-[11px] text-slate-300">Rincian peminjaman, pengembalian, dan denda</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalDetailTransaksiLaporan')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-3.5 overflow-y-auto max-h-[80vh]">
            <!-- Status & Transaction Code Header -->
            <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Kode Transaksi</span>
                    <span id="dtl_kode" class="font-mono font-bold text-sm text-navy-900 dark:text-white">TR-0000</span>
                </div>
                <div id="dtl_status_badge">
                    <!-- Dynamic Badge -->
                </div>
            </div>

            <!-- Anggota & Buku Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Info Peminjam -->
                <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-1.5">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Data Peminjam</span>
                    </div>
                    <div class="space-y-1 text-[11px]">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Nama Lengkap</span>
                            <span id="dtl_nama_anggota" class="font-bold text-slate-800 dark:text-slate-100 text-xs"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">No. Anggota / NISN</span>
                            <span id="dtl_no_anggota" class="font-mono text-slate-700 dark:text-slate-300"></span>
                        </div>
                    </div>
                </div>

                <!-- Info Buku -->
                <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-1.5">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <i data-lucide="book-open" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Koleksi Buku</span>
                    </div>
                    <div class="space-y-1 text-[11px]">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Judul Buku</span>
                            <span id="dtl_judul_buku" class="font-bold text-slate-800 dark:text-slate-100 text-xs line-clamp-2"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Kode &amp; Kategori</span>
                            <span id="dtl_kategori_buku" class="text-slate-700 dark:text-slate-300 font-mono"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline & Denda Box -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2.5">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Tgl Pinjam</span>
                        <span id="dtl_tgl_pinjam" class="font-mono font-semibold text-slate-800 dark:text-slate-200 text-xs">-</span>
                    </div>
                    <div class="p-2 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Jatuh Tempo</span>
                        <span id="dtl_tgl_tempo" class="font-mono font-semibold text-amber-700 dark:text-amber-400 text-xs">-</span>
                    </div>
                    <div class="p-2 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Tgl Kembali</span>
                        <span id="dtl_tgl_kembali" class="font-mono font-semibold text-emerald-700 dark:text-emerald-400 text-xs">-</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Total Denda:</span>
                    <span id="dtl_denda" class="font-mono font-bold text-sm text-slate-900 dark:text-white">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Petugas Pencatat:</span>
                    <span id="dtl_admin" class="font-medium text-slate-800 dark:text-slate-200">-</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <button type="button" onclick="closeModal('modalDetailTransaksiLaporan')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                Tutup
            </button>
            <a id="btnBukaStruk" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-amber-300"></i>
                <span>Buka Bukti Struk Cetak</span>
            </a>
        </div>
    </div>
</div>

<script>
    function viewDetailTransaksi(data) {
        document.getElementById('dtl_kode').textContent = data.kode_transaksi;
        document.getElementById('dtl_nama_anggota').textContent = data.nama_anggota;
        document.getElementById('dtl_no_anggota').textContent = data.nomor_anggota + (data.identitas_anggota ? ' • ' + data.identitas_anggota : '');
        document.getElementById('dtl_judul_buku').textContent = data.judul_buku;
        document.getElementById('dtl_kategori_buku').textContent = data.kode_buku + (data.nama_kategori ? ' • ' + data.nama_kategori : '');

        document.getElementById('dtl_tgl_pinjam').textContent = data.tanggal_pinjam || '-';
        document.getElementById('dtl_tgl_tempo').textContent = data.tanggal_jatuh_tempo || '-';
        document.getElementById('dtl_tgl_kembali').textContent = data.tanggal_kembali || 'Belum kembali';

        const dendaVal = parseFloat(data.denda) || 0;
        const dendaEl = document.getElementById('dtl_denda');
        if (dendaVal > 0) {
            dendaEl.textContent = 'Rp ' + dendaVal.toLocaleString('id-ID');
            dendaEl.className = 'font-mono font-bold text-sm text-rose-600 dark:text-rose-400';
        } else {
            dendaEl.textContent = 'Rp 0 (Tidak ada denda)';
            dendaEl.className = 'font-mono font-bold text-sm text-emerald-700 dark:text-emerald-400';
        }

        document.getElementById('dtl_admin').textContent = data.nama_admin || 'Administrator';

        // Badge Status
        const badgeContainer = document.getElementById('dtl_status_badge');
        if (data.status === 'dipinjam') {
            badgeContainer.innerHTML = '<span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200">Dipinjam</span>';
        } else if (data.status === 'terlambat') {
            badgeContainer.innerHTML = '<span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">Terlambat</span>';
        } else {
            badgeContainer.innerHTML = '<span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">Dikembalikan</span>';
        }

        // Action Struk Link
        document.getElementById('btnBukaStruk').href = '<?= site_url('/peminjaman/detail/') ?>' + data.id;

        if (window.lucide) lucide.createIcons();
        openModal('modalDetailTransaksiLaporan');
    }
</script>

<?= $this->endSection() ?>
