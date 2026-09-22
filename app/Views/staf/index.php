<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section with Logo Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="shield-check" class="w-5 h-5 text-amber-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Manajemen Akun Staf Pustaka</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola hak akses pengguna, pisahkan akun Administrator Utama dan Petugas Perpustakaan</p>
            </div>
        </div>
        <button onclick="openModal('modalTambahStaf')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-navy-900 hover:bg-navy-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="user-plus" class="w-4 h-4 text-amber-300"></i>
            <span>Tambah Akun Petugas</span>
        </button>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Akun Terdaftar</span>
                <h4 class="text-xl font-bold text-slate-900 dark:text-white font-mono"><?= $stats['total'] ?> Petugas</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0">
                <i data-lucide="shield" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Administrator Utama</span>
                <h4 class="text-xl font-bold text-amber-700 dark:text-amber-400 font-mono"><?= $stats['admin'] ?> Akun</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Staf Perpustakaan</span>
                <h4 class="text-xl font-bold text-slate-800 dark:text-slate-200 font-mono"><?= $stats['staf'] ?> Akun</h4>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Quick Search -->
        <form action="<?= site_url('/staf') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama staf, username, atau email..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none transition-colors">
            <?php if (!empty($selectedRole)): ?>
                <input type="hidden" name="role" value="<?= esc($selectedRole) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <!-- Modern Filter Button -->
            <button type="button" onclick="openModal('modalFilterStaf')" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-navy-600 dark:text-sky-400"></i>
                <span>Filter Data</span>
                <?php if (!empty($activeFilterCount)): ?>
                    <span class="w-5 h-5 rounded-full bg-navy-900 dark:bg-indigo-600 text-white text-[10px] flex items-center justify-center font-bold">
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
                <?php if (!empty($selectedRole)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Role: <strong><?= $selectedRole === 'admin' ? 'Administrator' : 'Staf Pustaka' ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2.5">
                <button onclick="openModal('modalFilterStaf')" class="text-[11px] font-semibold text-navy-800 dark:text-sky-400 hover:underline">
                    Ubah Filter
                </button>
                <span class="text-slate-300 dark:text-slate-700">|</span>
                <a href="<?= site_url('/staf') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                    Reset Filter
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table of Staff Members -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No</th>
                        <th class="px-4 py-3.5">Profil &amp; Nama Petugas</th>
                        <th class="px-4 py-3.5">Username &amp; Email</th>
                        <th class="px-4 py-3.5 text-center">Hak Akses / Peran</th>
                        <th class="px-4 py-3.5 text-center">Tanggal Dibuat</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($stafList)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <i data-lucide="user-x" class="w-6 h-6"></i>
                                </div>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada data staf yang sesuai</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($stafList as $s): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-mono text-xs">
                                    <?= $no++ ?>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg <?= $s['role'] === 'admin' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700' ?> flex items-center justify-center font-bold text-xs shrink-0">
                                            <?= strtoupper(substr($s['nama'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs flex items-center gap-2">
                                                <span><?= esc($s['nama']) ?></span>
                                                <?php if ($s['id'] == session()->get('admin_id')): ?>
                                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                        Anda (Aktif)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Petugas SIMPUS</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="font-mono text-slate-800 dark:text-slate-200 text-xs">@<?= esc($s['username']) ?></div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 flex items-center gap-1">
                                        <i data-lucide="mail" class="w-3 h-3"></i>
                                        <span><?= esc($s['email']) ?></span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if ($s['role'] === 'admin'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <i data-lucide="shield" class="w-3 h-3 text-amber-600"></i>
                                            Administrator
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            <i data-lucide="user-check" class="w-3 h-3 text-slate-500"></i>
                                            Staf Pustaka
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3.5 text-center text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap font-mono">
                                    <?= !empty($s['created_at']) ? date('d M Y, H:i', strtotime($s['created_at'])) : '-' ?>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Tombol Detail Profil (Khusus Administrator) -->
                                        <?php if (session()->get('admin_role') === 'admin'): ?>
                                            <button type="button" onclick='detailStaf(<?= json_encode($s) ?>)' 
                                                    class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                                    title="Lihat Detail Profil">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Tombol Reset Password Staf -->
                                            <button type="button" onclick='openResetPassword(<?= json_encode($s) ?>)' 
                                                    class="p-1.5 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-slate-400 dark:hover:text-emerald-400 dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                                    title="Reset Password Petugas">
                                                <i data-lucide="key" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Tombol Edit Staf -->
                                        <button type="button" onclick='editStaf(<?= json_encode($s) ?>)' 
                                                class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                                title="Edit Profil Petugas">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Tombol Hapus Staf -->
                                        <?php if ($s['id'] != session()->get('admin_id')): ?>
                                            <button type="button" 
                                                    onclick="confirmDelete('<?= site_url('/staf/delete/' . $s['id']) ?>', 'Apakah Anda yakin ingin menghapus akun staf <?= esc(addslashes($s['nama'])) ?>? Tindakan ini tidak dapat dibatalkan.')"
                                                    class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 dark:text-slate-400 dark:hover:text-rose-400 dark:hover:bg-slate-800 rounded-lg transition-colors" 
                                                    title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" disabled class="p-1.5 text-slate-300 dark:text-slate-600 rounded-lg cursor-not-allowed" title="Akun Anda sendiri tidak dapat dihapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
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

<!-- ========================================================================= -->
<!-- MODAL DETAIL AKUN STAF                                                    -->
<!-- ========================================================================= -->
<div id="modalDetailStaf" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        
        <!-- Modal Header -->
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Detail Profil Petugas</h4>
                    <p class="text-[11px] text-slate-300">Informasi akun login dan status keamanan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalDetailStaf')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-4">
            
            <!-- User Profile Summary Header -->
            <div class="flex items-center gap-3.5 p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                <div id="detail_avatar" class="w-12 h-12 rounded-lg bg-navy-900 text-white font-serif font-bold text-lg flex items-center justify-center shrink-0">
                    P
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 id="detail_nama" class="font-bold text-sm text-slate-900 dark:text-white truncate leading-tight">Nama Petugas</h4>
                        <span id="detail_role_badge" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-800 border border-slate-300 shrink-0">Staf</span>
                    </div>
                    <p id="detail_username_display" class="font-mono text-xs text-navy-800 dark:text-sky-400 font-semibold mt-0.5">@username</p>
                    <p id="detail_email_display" class="text-[11px] text-slate-500 dark:text-slate-400 truncate">email@sdn01.sch.id</p>
                </div>
            </div>

            <!-- Credentials / Security Status Box -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Keamanan &amp; Kredensial Login</span>
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3"></i> Bcrypt Hash Aman
                    </span>
                </div>

                <!-- Username Display -->
                <div class="flex items-center justify-between bg-white dark:bg-slate-800 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Username</span>
                        <span id="detail_username_val" class="font-mono font-bold text-slate-900 dark:text-white text-xs">admin</span>
                    </div>
                    <button type="button" onclick="copyText('detail_username_val', this)" class="p-1.5 text-slate-400 hover:text-navy-900 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Salin Username">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Info Password Encryption -->
                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-lg flex items-start gap-2 text-slate-600 dark:text-slate-400">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                    <p class="text-[11px] leading-relaxed text-amber-900 dark:text-amber-300">
                        Sandi tersimpan menggunakan enkripsi hash Bcrypt standar OWASP. Jika staf lupa sandi, gunakan tombol <strong>Reset Password</strong>.
                    </p>
                </div>
            </div>

            <!-- Detail Information Table -->
            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 px-3.5 py-1 text-xs">
                <div class="py-2 flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tingkat Hak Akses</span>
                    <span id="detail_role_desc" class="font-semibold text-slate-800 dark:text-slate-200">Akses Penuh Administrator</span>
                </div>
                <div class="py-2 flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Alamat Email</span>
                    <span id="detail_email_val" class="font-medium text-slate-800 dark:text-slate-200">admin@sdn01.sch.id</span>
                </div>
                <div class="py-2 flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tanggal Registrasi Akun</span>
                    <span id="detail_created_val" class="text-slate-700 dark:text-slate-300 font-mono text-[11px]">-</span>
                </div>
                <div class="py-2 flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Terakhir Diperbarui</span>
                    <span id="detail_updated_val" class="text-slate-700 dark:text-slate-300 font-mono text-[11px]">-</span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-2 flex items-center justify-between">
                <button type="button" onclick="closeModal('modalDetailStaf')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Tutup
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnResetFromDetail" onclick="openResetFromDetail()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        <span>Reset Password</span>
                    </button>
                    <button type="button" id="btnEditFromDetail" onclick="openEditFromDetail()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Ubah Profil</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL RESET PASSWORD STAF                                                 -->
<!-- ========================================================================= -->
<div id="modalResetPasswordStaf" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-emerald-300">
                    <i data-lucide="key" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Reset Password Petugas</h4>
                    <p class="text-[11px] text-slate-300">Buat kata sandi baru untuk akun petugas</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalResetPasswordStaf')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formResetPasswordStaf" action="" method="POST" class="p-5 space-y-3.5">
            <?= csrf_field() ?>

            <!-- Info Target Akun -->
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs">
                    <i data-lucide="user" class="w-4 h-4"></i>
                </div>
                <div>
                    <h5 id="reset_staf_nama" class="font-bold text-slate-900 dark:text-white text-xs">Nama Petugas</h5>
                    <span id="reset_staf_username" class="text-[11px] font-mono text-navy-800 dark:text-sky-400 font-semibold">@username</span>
                </div>
            </div>

            <!-- Input Password Baru -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input type="password" id="input_reset_password" name="password_baru" required minlength="6"
                           class="w-full pl-9 pr-9 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none"
                           placeholder="Minimal 6 karakter...">
                    <button type="button" onclick="toggleInputPassword('input_reset_password', this)" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Kata sandi akan otomatis di-hash dengan algoritma Bcrypt.</span>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalResetPasswordStaf')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors">Simpan Sandi Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POP-UP FILTER STAF                                                  -->
<!-- ========================================================================= -->
<div id="modalFilterStaf" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-slate-200">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Filter Akun Petugas</h4>
                    <p class="text-[11px] text-slate-300">Saring daftar staf perpustakaan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalFilterStaf')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/staf') ?>" method="GET" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kata Kunci (Nama/Username/Email)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Ketik kata kunci..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Hak Akses / Role</label>
                    <select name="role" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:outline-none">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= ($selectedRole ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="staf" <?= ($selectedRole ?? '') === 'staf' ? 'selected' : '' ?>>Staf Pustaka</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan Nama</label>
                    <select name="order" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:outline-none">
                        <option value="ASC" <?= ($order ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Nama A &rarr; Z</option>
                        <option value="DESC" <?= ($order ?? '') === 'DESC' ? 'selected' : '' ?>>Terbaru Dibuat</option>
                    </select>
                </div>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/staf') ?>" class="px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                    Reset Filter
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModal('modalFilterStaf')" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH STAF BARU                                                    -->
<!-- ========================================================================= -->
<div id="modalTambahStaf" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Tambah Akun Petugas Perpustakaan</h4>
                    <p class="text-[11px] text-slate-300">Buat kredensial akses untuk staf baru</p>
                </div>
            </div>
            <button onclick="closeModal('modalTambahStaf')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formTambahStaf" action="<?= site_url('/staf/store') ?>" method="POST" class="p-5 space-y-3.5 text-xs">
            <?= csrf_field() ?>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Nama Lengkap Petugas <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input type="text" id="tambah_nama" name="nama" required 
                           class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none placeholder:text-slate-400" 
                           placeholder="Contoh: Ibu Rina Kartika, S.Pd">
                </div>
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Username Login <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="at-sign" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="tambah_username" name="username" required pattern="[a-zA-Z0-9_]{4,}" 
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none placeholder:text-slate-400 font-mono" 
                               placeholder="rina_pustaka">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Min. 4 karakter alphanumeric</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" id="tambah_email" name="email" required 
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none placeholder:text-slate-400" 
                               placeholder="rina@sdn01.sch.id">
                    </div>
                </div>
            </div>

            <!-- Password & Role Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Kata Sandi (Password) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="tambah_password" name="password" required minlength="6" 
                               class="w-full pl-9 pr-9 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none placeholder:text-slate-400" 
                               placeholder="Minimal 6 karakter">
                        <button type="button" onclick="toggleInputPassword('tambah_password', this)" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Hak Akses / Role <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        <select id="tambah_role" name="role" required 
                                class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                            <option value="staf" selected>Staf Pustaka (Operasional Harian)</option>
                            <option value="admin">Administrator Utama (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notice / Hint Card -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 shrink-0 text-slate-500 mt-0.5"></i>
                <div class="leading-relaxed text-slate-600 dark:text-slate-400">
                    <strong class="text-slate-800 dark:text-slate-200">Perbedaan Peran:</strong>
                    <p class="mt-0.5">Staf Pustaka melayani peminjaman dan pengembalian. Administrator Utama memiliki akses tambahan untuk manajemen sistem dan seluruh akun petugas.</p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalTambahStaf')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewTambahStaf()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                    <span>Lanjutkan & Review Data</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL REVIEW DATA PENAMBAHAN STAF BARU                                     -->
<!-- ========================================================================= -->
<div id="modalReviewTambahStaf" class="fixed inset-0 z-[60] bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
                <h4 class="font-serif font-bold text-sm">Review Data Akun Petugas</h4>
            </div>
            <button type="button" onclick="closeModal('modalReviewTambahStaf')" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5">
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px] flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-navy-700 dark:text-sky-400 shrink-0"></i>
                <span>Pastikan data di bawah ini sudah benar sebelum akun didaftarkan.</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 p-3.5 space-y-2.5">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Nama Petugas</span>
                    <span id="rev_staf_nama" class="font-bold text-slate-900 dark:text-white text-sm"></span>
                </div>
                <div class="pt-2.5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Username Login</span>
                    <span id="rev_staf_username" class="font-mono font-semibold text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="pt-2.5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Email Resmi</span>
                    <span id="rev_staf_email" class="text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="pt-2.5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Hak Akses / Peran</span>
                    <span id="rev_staf_role" class="inline-block font-bold text-xs mt-0.5"></span>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between gap-2">
                <button type="button" onclick="closeModal('modalReviewTambahStaf')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    &larr; Kembali & Ubah
                </button>
                <button type="button" onclick="submitFormTambahStaf()" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Konfirmasi & Buat Akun</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT STAF                                                           -->
<!-- ========================================================================= -->
<div id="modalEditStaf" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Edit Data Petugas</h4>
                    <p class="text-[11px] text-slate-300">Perbarui profil dan akses pengguna</p>
                </div>
            </div>
            <button onclick="closeModal('modalEditStaf')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditStaf" action="" method="POST" class="p-5 space-y-3.5 text-xs">
            <?= csrf_field() ?>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Nama Lengkap Petugas <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input type="text" id="edit_nama" name="nama" required 
                           class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Username Login <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="at-sign" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="edit_username" name="username" required pattern="[a-zA-Z0-9_]{4,}" 
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" id="edit_email" name="email" required 
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Password & Role Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Ganti Password <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="edit_password" name="password" minlength="6" 
                               class="w-full pl-9 pr-9 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none placeholder:text-slate-400" 
                               placeholder="Isi sandi baru...">
                        <button type="button" onclick="toggleInputPassword('edit_password', this)" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Hak Akses / Role <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        <select id="edit_role" name="role" required 
                                class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                            <option value="staf">Staf Pustaka (Operasional Harian)</option>
                            <option value="admin">Administrator Utama (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditStaf')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">Perbarui Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentSelectedStaf = null;

    // Toggle eye button for normal input forms
    function toggleInputPassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.innerHTML = isPass ? '<i data-lucide="eye-off" class="w-4 h-4 text-amber-500"></i>' : '<i data-lucide="eye" class="w-4 h-4"></i>';
        lucide.createIcons();
    }

    // Modal Detail Staf
    function detailStaf(data) {
        currentSelectedStaf = data;

        // Set avatar initial
        const avatarEl = document.getElementById('detail_avatar');
        avatarEl.textContent = (data.nama || 'P').charAt(0).toUpperCase();

        // Populate details
        document.getElementById('detail_nama').textContent = data.nama;
        document.getElementById('detail_username_display').textContent = '@' + data.username;
        document.getElementById('detail_username_val').textContent = data.username;
        document.getElementById('detail_email_display').textContent = data.email;
        document.getElementById('detail_email_val').textContent = data.email;

        // Role badge
        const badge = document.getElementById('detail_role_badge');
        const roleDesc = document.getElementById('detail_role_desc');
        if (data.role === 'admin') {
            badge.textContent = 'Administrator Utama';
            badge.className = 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shrink-0';
            roleDesc.textContent = 'Akses Penuh Seluruh Sistem';
        } else {
            badge.textContent = 'Staf Pustaka';
            badge.className = 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shrink-0';
            roleDesc.textContent = 'Operasional Harian Sirkulasi';
        }

        // Timestamps
        document.getElementById('detail_created_val').textContent = data.created_at || '-';
        document.getElementById('detail_updated_val').textContent = data.updated_at || '-';

        lucide.createIcons();
        openModal('modalDetailStaf');
    }

    // Modal Reset Password
    function openResetPassword(data) {
        document.getElementById('formResetPasswordStaf').action = '<?= site_url('/staf/reset-password/') ?>' + data.id;
        document.getElementById('reset_staf_nama').textContent = data.nama;
        document.getElementById('reset_staf_username').textContent = '@' + data.username;
        document.getElementById('input_reset_password').value = '';
        openModal('modalResetPasswordStaf');
    }

    // Jump from Detail to Reset Password modal
    function openResetFromDetail() {
        if (currentSelectedStaf) {
            closeModal('modalDetailStaf');
            openResetPassword(currentSelectedStaf);
        }
    }

    // Copy Text to Clipboard Helper
    function copyText(elementId, btn) {
        const text = document.getElementById(elementId).textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>';
            lucide.createIcons();
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                lucide.createIcons();
            }, 1500);
        });
    }

    // Jump from Detail to Edit modal
    function openEditFromDetail() {
        if (currentSelectedStaf) {
            closeModal('modalDetailStaf');
            editStaf(currentSelectedStaf);
        }
    }

    // Form review before submission
    function reviewTambahStaf() {
        const form = document.getElementById('formTambahStaf');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const nama = document.getElementById('tambah_nama').value.trim();
        const username = document.getElementById('tambah_username').value.trim();
        const email = document.getElementById('tambah_email').value.trim();
        const role = document.getElementById('tambah_role').value;

        document.getElementById('rev_staf_nama').textContent = nama;
        document.getElementById('rev_staf_username').textContent = '@' + username;
        document.getElementById('rev_staf_email').textContent = email;

        const roleSpan = document.getElementById('rev_staf_role');
        if (role === 'admin') {
            roleSpan.textContent = 'Administrator Utama (Akses Penuh)';
            roleSpan.className = 'inline-block font-bold text-xs mt-0.5 text-amber-700 dark:text-amber-400';
        } else {
            roleSpan.textContent = 'Staf Pustaka (Operasional Harian)';
            roleSpan.className = 'inline-block font-bold text-xs mt-0.5 text-slate-800 dark:text-slate-200';
        }

        openModal('modalReviewTambahStaf');
    }

    function submitFormTambahStaf() {
        document.getElementById('formTambahStaf').submit();
    }

    function editStaf(data) {
        document.getElementById('formEditStaf').action = '<?= site_url('/staf/update/') ?>' + data.id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_role').value = data.role;

        openModal('modalEditStaf');
    }
</script>

<?= $this->endSection() ?>
