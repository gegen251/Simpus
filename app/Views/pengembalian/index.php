<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-emerald-700 dark:bg-emerald-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="arrow-down-left" class="w-5 h-5 text-emerald-200"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Pengembalian Buku & Denda Siswa</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verifikasi pengembalian buku siswa, hitung denda edukatif Rp 500/hari otomatis, dan kembalikan stok fisik</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= site_url('/pengembalian') ?>" class="px-3.5 py-2 bg-navy-900 text-white text-xs font-semibold rounded-lg shadow-xs">
                Proses Pengembalian
            </a>
            <a href="<?= site_url('/pengembalian/riwayat') ?>" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                Riwayat Pengembalian
            </a>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Quick Search Form -->
        <form action="<?= site_url('/pengembalian') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari kode transaksi, nama siswa, judul buku..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-emerald-500 focus:border-navy-900 focus:outline-none transition-colors">
            <?php if (!empty($selectedStat) && $selectedStat !== 'aktif'): ?>
                <input type="hidden" name="status" value="<?= esc($selectedStat) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <!-- Modern Filter Pop-up Button -->
            <button type="button" onclick="openModal('modalFilterPengembalian')" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-navy-600 dark:text-sky-400"></i>
                <span>Filter Peminjaman</span>
                <?php if (!empty($activeFilterCount)): ?>
                    <span class="w-5 h-5 rounded-full bg-navy-900 dark:bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">
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
                        Status: <strong><?= $selectedStat === 'terlambat' ? 'Terlambat' : 'Dipinjam (Belum Tempo)' ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <a href="<?= site_url('/pengembalian') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                <span>Reset Filter</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Table of Active Loans Ready to Return -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs font-medium text-slate-700 dark:text-slate-300">
            <span>Daftar Peminjaman Aktif (<?= count($pinjamanAktif) ?> Transaksi)</span>
            <span class="text-slate-500 dark:text-slate-400">Tarif Denda: <strong>Rp <?= number_format($tarifDenda, 0, ',', '.') ?>/hari/buku</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 w-32 text-center">Kode Pinjam</th>
                        <th class="px-4 py-3.5">Peminjam</th>
                        <th class="px-4 py-3.5">Buku Yang Dipinjam &amp; Sampul</th>
                        <th class="px-4 py-3.5 text-center">Tgl Pinjam</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-center">Status / Telat</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($pinjamanAktif)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center mx-auto text-[#2F6E4E] dark:text-emerald-400 shadow-2xs border border-emerald-200/80 dark:border-emerald-800/80">
                                        <i data-lucide="check-check" class="w-6 h-6 stroke-[1.75]"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h5 class="font-serif font-bold text-sm text-[#1B2A4A] dark:text-slate-200">Seluruh Koleksi Tertib</h5>
                                        <p class="text-xs text-[#5C6470] dark:text-slate-400">Semua buku telah dikembalikan atau tidak ada transaksi peminjaman aktif yang menunggu pengembalian.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pinjamanAktif as $item): ?>
                            <?php
                                $tglTempo = strtotime($item['tanggal_jatuh_tempo']);
                                $tglNow   = strtotime(date('Y-m-d'));
                                $isLate   = ($tglNow > $tglTempo);
                                $lateDays = $isLate ? (int)ceil(($tglNow - $tglTempo) / 86400) : 0;
                                $estFine  = $lateDays * $tarifDenda;
                            ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md text-[11px] border border-slate-200/80 dark:border-slate-700"><?= esc($item['kode_transaksi']) ?></span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs"><?= esc($item['nama_anggota']) ?></div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($item['nomor_anggota']) ?> &bull; <?= esc($item['kontak_anggota']) ?></div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-12 rounded border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 flex items-center justify-center">
                                            <?php if (!empty($item['cover_buku']) && file_exists(FCPATH . $item['cover_buku'])): ?>
                                                <img src="<?= base_url(esc($item['cover_buku'])) ?>" alt="Cover" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i data-lucide="book" class="w-4 h-4 text-slate-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 truncate text-xs" title="<?= esc($item['judul_buku']) ?>"><?= esc($item['judul_buku']) ?></div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5"><?= esc($item['kode_buku']) ?> &bull; <?= esc($item['lokasi_rak'] ?: '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap text-center text-xs">
                                    <?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap text-center text-xs">
                                    <span class="font-semibold"><?= date('d/m/Y', strtotime($item['tanggal_jatuh_tempo'])) ?></span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if ($isLate): ?>
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                Telat <?= $lateDays ?> Hari
                                            </span>
                                            <span class="text-[10px] font-mono text-rose-600 dark:text-rose-400 font-bold mt-0.5">Denda: Rp <?= number_format($estFine, 0, ',', '.') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                            Tepat Waktu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <button onclick="prosesModalKembali(<?= htmlspecialchars(json_encode($item)) ?>, <?= $tarifDenda ?>)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-xs transition-colors">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span>Proses Kembali</span>
                                        </button>
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

