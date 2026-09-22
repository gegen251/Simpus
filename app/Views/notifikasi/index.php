<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="message-square-text" class="w-5 h-5 text-emerald-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Pusat Notifikasi WhatsApp Sirkulasi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Kirim pengingat jatuh tempo ramah siswa dan pemberitahuan denda keterlambatan langsung ke nomor WhatsApp wali murid
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= site_url('/kiosk') ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                <i data-lucide="scan-line" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span>Layanan Mandiri Siswa</span>
            </a>
            <a href="<?= site_url('/peminjaman') ?>" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Sirkulasi Peminjaman</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Pinjaman Aktif -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pinjaman Aktif</span>
                <h4 class="font-bold text-xl text-slate-900 dark:text-white font-mono mt-0.5"><?= $totalAktif ?></h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Buku sedang dipinjam</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="book-open" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Terlambat (Overdue) -->
        <a href="<?= site_url('/notifikasi?filter=terlambat') ?>" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-rose-200 dark:border-rose-900/60 shadow-xs flex items-center justify-between hover:border-rose-300 transition-colors">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Pinjaman Terlambat</span>
                <h4 class="font-bold text-xl text-rose-600 dark:text-rose-400 font-mono mt-0.5"><?= $countTerlambat ?></h4>
                <p class="text-[11px] text-rose-500 mt-0.5">Denda sedang berjalan</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </a>

        <!-- Segera Jatuh Tempo (Hari Ini & Besok) -->
        <a href="<?= site_url('/notifikasi?filter=segera') ?>" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-amber-200 dark:border-amber-900/60 shadow-xs flex items-center justify-between hover:border-amber-300 transition-colors">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Jatuh Tempo Segera</span>
                <h4 class="font-bold text-xl text-amber-800 dark:text-amber-400 font-mono mt-0.5"><?= $countHariIni + $countH1 ?></h4>
                <p class="text-[11px] text-amber-700 mt-0.5">Hari ini (<?= $countHariIni ?>) &amp; Besok (<?= $countH1 ?>)</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-400 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </a>

        <!-- Normal (H-2 s/d H-3) -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Tenggat H-2 s/d H-3</span>
                <h4 class="font-bold text-xl text-emerald-800 dark:text-emerald-400 font-mono mt-0.5"><?= $countH2H3 ?></h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Masa pinjam masih aman</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-400 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Buttons Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-semibold">
            <a href="<?= site_url('/notifikasi?filter=semua') ?>"
               class="px-3.5 py-1.5 rounded-md transition-colors <?= $filter === 'semua' ? 'bg-white dark:bg-slate-900 text-navy-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' ?>">
                Semua Peminjaman (<?= $totalAktif ?>)
            </a>
            <a href="<?= site_url('/notifikasi?filter=terlambat') ?>"
               class="px-3.5 py-1.5 rounded-md transition-colors <?= $filter === 'terlambat' ? 'bg-white dark:bg-slate-900 text-rose-600 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' ?>">
                Hanya Terlambat (<?= $countTerlambat ?>)
            </a>
            <a href="<?= site_url('/notifikasi?filter=segera') ?>"
               class="px-3.5 py-1.5 rounded-md transition-colors <?= $filter === 'segera' ? 'bg-white dark:bg-slate-900 text-amber-700 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' ?>">
                Jatuh Tempo Segera (<?= $countHariIni + $countH1 ?>)
            </a>
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Integrasi Pesan: <strong>WhatsApp Web / App Langsung</strong></span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No</th>
                        <th class="px-4 py-3.5">Pemustaka / Wali Murid</th>
                        <th class="px-4 py-3.5">Buku yang Dipinjam</th>
                        <th class="px-4 py-3.5 text-center">Tgl Pinjam</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-center">Status Tenggat</th>
                        <th class="px-4 py-3.5 text-center">Estimasi Denda</th>
                        <th class="px-4 py-3.5 text-center w-40">Kirim WhatsApp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($loans)): ?>
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <i data-lucide="check-check" class="w-6 h-6 text-emerald-600"></i>
                                </div>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada peminjaman yang memerlukan notifikasi saat ini</p>
                                <p class="text-xs text-slate-400 mt-1">Seluruh sirkulasi dalam status aman atau filter tidak memiliki data.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($loans as $row): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <!-- No -->
                                <td class="px-4 py-3.5 text-center text-slate-400 font-mono text-xs">
                                    <?= $no++ ?>
                                </td>

                                <!-- Anggota -->
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs">
                                        <?= esc($row['nama_anggota']) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <span class="font-mono text-[10px] bg-slate-100 dark:bg-slate-800 px-1 py-0.2 rounded border border-slate-200 dark:border-slate-700">
                                            <?= esc($row['nomor_anggota']) ?>
                                        </span>
                                        <span>&bull;</span>
                                        <span><?= esc($row['kelas'] ?: 'Umum') ?></span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                        <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                                        <span><?= esc($row['kontak']) ?></span>
                                    </div>
                                </td>

                                <!-- Buku -->
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs line-clamp-1" title="<?= esc($row['judul']) ?>">
                                        <?= esc($row['judul']) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                        <span class="font-mono text-[10px] text-navy-900 dark:text-sky-300 font-bold">
                                            <?= esc($row['kode_buku']) ?>
                                        </span>
                                        <span>&bull;</span>
                                        <span class="font-mono text-[10px] text-slate-400">
                                            <?= esc($row['kode_transaksi']) ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Tgl Pinjam -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap text-slate-600 dark:text-slate-400 font-mono text-xs">
                                    <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>
                                </td>

                                <!-- Tgl Jatuh Tempo -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap font-mono text-xs font-semibold <?= $row['diff_days'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-200' ?>">
                                    <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $row['badge_class'] ?>">
                                        <?= esc($row['status_badge']) ?>
                                    </span>
                                </td>

                                <!-- Denda -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap font-mono">
                                    <?php if ($row['denda_est'] > 0): ?>
                                        <span class="font-bold text-rose-600 dark:text-rose-400 text-xs">
                                            Rp <?= number_format($row['denda_est'], 0, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Kirim WhatsApp -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <a href="<?= $row['wa_link'] ?>" target="_blank"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors"
                                       title="Buka WhatsApp dengan pesan pengingat terisi otomatis">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5 text-emerald-200"></i>
                                        <span>Kirim WA</span>
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
