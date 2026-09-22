<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="history" class="w-5 h-5 text-sky-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Riwayat Pengembalian &amp; Rekap Denda</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar transaksi sirkulasi yang telah selesai dikembalikan ke perpustakaan sekolah</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= site_url('/pengembalian') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Proses Pengembalian
            </a>
            <a href="<?= site_url('/pengembalian/riwayat') ?>" class="px-3.5 py-2 bg-navy-900 text-white text-xs font-semibold rounded-lg shadow-xs">
                Riwayat Pengembalian
            </a>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Quick Search Form -->
        <form action="<?= site_url('/pengembalian/riwayat') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari kode transaksi, nama peminjam, judul buku..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-sky-500 focus:border-navy-900 focus:outline-none transition-colors">
            <?php if (!empty($startDate)): ?>
                <input type="hidden" name="start_date" value="<?= esc($startDate) ?>">
            <?php endif; ?>
            <?php if (!empty($endDate)): ?>
                <input type="hidden" name="end_date" value="<?= esc($endDate) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <!-- Modern Filter Pop-up Button -->
            <button type="button" onclick="openModal('modalFilterRiwayatPengembalian')" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-navy-600 dark:text-sky-400"></i>
                <span>Filter Riwayat</span>
                <?php if (!empty($activeFilterCount)): ?>
                    <span class="w-5 h-5 rounded-full bg-navy-900 dark:bg-sky-600 text-white text-[10px] flex items-center justify-center font-bold">
                        <?= $activeFilterCount ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
    </div>

    <!-- Active Filter Tags Bar -->
    <?php if (!empty($activeFilterCount)): ?>
        <div class="flex flex-wrap items-center justify-between gap-2 px-3.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">Filter Aktif:</span>
                <?php if (!empty($keyword)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Kata Kunci: <strong><?= esc($keyword) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($startDate)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Dari: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($endDate)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Sampai: <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <a href="<?= site_url('/pengembalian/riwayat') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                <span>Reset Filter</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Table of Return History (FR-21) -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 w-32 text-center">Kode Pinjam</th>
                        <th class="px-4 py-3.5">Data Peminjam</th>
                        <th class="px-4 py-3.5">Buku Yang Dikembalikan</th>
                        <th class="px-4 py-3.5 text-center">Tgl Pinjam / Kembali</th>
                        <th class="px-4 py-3.5 text-center">Terlambat</th>
                        <th class="px-4 py-3.5 text-center">Denda</th>
                        <th class="px-4 py-3.5">Petugas &amp; Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                <i data-lucide="folder-x" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2"></i>
                                Belum ada riwayat pengembalian buku.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md text-[11px] border border-slate-200/80 dark:border-slate-700"><?= esc($r['kode_transaksi']) ?></span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs"><?= esc($r['nama_anggota']) ?></div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($r['nomor_anggota']) ?></div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-12 rounded border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 flex items-center justify-center">
                                            <?php if (!empty($r['cover_buku']) && file_exists(FCPATH . $r['cover_buku'])): ?>
                                                <img src="<?= base_url(esc($r['cover_buku'])) ?>" alt="Cover" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i data-lucide="book" class="w-4 h-4 text-slate-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 truncate text-xs" title="<?= esc($r['judul_buku']) ?>"><?= esc($r['judul_buku']) ?></div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5"><?= esc($r['kode_buku']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap text-center text-xs">
                                    <div>Pinjam: <?= date('d/m/Y', strtotime($r['tanggal_pinjam'])) ?></div>
                                    <div class="font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5">Kembali: <?= date('d/m/Y', strtotime($r['tanggal_kembali'])) ?></div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if ($r['jumlah_hari_terlambat'] > 0): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <?= $r['jumlah_hari_terlambat'] ?> Hari
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            0 Hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono font-semibold whitespace-nowrap">
                                    <?php if ($r['denda'] > 0): ?>
                                        <span class="text-rose-600 dark:text-rose-400 text-xs">Rp <?= number_format($r['denda'], 0, ',', '.') ?></span>
                                        <?php if (($r['status_denda'] ?? 'lunas') === 'belum_lunas'): ?>
                                            <div class="mt-1 flex flex-col items-center gap-1 font-sans">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Belum Lunas</span>
                                                <form action="<?= site_url('/pengembalian/lunasi-denda/' . $r['id']) ?>" method="POST" onsubmit="return confirm('Konfirmasi pelunasan denda sebesar Rp <?= number_format($r['denda'], 0, ',', '.') ?> dari siswa?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white text-[10px] font-semibold transition-colors cursor-pointer">
                                                        <i data-lucide="check" class="w-3 h-3"></i>
                                                        <span>Tandai Lunas</span>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-0.5">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold font-sans bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Lunas</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400 dark:text-slate-500 text-xs">Rp 0</span>
                                        <div class="text-[10px] text-slate-400 font-sans">-</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300 max-w-xs">
                                    <div class="text-slate-800 dark:text-slate-100 font-medium"><?= esc($r['nama_admin'] ?: 'Admin') ?></div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 italic truncate"><?= esc($r['catatan'] ?: 'Tanpa catatan khusus') ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pop-up Filter Riwayat Pengembalian -->
<div id="modalFilterRiwayatPengembalian" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-amber-300"></i>
                <h4 class="font-serif font-bold text-sm">Filter Riwayat Pengembalian</h4>
            </div>
            <button onclick="closeModal('modalFilterRiwayatPengembalian')" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="<?= site_url('/pengembalian/riwayat') ?>" method="GET" class="p-5 space-y-3.5 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Kode transaksi, nama peminjam, judul..."
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?= esc($startDate ?? '') ?>"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?= esc($endDate ?? '') ?>"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/pengembalian/riwayat') ?>" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors text-center">
                    Reset
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modalFilterRiwayatPengembalian')" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
