<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-terracotta-700 dark:bg-terracotta-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-amber-200"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Buku Sirkulasi Peminjaman</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan sirkulasi peminjaman buku pelajaran & dongeng ramah anak, batas tempo 5 hari, dan kuota 2 buku</p>
            </div>
        </div>
        <button onclick="openModal('modalPinjamBaru')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-terracotta-700 hover:bg-terracotta-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="bookmark-plus" class="w-4 h-4"></i>
            <span>Pinjam Buku Siswa</span>
        </button>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Quick Search Form -->
        <form action="<?= site_url('/peminjaman') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari kode transaksi, siswa, kelas, judul buku..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-terracotta-500 focus:border-navy-900 focus:outline-none transition-colors">
            <?php if (!empty($selectedStat) && $selectedStat !== 'aktif'): ?>
                <input type="hidden" name="status" value="<?= esc($selectedStat) ?>">
            <?php endif; ?>
            <?php if (!empty($startDate)): ?>
                <input type="hidden" name="start_date" value="<?= esc($startDate) ?>">
            <?php endif; ?>
            <?php if (!empty($endDate)): ?>
                <input type="hidden" name="end_date" value="<?= esc($endDate) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <!-- Modern Filter Pop-up Button -->
            <button type="button" onclick="openModal('modalFilterPeminjaman')" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-navy-600 dark:text-sky-400"></i>
                <span>Filter Data</span>
                <?php if (!empty($activeFilterCount)): ?>
                    <span class="w-5 h-5 rounded-full bg-navy-900 dark:bg-amber-600 text-white text-[10px] flex items-center justify-center font-bold">
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
                <?php if (!empty($selectedStat) && $selectedStat !== 'aktif'): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Status: <strong><?= ucfirst(esc($selectedStat)) ?></strong>
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
            <a href="<?= site_url('/peminjaman') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                <span>Reset Filter</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Table of Loans (FR-17) -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-32 text-center">Kode Transaksi</th>
                        <th class="px-4 py-3.5">Data Peminjam</th>
                        <th class="px-4 py-3.5">Buku Yang Dipinjam &amp; Sampul</th>
                        <th class="px-4 py-3.5 text-center">Tgl Pinjam</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($peminjaman)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-[#C1613A] dark:text-[#E07A5F] shadow-2xs border border-slate-200/80 dark:border-slate-700/80">
                                        <i data-lucide="book-marked" class="w-6 h-6 stroke-[1.75]"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h5 class="font-serif font-bold text-sm text-[#1B2A4A] dark:text-slate-200">Tidak Ada Transaksi Peminjaman</h5>
                                        <p class="text-xs text-[#5C6470] dark:text-slate-400">Belum ada transaksi pada kriteria filter ini, atau belum ada peminjaman buku yang tercatat.</p>
                                    </div>
                                    <button type="button" onclick="bukaModalPinjam()" class="btn-touch px-3.5 py-1.5 bg-[#1B2A4A] hover:bg-[#243B53] text-white text-xs font-semibold rounded-lg shadow-xs transition-colors gap-1.5 border border-[#1B2A4A]">
                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-amber-300"></i>
                                        <span>+ Pinjam Buku Baru</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($peminjaman as $p): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md text-[11px] border border-slate-200/80 dark:border-slate-700"><?= esc($p['kode_transaksi']) ?></span>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Oleh: <?= esc($p['nama_admin'] ?: 'Admin') ?></div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-semibold text-slate-900 dark:text-slate-100 text-xs"><?= esc($p['nama_anggota']) ?></span>
                                        <?php if (!empty($p['kelas_anggota'])): ?>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700"><?= esc($p['kelas_anggota']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5"><?= esc($p['nomor_anggota']) ?> &bull; <?= esc($p['kontak_anggota']) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-sm">
                                    <div class="flex items-center gap-2.5">
                                        <!-- Thumbnail Cover Buku Yang Dipinjam -->
                                        <div class="w-9 h-12 shrink-0 rounded border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                            <?php if (!empty($p['cover_buku'])): ?>
                                                <img src="<?= base_url(esc($p['cover_buku'])) ?>" alt="<?= esc($p['judul_buku']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i data-lucide="book" class="w-4 h-4 text-slate-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs leading-snug line-clamp-2" title="<?= esc($p['judul_buku']) ?>"><?= esc($p['judul_buku']) ?></div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                                                <span class="font-bold text-navy-900 dark:text-sky-300"><?= esc($p['kode_buku']) ?></span>
                                                <span>&bull;</span>
                                                <span><?= esc($p['lokasi_rak'] ?: 'Rak Umum') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap text-center text-xs">
                                    <?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap text-center text-xs">
                                    <div class="font-semibold"><?= date('d/m/Y', strtotime($p['tanggal_jatuh_tempo'])) ?></div>
                                    <?php
                                        $isPast = (strtotime(date('Y-m-d')) > strtotime($p['tanggal_jatuh_tempo']));
                                        if ($p['status'] !== 'dikembalikan' && $isPast) {
                                            $daysLate = (int)ceil((strtotime(date('Y-m-d')) - strtotime($p['tanggal_jatuh_tempo'])) / 86400);
                                            echo "<span class='text-[10px] text-red-600 dark:text-red-400 font-bold block'>Telat $daysLate hari</span>";
                                        }
                                    ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if ($p['status'] === 'dipinjam'): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">Dipinjam</span>
                                    <?php elseif ($p['status'] === 'terlambat'): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">Terlambat</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">Dikembalikan</span>
                                    <?php endif; ?>

                                    <?php if (!empty($p['jumlah_perpanjangan']) && $p['jumlah_perpanjangan'] > 0): ?>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.2 rounded border border-amber-200 dark:border-amber-800">
                                                <i data-lucide="rotate-cw" class="w-2.5 h-2.5"></i>
                                                <?= $p['jumlah_perpanjangan'] ?>x Diperpanjang
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="<?= site_url('/peminjaman/detail/' . $p['id']) ?>" class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" title="Lihat Bukti Struk">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <?php if ($p['status'] === 'dipinjam' && (int)($p['jumlah_perpanjangan'] ?? 0) < 2): ?>
                                            <button type="button" onclick="perpanjangPinjaman(<?= $p['id'] ?>, '<?= esc($p['kode_transaksi']) ?>', '<?= esc(addslashes($p['nama_anggota'])) ?>', '<?= esc(addslashes($p['judul_buku'])) ?>')" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 rounded-lg text-[11px] font-semibold transition-colors" 
                                                    title="Perpanjang Masa Pinjam">
                                                <i data-lucide="rotate-cw" class="w-3 h-3 text-amber-700 dark:text-amber-400"></i>
                                                <span>Perpanjang</span>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($p['status'] !== 'dikembalikan'): ?>
                                            <a href="<?= site_url('/pengembalian?q=' . urlencode($p['kode_transaksi'])) ?>" class="inline-flex items-center gap-1 px-2.5 py-1 bg-navy-900 hover:bg-navy-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-lg text-[11px] font-medium transition-colors">
                                                <i data-lucide="corner-down-left" class="w-3 h-3"></i>
                                                <span>Kembalikan</span>
                                            </a>
                                        <?php endif; ?>
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

<!-- Modal Pop-up Filter Peminjaman -->
<div id="modalFilterPeminjaman" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Filter Transaksi Peminjaman</h4>
                    <p class="text-[11px] text-slate-300">Saring riwayat sirkulasi peminjaman buku</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalFilterPeminjaman')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="<?= site_url('/peminjaman') ?>" method="GET" class="p-5 space-y-3.5 text-xs">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Cari Kata Kunci</span>
                </label>
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Kode transaksi, nama siswa, atau judul..."
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Status Transaksi</span>
                </label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    <option value="aktif" <?= ($selectedStat ?? '') === 'aktif' ? 'selected' : '' ?>>Pinjaman Aktif (Dipinjam &amp; Terlambat)</option>
                    <option value="dipinjam" <?= ($selectedStat ?? '') === 'dipinjam' ? 'selected' : '' ?>>Dipinjam (Belum Jatuh Tempo)</option>
                    <option value="terlambat" <?= ($selectedStat ?? '') === 'terlambat' ? 'selected' : '' ?>>Terlambat (Lewat Jatuh Tempo)</option>
                    <option value="dikembalikan" <?= ($selectedStat ?? '') === 'dikembalikan' ? 'selected' : '' ?>>Sudah Dikembalikan</option>
                    <option value="semua" <?= ($selectedStat ?? '') === 'semua' ? 'selected' : '' ?>>Semua Status Transaksi</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Dari Tanggal</span>
                    </label>
                    <input type="date" name="start_date" value="<?= esc($startDate ?? '') ?>"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Sampai Tanggal</span>
                    </label>
                    <input type="date" name="end_date" value="<?= esc($endDate ?? '') ?>"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/peminjaman') ?>" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors text-center">
                    Reset
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modalFilterPeminjaman')" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
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

<!-- Modal Catat Peminjaman Baru (FR-14, FR-15, FR-16) -->
<div id="modalPinjamBaru" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="book-up" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Input Transaksi Peminjaman Baru</h4>
                    <p class="text-[11px] text-slate-300">Pilih anggota peminjam dan buku yang dipinjam</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalPinjamBaru')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formPinjamBaru" action="<?= site_url('/peminjaman/store') ?>" method="POST" class="p-5 space-y-3.5 overflow-y-auto text-xs">
            <?= csrf_field() ?>

            <!-- Rule Info Alert -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-navy-700 dark:text-sky-400 shrink-0"></i>
                <span>Ketentuan: Maksimal <strong><?= $maxPinjam ?> buku</strong> dipinjam per anggota. Stok buku berkurang otomatis.</span>
            </div>

            <!-- Quick Barcode Scanner Input (Scanner Tembak) -->
            <div class="p-3 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-lg space-y-1">
                <label class="block text-[11px] font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="scan-line" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Scan Barcode Cepat (Kartu Anggota / Buku)</span>
                </label>
                <div class="relative">
                    <input type="text" id="quickScanPinjamInput" placeholder="Arahkan scanner tembak ke barcode kartu anggota atau buku..." 
                           class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 text-xs font-mono placeholder:font-sans focus:outline-none focus:ring-1 focus:ring-navy-900 text-slate-800 dark:text-white">
                    <i data-lucide="barcode" class="w-3.5 h-3.5 text-amber-600 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                </div>
                <div id="quickScanFeedback" class="text-[11px] font-medium hidden"></div>
            </div>

            <!-- Pilih Anggota -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Pilih Anggota Peminjam *</span>
                </label>
                <select name="anggota_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    <option value="">-- Cari / Pilih Anggota --</option>
                    <?php foreach ($activeMembers as $m): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= esc($m['nomor_anggota']) ?> - <?= esc($m['nama']) ?> (<?= esc($m['no_identitas']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pilih Buku -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Pilih Buku Koleksi * (Stok tersedia)</span>
                </label>
                <select name="buku_id" id="select_buku_pinjam" onchange="onSelectBuku(this.value)" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    <option value="">-- Cari / Pilih Judul Buku --</option>
                    <?php foreach ($availableBooks as $bk): ?>
                        <option value="<?= $bk['id'] ?>">
                            <?= esc($bk['kode_buku']) ?> - <?= esc($bk['judul']) ?> [Tersedia: <?= $bk['stok_tersedia'] ?>]
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Live Preview Cover Buku Terpilih -->
            <div id="selected_buku_preview" class="hidden p-3 rounded-lg bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <img id="preview_buku_cover" src="" alt="Cover" class="w-10 h-14 object-cover rounded border border-slate-300 dark:border-slate-600 shrink-0">
                <div class="space-y-0.5 text-xs">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Verifikasi Sampul Buku</span>
                    <div id="preview_buku_judul" class="font-bold text-slate-900 dark:text-white leading-snug"></div>
                    <div id="preview_buku_meta" class="text-[11px] text-slate-500 dark:text-slate-400 font-mono"></div>
                </div>
            </div>

            <!-- Tanggal Pinjam & Jatuh Tempo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Tanggal Pinjam *</span>
                    </label>
                    <input type="date" id="tgl_pinjam" name="tanggal_pinjam" value="<?= $defaultTglPinjam ?>" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none"
                           onchange="updateDueDate()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Tanggal Jatuh Tempo *</span>
                    </label>
                    <input type="date" id="tgl_tempo" name="tanggal_jatuh_tempo" value="<?= $defaultTglTempo ?>" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Catatan Peminjaman (Opsional)</span>
                </label>
                <textarea name="catatan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none" placeholder="Catatan kondisi buku atau keperluan pinjam..."></textarea>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modalPinjamBaru')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewPinjamBaru()" class="px-4 py-2 text-xs font-semibold bg-terracotta-700 hover:bg-terracotta-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <span>Lanjutkan & Review Data</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Review Data Transaksi Peminjaman Sebelum Simpan -->
<div id="modalReviewPinjam" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded bg-white/10 text-amber-300 flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm">Review Data Transaksi Peminjaman</h4>
                    <p class="text-[10px] text-slate-300">Pastikan data siswa &amp; buku sesuai sebelum disimpan resmi</p>
                </div>
            </div>
            <button onclick="closeModal('modalReviewPinjam')" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5 text-xs">
            <!-- Box Data Siswa -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 space-y-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Anggota Peminjam</span>
                </div>
                <div class="font-bold text-slate-900 dark:text-white text-sm" id="rev_nama_anggota">-</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono" id="rev_identitas_anggota">-</div>
            </div>

            <!-- Box Data Buku & Cover -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-10 h-14 rounded overflow-hidden bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shrink-0 flex items-center justify-center">
                    <img id="rev_cover_buku" src="" alt="Cover" class="w-full h-full object-cover hidden">
                    <div id="rev_cover_buku_fallback" class="text-slate-400">
                        <i data-lucide="book" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="space-y-0.5 min-w-0 flex-1">
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Buku Yang Dipinjam</div>
                    <div class="font-bold text-slate-900 dark:text-white text-xs truncate" id="rev_judul_buku">-</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono" id="rev_meta_buku">-</div>
                </div>
            </div>

            <!-- Box Jadwal & Durasi -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700">
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-medium">Tanggal Pinjam</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs font-mono" id="rev_tgl_pinjam">-</span>
                </div>
                <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50">
                    <span class="text-[10px] text-amber-800 dark:text-amber-300 uppercase tracking-wider block font-bold">Jatuh Tempo</span>
                    <span class="font-bold text-amber-900 dark:text-amber-200 text-xs font-mono" id="rev_tgl_tempo">-</span>
                </div>
            </div>

            <!-- Box Catatan -->
            <div id="rev_box_catatan" class="hidden p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold mb-0.5">Catatan Khusus:</span>
                <p id="rev_catatan" class="italic text-[11px]"></p>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalReviewPinjam')" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Kembali & Ubah
                </button>
                <button type="button" onclick="document.getElementById('formPinjamBaru').submit()" class="px-4 py-2 font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Konfirmasi & Catat Pinjaman</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const availableBooksData = <?= json_encode($availableBooks) ?>;

    function onSelectBuku(bukuId) {
        const previewBox = document.getElementById('selected_buku_preview');
        if (!bukuId) {
            previewBox.classList.add('hidden');
            return;
        }

        const b = availableBooksData.find(x => x.id == bukuId);
        if (b) {
            previewBox.classList.remove('hidden');
            document.getElementById('preview_buku_judul').textContent = b.judul;
            document.getElementById('preview_buku_meta').textContent = (b.kode_buku || '') + ' • Penulis: ' + (b.penulis || '-') + ' • ' + (b.lokasi_rak || 'Rak Umum');
            
            const coverImg = document.getElementById('preview_buku_cover');
            if (b.cover) {
                coverImg.src = '<?= base_url() ?>/' + b.cover;
                coverImg.classList.remove('hidden');
            } else {
                coverImg.classList.add('hidden');
            }
        } else {
            previewBox.classList.add('hidden');
        }
    }

    function updateDueDate() {
        const pinjamVal = document.getElementById('tgl_pinjam').value;
        if (pinjamVal) {
            const d = new Date(pinjamVal);
            d.setDate(d.getDate() + 7); // Default 7 hari
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            document.getElementById('tgl_tempo').value = `${yyyy}-${mm}-${dd}`;
        }
    }

    function reviewPinjamBaru() {
        const form = document.getElementById('formPinjamBaru');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const selectAnggota = form.querySelector('select[name="anggota_id"]');
        const selectedAnggotaText = selectAnggota.options[selectAnggota.selectedIndex].text;
        const parts = selectedAnggotaText.split(' - ');
        document.getElementById('rev_nama_anggota').textContent = parts[1] ? parts[1].split(' (')[0] : selectedAnggotaText;
        document.getElementById('rev_identitas_anggota').textContent = selectedAnggotaText;

        const selectBuku = document.getElementById('select_buku_pinjam');
        const bukuId = selectBuku.value;
        const b = availableBooksData.find(x => x.id == bukuId);
        if (b) {
            document.getElementById('rev_judul_buku').textContent = b.judul;
            document.getElementById('rev_meta_buku').textContent = (b.kode_buku || '') + ' • Rak: ' + (b.lokasi_rak || 'Umum') + ' • Penulis: ' + (b.penulis || '-');
            const revCover = document.getElementById('rev_cover_buku');
            const revFallback = document.getElementById('rev_cover_buku_fallback');
            if (b.cover) {
                revCover.src = '<?= base_url() ?>/' + b.cover;
                revCover.classList.remove('hidden');
                revFallback.classList.add('hidden');
            } else {
                revCover.classList.add('hidden');
                revFallback.classList.remove('hidden');
            }
        }

        const tglPinjam = document.getElementById('tgl_pinjam').value;
        const tglTempo = document.getElementById('tgl_tempo').value;
        document.getElementById('rev_tgl_pinjam').textContent = tglPinjam;
        document.getElementById('rev_tgl_tempo').textContent = tglTempo;

        const catatanVal = form.querySelector('textarea[name="catatan"]').value.trim();
        const boxCatatan = document.getElementById('rev_box_catatan');
        if (catatanVal) {
            boxCatatan.classList.remove('hidden');
            document.getElementById('rev_catatan').textContent = catatanVal;
        } else {
            boxCatatan.classList.add('hidden');
        }

        openModal('modalReviewPinjam');
        if (window.lucide) lucide.createIcons();
    }

    function perpanjangPinjaman(id, kode, nama, judul) {
        if (window.Swal) {
            Swal.fire({
                title: 'Perpanjang Masa Pinjam?',
                html: `Perpanjang masa peminjaman buku <strong>"${judul}"</strong> untuk <strong>${nama}</strong>?<br><br><span class="text-xs text-slate-500">Masa jatuh tempo akan otomatis diperpanjang sesuai durasi default.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1B2A4A',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Perpanjang Pinjaman',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 shadow-xl',
                    confirmButton: 'rounded-lg px-4 py-2 font-semibold',
                    cancelButton: 'rounded-lg px-4 py-2 font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= site_url('/peminjaman/perpanjang/') ?>' + id;
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '<?= csrf_token() ?>';
                    csrfInput.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        } else if (confirm(`Perpanjang peminjaman buku "${judul}" untuk ${nama}?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= site_url('/peminjaman/perpanjang/') ?>' + id;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Quick Barcode Scanner Handler di Modal Pinjam Baru
    document.addEventListener('DOMContentLoaded', () => {
        const quickScanInput = document.getElementById('quickScanPinjamInput');
        if (quickScanInput) {
            quickScanInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = this.value.trim();
                    if (!code) return;
                    handleAdminPinjamScan(code);
                    this.value = '';
                }
            });
        }
    });

    function handleAdminPinjamScan(code) {
        const feedback = document.getElementById('quickScanFeedback');
        if (!feedback) return;
        feedback.classList.remove('hidden');

        const cleanCode = code.toLowerCase().replace(/[^a-z0-9]/g, '');

        // 1. Cek opsi Anggota
        const memberSelect = document.querySelector('select[name="anggota_id"]');
        let memberFound = false;
        if (memberSelect) {
            for (let i = 0; i < memberSelect.options.length; i++) {
                const optText = memberSelect.options[i].text.toLowerCase();
                const optClean = optText.replace(/[^a-z0-9]/g, '');
                if (optText.includes(code.toLowerCase()) || (cleanCode.length >= 4 && optClean.includes(cleanCode))) {
                    memberSelect.selectedIndex = i;
                    memberFound = true;
                    break;
                }
            }
        }

        // 2. Cek opsi Buku
        const bukuSelect = document.getElementById('select_buku_pinjam');
        let bukuFound = false;
        if (bukuSelect) {
            for (let i = 0; i < bukuSelect.options.length; i++) {
                const optText = bukuSelect.options[i].text.toLowerCase();
                const optClean = optText.replace(/[^a-z0-9]/g, '');
                if (optText.includes(code.toLowerCase()) || (cleanCode.length >= 4 && optClean.includes(cleanCode))) {
                    bukuSelect.selectedIndex = i;
                    onSelectBuku(bukuSelect.options[i].value);
                    bukuFound = true;
                    break;
                }
            }
        }

        if (memberFound) {
            feedback.className = "text-[11px] font-medium text-emerald-700 dark:text-emerald-400 mt-1";
            feedback.textContent = `✓ Kartu Anggota dipilih: ${memberSelect.options[memberSelect.selectedIndex].text}`;
        } else if (bukuFound) {
            feedback.className = "text-[11px] font-medium text-emerald-700 dark:text-emerald-400 mt-1";
            feedback.textContent = `✓ Buku dipilih: ${bukuSelect.options[bukuSelect.selectedIndex].text}`;
        } else {
            feedback.className = "text-[11px] font-medium text-rose-600 dark:text-rose-400 mt-1";
            feedback.textContent = `✕ Kode '${code}' tidak ditemukan di opsi Anggota maupun Buku aktif.`;
        }
    }
</script>

<?= $this->endSection() ?>
