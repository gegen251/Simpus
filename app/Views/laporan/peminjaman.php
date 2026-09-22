<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="file-text" class="w-5 h-5 text-sky-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Laporan Sirkulasi Peminjaman Buku</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rekapitulasi berkala transaksi peminjaman buku siswa Sekolah Dasar untuk evaluasi dan arsip sekolah</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="<?= site_url('/laporan/semua') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Semua Laporan
            </a>
            <a href="<?= site_url('/laporan/peminjaman') ?>" class="px-3.5 py-2 bg-navy-900 text-white text-xs font-semibold rounded-lg shadow-xs">
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

    <!-- Filter & Action Card (FR-22, FR-25) -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="<?= site_url('/laporan/peminjaman') ?>" method="GET" class="flex flex-col lg:flex-row lg:items-end justify-between gap-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal Pinjam</label>
                    <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal Pinjam</label>
                    <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Filter Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                        <option value="">Semua Status Sirkulasi</option>
                        <option value="dipinjam" <?= ($status ?? '') === 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="terlambat" <?= ($status ?? '') === 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                        <option value="dikembalikan" <?= ($status ?? '') === 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1 lg:pt-0 flex-wrap">
                <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>Filter</span>
                </button>

                <!-- 3 Export Options -->
                <a href="<?= site_url('/laporan/pdf/peminjaman?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate) . '&status=' . urlencode($status ?? '')) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Format PDF">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>PDF</span>
                </a>

                <a href="<?= site_url('/laporan/word/peminjaman?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate) . '&status=' . urlencode($status ?? '')) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Format Microsoft Word (.doc)">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Word</span>
                </a>

                <a href="<?= site_url('/laporan/excel/peminjaman?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate) . '&status=' . urlencode($status ?? '')) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs"
                   title="Export Format Microsoft Excel (.xls)">
                    <i data-lucide="sheet" class="w-3.5 h-3.5"></i>
                    <span>Excel</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Preview Section -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Hasil Peminjaman: </span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-mono"><?= count($dataLaporan) ?> Data</span>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Cepat</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No</th>
                        <th class="px-4 py-3.5 text-center">Kode Transaksi</th>
                        <th class="px-4 py-3.5">Nama Anggota</th>
                        <th class="px-4 py-3.5">Judul Buku</th>
                        <th class="px-4 py-3.5 text-center">Tanggal Pinjam</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($dataLaporan)): ?>
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada data transaksi peminjaman pada rentang tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dataLaporan as $row): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-900 dark:text-slate-200 text-center"><?= esc($row['kode_transaksi']) ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100"><?= esc($row['nama_anggota']) ?></div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($row['nomor_anggota']) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-slate-800 dark:text-slate-200" title="<?= esc($row['judul_buku']) ?>">
                                    <?= esc($row['judul_buku']) ?>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-400 text-center text-xs"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-400 text-center text-xs"><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if ($row['status'] === 'dipinjam'): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">Dipinjam</span>
                                    <?php elseif ($row['status'] === 'terlambat'): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Terlambat</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <a href="<?= site_url('/peminjaman/detail/' . $row['id']) ?>" 
                                       target="_blank"
                                       class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors inline-flex items-center justify-center" 
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
