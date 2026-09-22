<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Welcome Header Banner (Bookcloth Navy Institutional Ledger) -->
    <div class="rounded-xl bg-[#101B30] dark:bg-[#0C1424] text-white p-6 sm:p-7 border border-slate-800 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-start gap-3.5">
                <div class="w-12 h-12 rounded-lg bg-white/10 p-2 border border-white/15 shrink-0 hidden sm:flex items-center justify-center">
                    <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                </div>
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-800/90 text-[11px] font-semibold text-amber-400 border border-slate-700/60">
                        <span>Perpustakaan Ramah Anak & Literasi SD</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-serif font-bold text-white tracking-tight">
                        Selamat Datang di SIMPUS SD, <?= esc(session()->get('admin_nama') ?? 'Pustakawan SD') ?>!
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                        Sistem manajemen sirkulasi buku tematik kurikulum merdeka, dongeng nusantara, komik edukasi sains, dan rekapitulasi literasi siswa SDN 12 Sumbawa.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2.5 shrink-0">
                <a href="<?= site_url('/peminjaman') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-terracotta-500 hover:bg-terracotta-600 text-white font-semibold text-xs transition-colors shadow-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Catat Pinjam Siswa</span>
                </a>
                <a href="<?= site_url('/buku') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs border border-slate-700 transition-colors">
                    <i data-lucide="book-plus" class="w-4 h-4"></i>
                    <span>Tambah Koleksi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 5 KPI Stat Cards (Flat Ledger Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Judul & Eksemplar SD -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Koleksi Buku SD</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight font-sans"><?= number_format($totalBuku) ?></span>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Judul</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span><strong class="font-semibold text-slate-700 dark:text-slate-200"><?= number_format($totalEksemplar) ?></strong> fisik</span>
                <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <?= $totalStokTersedia ?> siap
                </span>
            </div>
        </div>

        <!-- 2. Total Siswa & Guru -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa & Guru</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-900/40">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight font-sans"><?= number_format($totalAnggota) ?></span>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Orang</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                <span>Anggota aktif terdaftar</span>
            </div>
        </div>

        <!-- 3. Peminjaman Aktif Siswa -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sedang Dibaca</span>
                    <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-100 dark:border-sky-900/40">
                        <i data-lucide="book-marked" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-sky-600 dark:text-sky-400 tracking-tight font-sans"><?= number_format($totalPinjamAktif) ?></span>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Buku</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                <span>Sirkulasi berjalan</span>
            </div>
        </div>

        <!-- 4. Jatuh Tempo / Pengembalian -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border <?= $totalTerlambat > 0 ? 'border-amber-300 dark:border-amber-700/80 bg-amber-50/20' : 'border-slate-200/90 dark:border-slate-800' ?> shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Batas Kembali</span>
                    <div class="w-8 h-8 rounded-xl <?= $totalTerlambat > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/60' ?> flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold <?= $totalTerlambat > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' ?> tracking-tight font-sans"><?= number_format($totalTerlambat) ?></span>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Buku</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs <?= $totalTerlambat > 0 ? 'text-amber-700 dark:text-amber-300 font-semibold' : 'text-slate-500 dark:text-slate-400' ?>">
                <span class="w-1.5 h-1.5 rounded-full <?= $totalTerlambat > 0 ? 'bg-amber-500' : 'bg-slate-400 dark:bg-slate-600' ?>"></span>
                <span><?= $totalTerlambat > 0 ? 'Perlu diingatkan' : 'Tertib tepat waktu' ?></span>
            </div>
        </div>

        <!-- 5. Total Kas Denda Ramah Anak -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kas Denda Siswa</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                        <i data-lucide="coins" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight font-sans truncate">Rp <?= number_format($totalDenda, 0, ',', '.') ?></span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Tarif Rp 500/hari</span>
            </div>
        </div>
    </div>

    <!-- Pojok Literasi Sekolah (GLS SD Negeri 12 Sumbawa) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200/80 dark:border-amber-800/70 flex items-center justify-center shrink-0 shadow-2xs">
                <i data-lucide="book-heart" class="w-5 h-5"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider">Gerakan 15 Menit Membaca</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pembiasaan membaca buku fabel dan cerita nusantara setiap pagi sebelum jam pertama untuk melatih imajinasi dan budi pekerti siswa.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200/80 dark:border-sky-800/70 flex items-center justify-center shrink-0 shadow-2xs">
                <i data-lucide="library" class="w-5 h-5"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider">Koleksi Kurikulum Merdeka</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Buku teks utama Kelas 1 sampai 6 tertata rapi di Rak Tematik untuk kemudahan peminjaman kelompok belajar kelas.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/70 flex items-center justify-center shrink-0 shadow-2xs">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider">Aturan Ramah Anak</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Batas pinjam 2 buku per siswa dengan durasi 5 hari. Denda Rp 500/hari dirancang mendidik tanggung jawab tanpa membebani.
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Section (FR-27) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">Tren Peminjaman Buku Siswa</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Statistik peminjaman buku pelajaran & cerita 6 bulan terakhir</p>
                </div>
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded text-xs font-medium border border-slate-200 dark:border-slate-700">Bulanan</span>
            </div>
            <div class="h-64">
                <canvas id="chartTrenPeminjaman"></canvas>
            </div>
        </div>

        <!-- Top 5 Popular Books Chart -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">Buku Paling Disukai</h3>
                    <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Koleksi yang paling sering dipinjam siswa</p>
                <div class="h-52 flex items-center justify-center">
                    <canvas id="chartBukuPopuler"></canvas>
                </div>
            </div>
            <div class="text-center pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 dark:text-slate-500">
                Diperbarui otomatis dari transaksi sirkulasi
            </div>
        </div>
    </div>

    <!-- Tables Grid: Overdue Warning & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Alert Table: Jatuh Tempo / Terlambat -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-slate-900 dark:text-white">Pengingat Batas Waktu</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Peminjaman yang mendekati atau melewati tempo</p>
                    </div>
                </div>
                <a href="<?= site_url('/pengembalian') ?>" class="text-xs font-semibold text-terracotta-500 hover:text-terracotta-600 flex items-center gap-1">
                    <span>Kelola</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-2.5">Peminjam</th>
                            <th class="px-4 py-2.5">Buku</th>
                            <th class="px-4 py-2.5">Jatuh Tempo</th>
                            <th class="px-4 py-2.5">Status</th>
                            <th class="px-4 py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($overdueLoans)): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                    <i data-lucide="check-circle-2" class="w-6 h-6 mx-auto text-emerald-500 mb-1.5"></i>
                                    Tidak ada pinjaman yang terlambat saat ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($overdueLoans as $row): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100"><?= esc($row['nama_anggota']) ?></div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($row['nomor_anggota']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 max-w-[170px] truncate text-slate-700 dark:text-slate-300" title="<?= esc($row['judul_buku']) ?>">
                                        <?= esc($row['judul_buku']) ?>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                        <?= date('d M Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if ($row['status'] === 'terlambat'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Terlambat</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">Dipinjam</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="<?= site_url('/notifikasi') ?>" class="p-1 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900 rounded border border-emerald-200 dark:border-emerald-800 transition-colors" title="Pengingat WhatsApp">
                                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                            </a>
                                            <a href="<?= site_url('/pengembalian') ?>" class="inline-flex items-center px-2 py-1 bg-navy-800 dark:bg-slate-800 hover:bg-navy-900 dark:hover:bg-slate-700 text-white rounded text-[11px] font-medium transition-colors">
                                                <span>Kembali</span>
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

        <!-- Table 2: Sirkulasi Terbaru -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                        <i data-lucide="history" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-slate-900 dark:text-white">Sirkulasi Terbaru</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Peminjaman buku siswa paling mutakhir</p>
                    </div>
                </div>
                <a href="<?= site_url('/peminjaman') ?>" class="text-xs font-semibold text-terracotta-500 hover:text-terracotta-600 flex items-center gap-1">
                    <span>Semua</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-2.5">Kode / Tanggal</th>
                            <th class="px-4 py-2.5">Peminjam</th>
                            <th class="px-4 py-2.5">Buku</th>
                            <th class="px-4 py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada transaksi peminjaman.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $tr): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200"><?= esc($tr['kode_transaksi']) ?></span>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500"><?= date('d M Y', strtotime($tr['tanggal_pinjam'])) ?></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-800 dark:text-slate-100"><?= esc($tr['nama_anggota']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 max-w-[170px] truncate text-slate-700 dark:text-slate-300" title="<?= esc($tr['judul_buku']) ?>">
                                        <?= esc($tr['judul_buku']) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if ($tr['status'] === 'dipinjam'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">Dipinjam</span>
                                        <?php elseif ($tr['status'] === 'terlambat'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Terlambat</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js initialization script with dynamic dark/light theme support -->
<script>
    let chartTren = null;
    let chartPopuler = null;

    function buildCharts() {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : '#E2E8F0';
        const textColor = isDark ? '#94A3B8' : '#64748B';
        const barColor = isDark ? '#38BDF8' : '#1B2A4A';

        // 1. Chart Tren Peminjaman
        const ctxTren = document.getElementById('chartTrenPeminjaman')?.getContext('2d');
        if (ctxTren) {
            if (chartTren) chartTren.destroy();
            chartTren = new Chart(ctxTren, {
                type: 'bar',
                data: {
                    labels: <?= $chartMonths ?>,
                    datasets: [{
                        label: 'Jumlah Buku Dipinjam',
                        data: <?= $chartLoanCounts ?>,
                        backgroundColor: barColor,
                        borderRadius: 4,
                        barThickness: 22,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1E293B' : '#101B30',
                            titleColor: '#FFFFFF',
                            bodyColor: '#E2E8F0',
                            padding: 8,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: textColor, precision: 0, font: { family: "'Plus Jakarta Sans'" } },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor, font: { family: "'Plus Jakarta Sans'" } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Chart Buku Terpopuler
        const ctxPopuler = document.getElementById('chartBukuPopuler')?.getContext('2d');
        if (ctxPopuler) {
            if (chartPopuler) chartPopuler.destroy();
            const labels = <?= $popularLabels ?>;
            const dataVal = <?= $popularData ?>;

            if (labels.length === 0) {
                labels.push('Belum ada data');
                dataVal.push(1);
            }

            chartPopuler = new Chart(ctxPopuler, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataVal,
                        backgroundColor: isDark 
                            ? ['#38BDF8', '#FB923C', '#34D399', '#FBBF24', '#94A3B8'] 
                            : ['#1B2A4A', '#C1613A', '#2F6E4E', '#B8791F', '#64748B'],
                        borderWidth: 2,
                        borderColor: isDark ? '#0F172A' : '#FFFFFF',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                boxWidth: 10,
                                font: { size: 10, family: "'Plus Jakarta Sans'" }
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1E293B' : '#101B30',
                            titleColor: '#FFFFFF',
                            bodyColor: '#E2E8F0',
                            padding: 8,
                            cornerRadius: 6
                        }
                    },
                    cutout: '68%'
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        buildCharts();
        window.addEventListener('themeChanged', () => {
            buildCharts();
        });
    });
</script>

<?= $this->endSection() ?>
