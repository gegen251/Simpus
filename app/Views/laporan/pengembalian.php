<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#131B2E] p-5 sm:p-6 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-[#2F6E4E] flex items-center justify-center text-white shadow-sm shrink-0">
                <i data-lucide="file-check" class="w-5 h-5 text-emerald-100"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-[#1B2A4A] dark:text-slate-100">Laporan Pengembalian & Denda Siswa SD</h3>
                <p class="text-xs text-[#5C6470] dark:text-slate-400 mt-0.5">Rekapitulasi pengembalian buku dan kas denda edukatif siswa SDN 12 Sumbawa</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="<?= site_url('/laporan/semua') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Semua Laporan
            </a>
            <a href="<?= site_url('/laporan/peminjaman') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Laporan Peminjaman
            </a>
            <a href="<?= site_url('/laporan/pengembalian') ?>" class="px-3 py-1.5 bg-[#1B2A4A] text-white text-xs font-semibold rounded-lg shadow-sm border border-[#1B2A4A]">
                Laporan Pengembalian
            </a>
            <a href="<?= site_url('/laporan/buku') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Laporan Koleksi Buku
            </a>
        </div>
    </div>

    <!-- Filter & Action Card (FR-23, FR-25) -->
    <div class="bg-white dark:bg-[#131B2E] p-5 sm:p-6 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm">
        <form action="<?= site_url('/laporan/pengembalian') ?>" method="GET" class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                <div>
                    <label class="block text-[11px] font-semibold text-[#5C6470] dark:text-slate-300 uppercase tracking-wider mb-1.5">Dari Tanggal Kembali</label>
                    <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="w-full px-3.5 py-2 rounded-lg border border-[#E5DFD3] dark:border-slate-700 bg-[#FBF9F5] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#1B2A4A]/20 focus:border-[#1B2A4A] focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#5C6470] dark:text-slate-300 uppercase tracking-wider mb-1.5">Sampai Tanggal Kembali</label>
                    <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="w-full px-3.5 py-2 rounded-lg border border-[#E5DFD3] dark:border-slate-700 bg-[#FBF9F5] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#1B2A4A]/20 focus:border-[#1B2A4A] focus:outline-none transition-all">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2 lg:pt-0 flex-wrap">
                <button type="submit" class="px-4 py-2 bg-[#1B2A4A] hover:bg-[#24375D] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 shadow-sm transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Filter</span>
                </button>

                <!-- 3 Export Options -->
                <a href="<?= site_url('/laporan/pdf/pengembalian?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#C1613A] hover:bg-[#A95330] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format PDF">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>PDF</span>
                </a>

                <a href="<?= site_url('/laporan/word/pengembalian?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2B4C7E] hover:bg-[#1E365B] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format Microsoft Word (.doc)">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Word</span>
                </a>

                <a href="<?= site_url('/laporan/excel/pengembalian?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)) ?>"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2F6E4E] hover:bg-[#25573E] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format Microsoft Excel (.xls)">
                    <i data-lucide="sheet" class="w-3.5 h-3.5"></i>
                    <span>Excel</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-[#131B2E] p-5 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] text-[#5C6470] dark:text-slate-400 font-semibold uppercase tracking-wider block">Total Transaksi Pengembalian</span>
                <span class="text-xl font-bold text-[#1B2A4A] dark:text-slate-100 font-serif mt-0.5 block"><?= count($dataLaporan) ?> Data</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-[#F8F6F0] dark:bg-slate-800 flex items-center justify-center text-[#1B2A4A] dark:text-slate-300">
                <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white dark:bg-[#131B2E] p-5 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] text-[#5C6470] dark:text-slate-400 font-semibold uppercase tracking-wider block">Total Penerimaan Denda</span>
                <span class="text-xl font-bold text-[#2F6E4E] dark:text-emerald-400 font-mono mt-0.5 block">Rp <?= number_format($totalDenda, 0, ',', '.') ?></span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-[#2F6E4E] dark:text-emerald-400">
                <i data-lucide="coins" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Preview Table Section -->
    <div class="bg-white dark:bg-[#131B2E] rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8F6F0] dark:bg-slate-800/80 text-[#5C6470] dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-[#E5DFD3] dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5 w-12 text-center">No</th>
                        <th class="px-5 py-3.5 text-center">Kode Transaksi</th>
                        <th class="px-5 py-3.5">Nama Peminjam</th>
                        <th class="px-5 py-3.5">Judul Buku</th>
                        <th class="px-5 py-3.5 text-center">Tgl Pinjam</th>
                        <th class="px-5 py-3.5 text-center">Tgl Kembali</th>
                        <th class="px-5 py-3.5 text-center">Hari Terlambat</th>
                        <th class="px-5 py-3.5 text-center">Denda</th>
                        <th class="px-5 py-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5DFD3]/60 dark:divide-slate-800/60">
                    <?php if (empty($dataLaporan)): ?>
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-[#5C6470] dark:text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600"></i>
                                Tidak ada data pengembalian buku pada rentang tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dataLaporan as $row): ?>
                            <tr class="hover:bg-[#F8F6F0]/50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 text-center text-[#5C6470] font-mono"><?= $no++ ?></td>
                                <td class="px-5 py-3.5 font-mono font-bold text-[#1B2A4A] dark:text-slate-200 text-center"><?= esc($row['kode_transaksi']) ?></td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#1B2A4A] dark:text-slate-100"><?= esc($row['nama_anggota']) ?></div>
                                    <div class="text-[10px] text-[#5C6470] font-mono"><?= esc($row['nomor_anggota']) ?></div>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs truncate text-[#1B2A4A] dark:text-slate-300" title="<?= esc($row['judul_buku']) ?>">
                                    <?= esc($row['judul_buku']) ?>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-[#5C6470] dark:text-slate-400 text-center"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td class="px-5 py-3.5 font-mono text-[#1B2A4A] dark:text-slate-200 font-semibold text-center"><?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?></td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($row['jumlah_hari_terlambat'] > 0): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <?= $row['jumlah_hari_terlambat'] ?> Hari
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 dark:text-slate-500">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center font-mono font-bold">
                                    <?php if ($row['denda'] > 0): ?>
                                        <span class="text-[#C1613A] dark:text-rose-400">Rp <?= number_format($row['denda'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 dark:text-slate-500">Rp 0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    <a href="<?= site_url('/peminjaman/detail/' . $row['peminjaman_id']) ?>" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center p-1.5 text-[#1B2A4A] dark:text-slate-300 hover:text-white bg-[#F8F6F0] hover:bg-[#1B2A4A] dark:bg-slate-800 dark:hover:bg-slate-700 border border-[#E5DFD3] dark:border-slate-700 rounded-lg transition-colors shadow-xs" 
                                       title="Lihat Detail & Bukti Struk">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
