<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="shield-alert" class="w-5 h-5 text-amber-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Audit Log & Jejak Keamanan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rekam jejak seluruh aksi sensitif: modifikasi peran, reset kata sandi, dan penghapusan data</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/60">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                <span>Audit Trail Aktif</span>
            </span>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Jejak Aksi</span>
                <h4 class="text-xl font-bold text-slate-900 dark:text-white font-mono"><?= $stats['total'] ?> Log</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0">
                <i data-lucide="user-cog" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Perubahan Role</span>
                <h4 class="text-xl font-bold text-amber-700 dark:text-amber-400 font-mono"><?= $stats['role'] ?> Aksi</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center shrink-0">
                <i data-lucide="key" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Reset Password</span>
                <h4 class="text-xl font-bold text-indigo-700 dark:text-indigo-400 font-mono"><?= $stats['reset'] ?> Kali</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 flex items-center justify-center shrink-0">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Penghapusan Data</span>
                <h4 class="text-xl font-bold text-rose-700 dark:text-rose-400 font-mono"><?= $stats['hapus'] ?> Aksi</h4>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="<?= site_url('/audit-log') ?>" method="GET" class="flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari username, keterangan, atau IP address..."
                       class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:outline-none">
            </div>

            <select name="action" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:outline-none">
                <option value="">Semua Tipe Aksi</option>
                <option value="UBAH_ROLE" <?= ($selectedAction ?? '') === 'UBAH_ROLE' ? 'selected' : '' ?>>Ubah Role Staf</option>
                <option value="RESET_PASSWORD" <?= ($selectedAction ?? '') === 'RESET_PASSWORD' ? 'selected' : '' ?>>Reset Password</option>
                <option value="HAPUS_STAF" <?= ($selectedAction ?? '') === 'HAPUS_STAF' ? 'selected' : '' ?>>Hapus Staf</option>
                <option value="HAPUS_BUKU" <?= ($selectedAction ?? '') === 'HAPUS_BUKU' ? 'selected' : '' ?>>Hapus Buku</option>
                <option value="HAPUS_ANGGOTA" <?= ($selectedAction ?? '') === 'HAPUS_ANGGOTA' ? 'selected' : '' ?>>Hapus Anggota</option>
                <option value="HAPUS_KATEGORI" <?= ($selectedAction ?? '') === 'HAPUS_KATEGORI' ? 'selected' : '' ?>>Hapus Kategori</option>
                <option value="UBAH_PENGATURAN" <?= ($selectedAction ?? '') === 'UBAH_PENGATURAN' ? 'selected' : '' ?>>Ubah Pengaturan</option>
                <option value="TAMBAH_STAF" <?= ($selectedAction ?? '') === 'TAMBAH_STAF' ? 'selected' : '' ?>>Tambah Staf</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-lg text-xs font-semibold shadow-xs transition-colors flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan</span>
            </button>

            <?php if (!empty($keyword) || !empty($selectedAction) || !empty($dateStart)): ?>
                <a href="<?= site_url('/audit-log') ?>" class="px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg text-xs font-semibold transition-colors">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Waktu (WIB)</th>
                        <th class="py-3.5 px-4">Pengguna / Pelaku</th>
                        <th class="py-3.5 px-4">Tipe Aksi</th>
                        <th class="py-3.5 px-4">Rincian Deskripsi</th>
                        <th class="py-3.5 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i data-lucide="shield" class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600"></i>
                                <p class="text-sm font-medium">Belum ada catatan aktivitas audit yang tercatat.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <div class="font-bold text-slate-700 dark:text-slate-300"><?= date('d M Y', strtotime($log['created_at'])) ?></div>
                                    <div><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">@<?= esc($log['username'] ?? 'sistem') ?></div>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider <?= ($log['role'] === 'admin') ? 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                                        <?= esc($log['role'] ?? 'user') ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <?php
                                    $action = $log['action'];
                                    $badgeClass = 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200';
                                    if (str_contains($action, 'ROLE')) {
                                        $badgeClass = 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/60';
                                    } elseif (str_contains($action, 'PASSWORD')) {
                                        $badgeClass = 'bg-indigo-50 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60';
                                    } elseif (str_contains($action, 'HAPUS')) {
                                        $badgeClass = 'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200 dark:border-rose-800/60';
                                    } elseif (str_contains($action, 'TAMBAH')) {
                                        $badgeClass = 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60';
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border <?= $badgeClass ?>">
                                        <?= esc($action) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-normal leading-relaxed">
                                    <?= esc($log['description']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <?= esc($log['ip_address'] ?? '-') ?>
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