<!-- Modal Pop-up Filter Pengembalian -->
<div id="modalFilterPengembalian" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Filter Peminjaman Siap Kembali</h4>
                    <p class="text-[10px] text-slate-300">Saring transaksi peminjaman aktif &amp; terlambat</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalFilterPengembalian')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="<?= site_url('/pengembalian') ?>" method="GET" class="p-5 space-y-3.5 text-xs">
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
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Status Keterlambatan</span>
                </label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-navy-900 focus:outline-none">
                    <option value="aktif" <?= ($selectedStat ?? '') === 'aktif' ? 'selected' : '' ?>>Semua Pinjaman Aktif (Dipinjam &amp; Terlambat)</option>
                    <option value="dipinjam" <?= ($selectedStat ?? '') === 'dipinjam' ? 'selected' : '' ?>>Belum Jatuh Tempo Saja</option>
                    <option value="terlambat" <?= ($selectedStat ?? '') === 'terlambat' ? 'selected' : '' ?>>Sudah Terlambat Saja</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/pengembalian') ?>" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors text-center">
                    Reset
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modalFilterPengembalian')" class="px-3.5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
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

<!-- Modal Konfirmasi Pengembalian Buku & Hitung Denda (FR-19, FR-20) -->
<div id="modalProsesKembali" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-emerald-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Konfirmasi Pengembalian Buku</h4>
                    <p class="text-[11px] text-slate-300">Periksa tanggal kembali, denda, dan kondisi buku</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalProsesKembali')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formProsesKembali" action="<?= site_url('/pengembalian/save') ?>" method="POST" class="p-5 space-y-3.5 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" id="kembali_peminjaman_id" name="peminjaman_id">
            <input type="hidden" id="kembali_tgl_tempo_val">
            <input type="hidden" id="kembali_tarif_denda_val">

            <!-- Detail Box -->
            <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 flex gap-3.5 items-center">
                <div class="w-12 h-16 rounded overflow-hidden bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 shrink-0 flex items-center justify-center">
                    <img id="kembali_cover_img" src="" alt="Cover Buku" class="w-full h-full object-cover hidden">
                    <div id="kembali_cover_icon" class="flex flex-col items-center justify-center text-slate-400">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">Kode Transaksi:</span>
                        <span id="label_kode_transaksi" class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-200/60 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[11px]"></span>
                    </div>
                    <div>
                        <span id="label_judul_buku" class="font-semibold text-slate-900 dark:text-slate-100 text-xs block truncate"></span>
                        <span id="label_nama_anggota" class="text-slate-500 dark:text-slate-400 text-[11px] block truncate"></span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200 dark:border-slate-700 pt-1">
                        <span class="text-slate-400 text-[10px]">Jatuh Tempo:</span>
                        <span id="label_tgl_tempo" class="font-mono font-bold text-amber-600 dark:text-amber-400"></span>
                    </div>
                </div>
            </div>

            <!-- Tanggal Pengembalian -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Tanggal Pengembalian (Default Hari Ini) *</span>
                </label>
                <input type="date" id="input_tanggal_kembali" name="tanggal_kembali" value="<?= $today ?>" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none"
                       onchange="hitungDendaRealtime()">
            </div>

            <!-- Kalkulasi Denda Preview Box -->
            <div id="boxDenda" class="p-3.5 rounded-lg border flex items-center justify-between">
                <div>
                    <span id="dendaTitle" class="text-xs font-bold block">Status Denda</span>
                    <span id="dendaDesc" class="text-[11px] text-slate-500 dark:text-slate-400">0 Hari Keterlambatan</span>
                </div>
                <div class="text-right">
                    <span id="dendaAmount" class="text-base font-bold font-mono">Rp 0</span>
                </div>
            </div>

            <!-- Catatan Pengembalian -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Catatan Pengembalian (Kondisi Buku)</span>
                </label>
                <textarea id="kembali_catatan" name="catatan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none" placeholder="Buku dalam kondisi baik dan lengkap..."></textarea>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modalProsesKembali')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewProsesKembali()" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <span>Lanjutkan & Review Data</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Review Data Pengembalian Buku Sebelum Simpan -->
<div id="modalReviewKembali" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded bg-white/10 text-emerald-300 flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm">Review Pengembalian Buku</h4>
                    <p class="text-[10px] text-slate-300">Verifikasi data pengembalian &amp; perhitungan denda</p>
                </div>
            </div>
            <button onclick="closeModal('modalReviewKembali')" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5 text-xs">
            <!-- Peminjam & Kode -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 space-y-1">
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-slate-400 font-medium">Kode Transaksi:</span>
                    <span id="rev_kembali_kode" class="font-mono font-bold text-navy-900 dark:text-sky-300 bg-white dark:bg-slate-700 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600"></span>
                </div>
                <div class="font-bold text-slate-900 dark:text-white text-sm pt-0.5" id="rev_kembali_anggota"></div>
            </div>

            <!-- Buku Yang Dikembalikan -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-10 h-14 rounded overflow-hidden bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shrink-0 flex items-center justify-center">
                    <img id="rev_kembali_cover" src="" alt="Cover" class="w-full h-full object-cover hidden">
                    <div id="rev_kembali_cover_fallback" class="text-slate-400">
                        <i data-lucide="book" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
                <div class="space-y-0.5 min-w-0 flex-1">
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Buku Yang Dikembalikan</div>
                    <div class="font-bold text-slate-900 dark:text-white text-xs truncate" id="rev_kembali_judul"></div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Stok fisik akan bertambah 1 eksemplar</div>
                </div>
            </div>

            <!-- Tanggal Kembali & Status Denda -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700">
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-medium">Tanggal Kembali</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs font-mono" id="rev_kembali_tgl">-</span>
                </div>
                <div id="rev_kembali_box_denda" class="p-3 rounded-lg border">
                    <span class="text-[10px] uppercase tracking-wider block font-bold" id="rev_kembali_status_denda">-</span>
                    <span class="font-bold text-xs font-mono" id="rev_kembali_nominal_denda">-</span>
                </div>
            </div>

            <!-- Catatan Pengembalian -->
            <div id="rev_kembali_box_catatan" class="hidden p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold mb-0.5">Catatan Kondisi Buku:</span>
                <p id="rev_kembali_catatan" class="italic text-[11px]"></p>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalReviewKembali')" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Kembali & Ubah
                </button>
                <button type="button" onclick="document.getElementById('formProsesKembali').submit()" class="px-4 py-2 font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    <span>Konfirmasi & Selesaikan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentKembaliItem = null;

    function prosesModalKembali(item, tarifDenda) {
        currentKembaliItem = item;
        document.getElementById('kembali_peminjaman_id').value = item.id;
        document.getElementById('label_kode_transaksi').innerText = item.kode_transaksi;
        document.getElementById('label_nama_anggota').innerText = item.nama_anggota;
        document.getElementById('label_judul_buku').innerText = item.judul_buku;
        document.getElementById('label_tgl_tempo').innerText = item.tanggal_jatuh_tempo;
        
        document.getElementById('kembali_tgl_tempo_val').value = item.tanggal_jatuh_tempo;
        document.getElementById('kembali_tarif_denda_val').value = tarifDenda;

        const coverImg = document.getElementById('kembali_cover_img');
        const coverIcon = document.getElementById('kembali_cover_icon');
        if (item.cover_buku) {
            coverImg.src = '<?= base_url() ?>/' + item.cover_buku;
            coverImg.classList.remove('hidden');
            coverIcon.classList.add('hidden');
        } else {
            coverImg.classList.add('hidden');
            coverIcon.classList.remove('hidden');
        }

        hitungDendaRealtime();
        openModal('modalProsesKembali');
    }

    function hitungDendaRealtime() {
        const tglKembaliStr = document.getElementById('input_tanggal_kembali').value;
        const tglTempoStr = document.getElementById('kembali_tgl_tempo_val').value;
        const tarif = parseFloat(document.getElementById('kembali_tarif_denda_val').value) || 1000;

        const boxDenda = document.getElementById('boxDenda');
        const dendaTitle = document.getElementById('dendaTitle');
        const dendaDesc = document.getElementById('dendaDesc');
        const dendaAmount = document.getElementById('dendaAmount');

        if (!tglKembaliStr || !tglTempoStr) return;

        const dKembali = new Date(tglKembaliStr + 'T00:00:00');
        const dTempo   = new Date(tglTempoStr + 'T00:00:00');

        const diffTime = dKembali - dTempo;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 0) {
            const nominal = diffDays * tarif;
            boxDenda.className = 'p-3 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 text-red-900 dark:text-red-200 flex items-center justify-between';
            dendaTitle.innerText = 'Denda Keterlambatan Dikenakan';
            dendaDesc.innerText = `Terlambat ${diffDays} hari × Rp ${tarif.toLocaleString('id-ID')}/hari`;
            dendaAmount.innerText = `Rp ${nominal.toLocaleString('id-ID')}`;
            dendaAmount.className = 'text-base font-bold font-mono text-red-700 dark:text-red-400';
        } else {
            boxDenda.className = 'p-3 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 flex items-center justify-between';
            dendaTitle.innerText = 'Tepat Waktu';
            dendaDesc.innerText = 'Pengembalian tidak dikenakan denda keterlambatan';
            dendaAmount.innerText = 'Rp 0';
            dendaAmount.className = 'text-base font-bold font-mono text-emerald-700 dark:text-emerald-400';
        }
    }

    function reviewProsesKembali() {
        const form = document.getElementById('formProsesKembali');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (currentKembaliItem) {
            document.getElementById('rev_kembali_kode').textContent = currentKembaliItem.kode_transaksi;
            document.getElementById('rev_kembali_anggota').textContent = currentKembaliItem.nama_anggota + ' (' + currentKembaliItem.nomor_anggota + ')';
            document.getElementById('rev_kembali_judul').textContent = currentKembaliItem.judul_buku;

            const coverImg = document.getElementById('rev_kembali_cover');
            const coverFallback = document.getElementById('rev_kembali_cover_fallback');
            if (currentKembaliItem.cover_buku) {
                coverImg.src = '<?= base_url() ?>/' + currentKembaliItem.cover_buku;
                coverImg.classList.remove('hidden');
                coverFallback.classList.add('hidden');
            } else {
                coverImg.classList.add('hidden');
                coverFallback.classList.remove('hidden');
            }
        }

        const tglKembali = document.getElementById('input_tanggal_kembali').value;
        document.getElementById('rev_kembali_tgl').textContent = tglKembali;

        const dendaTitle = document.getElementById('dendaTitle').innerText;
        const dendaAmount = document.getElementById('dendaAmount').innerText;
        const revBoxDenda = document.getElementById('rev_kembali_box_denda');
        const revStatusDenda = document.getElementById('rev_kembali_status_denda');
        const revNominalDenda = document.getElementById('rev_kembali_nominal_denda');

        revStatusDenda.innerText = dendaTitle;
        revNominalDenda.innerText = dendaAmount;

        if (dendaAmount !== 'Rp 0') {
            revBoxDenda.className = 'p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-900 dark:text-red-200';
            revStatusDenda.className = 'text-[10px] uppercase tracking-wider block font-bold text-red-600 dark:text-red-400';
            revNominalDenda.className = 'font-bold text-sm font-mono text-red-700 dark:text-red-300';
        } else {
            revBoxDenda.className = 'p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200';
            revStatusDenda.className = 'text-[10px] uppercase tracking-wider block font-bold text-emerald-600 dark:text-emerald-400';
            revNominalDenda.className = 'font-bold text-sm font-mono text-emerald-700 dark:text-emerald-300';
        }

        const catatanVal = document.getElementById('kembali_catatan').value.trim();
        const boxCatatan = document.getElementById('rev_kembali_box_catatan');
        if (catatanVal) {
            boxCatatan.classList.remove('hidden');
            document.getElementById('rev_kembali_catatan').textContent = catatanVal;
        } else {
            boxCatatan.classList.add('hidden');
        }

        openModal('modalReviewKembali');
        if (window.lucide) lucide.createIcons();
    }
</script>

<?= $this->endSection() ?>
