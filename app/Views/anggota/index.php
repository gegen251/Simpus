<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
                <i data-lucide="users" class="w-5 h-5 text-emerald-300"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-white">Buku Induk Anggota Perpustakaan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar siswa Kelas 1 s/d 6, dewan guru, nomor induk NISN/NIP, dan kontak wali</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= site_url('/anggota/cetak-kartu') ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors shadow-xs" title="Cetak Semua Kartu Anggota (Lembaran A4)">
                <i data-lucide="printer" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span class="hidden sm:inline">Cetak Kartu Massal</span>
            </a>
            <button type="button" onclick="openModal('modalImportAnggota')" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-200 dark:border-emerald-800/60 transition-colors shadow-xs">
                <i data-lucide="file-up" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span>Import Excel / CSV</span>
            </button>
            <button onclick="openModal('modalTambahAnggota')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-navy-900 hover:bg-navy-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                <i data-lucide="user-plus" class="w-4 h-4 text-amber-300"></i>
                <span>Tambah Anggota</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Quick Search Form -->
        <form action="<?= site_url('/anggota') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama, NISN/NIP, no anggota, kontak..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none transition-colors">
            <?php if (!empty($selectedStat)): ?>
                <input type="hidden" name="status" value="<?= esc($selectedStat) ?>">
            <?php endif; ?>
            <?php if (!empty($selectedJk)): ?>
                <input type="hidden" name="jenis_kelamin" value="<?= esc($selectedJk) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <!-- Modern Filter Pop-up Button -->
            <button type="button" onclick="openModal('modalFilterAnggota')" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-navy-600 dark:text-sky-400"></i>
                <span>Filter Anggota</span>
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
                <?php if (!empty($selectedStat)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Status: <strong><?= ucfirst($selectedStat) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($selectedJk)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Gender: <strong><?= $selectedJk === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                    </span>
                <?php endif; ?>
                <?php if ($order !== 'DESC'): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-[11px]">
                        Urutan: <strong><?= $order === 'ASC' ? 'Nama A &rarr; Z' : 'No Anggota' ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2.5">
                <button onclick="openModal('modalFilterAnggota')" class="text-[11px] font-semibold text-navy-800 dark:text-sky-400 hover:underline">
                    Ubah Filter
                </button>
                <span class="text-slate-300 dark:text-slate-700">|</span>
                <a href="<?= site_url('/anggota') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                    Reset Filter
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table of Members -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 w-28 text-center">No Anggota</th>
                        <th class="px-4 py-3.5">Nama Lengkap</th>
                        <th class="px-4 py-3.5">Tipe &amp; Kelas / Jabatan</th>
                        <th class="px-4 py-3.5 font-mono">No Identitas (NISN/NIP)</th>
                        <th class="px-4 py-3.5 text-center">Pinjaman Aktif</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <?php if (empty($anggota)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <i data-lucide="user-x" class="w-6 h-6"></i>
                                </div>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada data anggota yang sesuai</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kriteria pada pop-up filter atau daftarkan anggota baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($anggota as $a): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                <!-- 1. No Anggota -->
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md text-[11px] border border-slate-200/80 dark:border-slate-700"><?= esc($a['nomor_anggota']) ?></span>
                                </td>

                                <!-- 2. Nama Lengkap (dengan Pas Foto) -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <!-- Pas Foto Siswa / Guru (Rasio 3:4) -->
                                        <div class="w-8 h-11 rounded border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 flex items-center justify-center overflow-hidden">
                                            <?php if (!empty($a['foto']) && file_exists(FCPATH . $a['foto'])): ?>
                                                <img src="<?= base_url(esc($a['foto'])) ?>" alt="<?= esc($a['nama']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-[10px] font-bold <?= ($a['tipe_anggota'] ?? 'siswa') === 'guru' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                                                    <span><?= strtoupper(substr($a['nama'], 0, 1)) ?></span>
                                                    <span class="text-[7px] font-semibold text-slate-400 uppercase">3x4</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs">
                                                <?= esc($a['nama']) ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                                <?= esc($a['kontak'] ?: '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Tipe & Kelas/Rombel/Jabatan -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold <?= ($a['tipe_anggota'] ?? 'siswa') === 'guru' ? 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700' ?>">
                                            <?= esc(ucfirst($a['tipe_anggota'] ?? 'siswa')) ?>
                                        </span>
                                        <span class="font-medium text-slate-700 dark:text-slate-300 text-xs">
                                            <?= esc($a['kelas'] ?: ($a['tipe_anggota'] === 'guru' ? 'Dewan Guru' : '-')) ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- 4. No Identitas -->
                                <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300 text-xs">
                                    <?= esc($a['no_identitas']) ?>
                                </td>

                                <!-- 5. Pinjaman Aktif -->
                                <td class="px-4 py-3.5 text-center">
                                    <?php $pinjamCount = (int)($a['pinjaman_aktif'] ?? 0); ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold <?= $pinjamCount > 0 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' ?>">
                                        <?= $pinjamCount ?> Buku
                                    </span>
                                </td>

                                <!-- 6. Status -->
                                <td class="px-4 py-3.5 text-center">
                                    <?php if ($a['status'] === 'aktif'): ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">Aktif</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">Nonaktif</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 7. Aksi (View, Cetak, Edit, Hapus) -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Tombol View Detail -->
                                        <button type="button" onclick="showDetailAnggota(<?= htmlspecialchars(json_encode($a)) ?>)" class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" title="Lihat Detail Lengkap Anggota">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <!-- Tombol Cetak Kartu -->
                                        <a href="<?= site_url('/anggota/cetak-kartu/' . $a['id']) ?>" target="_blank" class="p-1.5 text-slate-600 hover:text-navy-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-colors" title="Cetak Kartu Anggota">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                        <!-- Tombol Edit -->
                                        <button type="button" onclick="editAnggota(<?= htmlspecialchars(json_encode($a)) ?>)" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Edit Anggota">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <button type="button" onclick="confirmDelete('<?= site_url('/anggota/delete/' . $a['id']) ?>', 'Hapus data anggota <?= esc(addslashes($a['nama'])) ?>? Anggota dengan pinjaman aktif tidak dapat dihapus.')" class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 dark:text-slate-400 dark:hover:text-rose-400 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Hapus Anggota">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
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

<!-- ========================================================================= -->
<!-- MODAL DETAIL / VIEW LENGKAP ANGGOTA (SISWA & GURU)                        -->
<!-- ========================================================================= -->
<div id="modalDetailAnggota" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs max-h-[90vh]">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Detail Profil Anggota</h4>
                    <p class="text-[11px] text-slate-300">Informasi lengkap siswa atau dewan guru</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalDetailAnggota')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Body Scrollable -->
        <div class="p-5 space-y-4 overflow-y-auto">
            <!-- Header Kartu Profil dengan Pasfoto 3:4 -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                <!-- Pas Foto Box 3:4 -->
                <div class="w-20 h-28 rounded-md overflow-hidden border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 shrink-0 flex items-center justify-center relative">
                    <img id="detail_ang_foto" src="" alt="Pas Foto" class="w-full h-full object-cover hidden">
                    <div id="detail_ang_foto_placeholder" class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 p-2 text-center">
                        <i data-lucide="user" class="w-8 h-8 mb-1"></i>
                        <span class="text-[8px] font-bold uppercase">Pasfoto 3x4</span>
                    </div>
                </div>

                <!-- Info Utama -->
                <div class="flex-1 text-center sm:text-left min-w-0">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                        <span id="detail_ang_tipe_badge" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-800 border border-slate-300">
                            Siswa
                        </span>
                        <span id="detail_ang_status_badge" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Aktif
                        </span>
                    </div>
                    <h3 id="detail_ang_nama" class="font-bold text-base text-slate-900 dark:text-white leading-snug truncate">
                        Nama Lengkap Siswa
                    </h3>
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-300 mt-0.5" id="detail_ang_kelas_jabatan">
                        Kelas 4A
                    </p>
                    <div class="mt-2.5 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-[11px]">
                        <span class="inline-flex items-center gap-1 font-mono font-bold text-navy-900 dark:text-sky-300 bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span id="detail_ang_nomor">AG-0001</span>
                        </span>
                        <span class="inline-flex items-center gap-1 font-mono text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span id="detail_ang_identitas">0081234567</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Grid Informasi Tambahan ("Sisa Data") -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Jenis Kelamin -->
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Jenis Kelamin</span>
                    </span>
                    <p id="detail_ang_jk" class="font-semibold text-slate-800 dark:text-slate-200 text-xs">Laki-laki</p>
                </div>

                <!-- Pinjaman Aktif -->
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                        <i data-lucide="book-open" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Pinjaman Buku Aktif</span>
                    </span>
                    <p id="detail_ang_pinjaman" class="font-semibold text-slate-800 dark:text-slate-200 text-xs">0 Buku</p>
                </div>

                <!-- Kontak / WhatsApp -->
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Kontak / WhatsApp</span>
                    </span>
                    <div class="flex items-center justify-between">
                        <p id="detail_ang_kontak" class="font-mono font-semibold text-slate-800 dark:text-slate-200 text-xs">081234567890</p>
                        <a id="detail_ang_wa_link" href="#" target="_blank" class="text-[10.5px] font-semibold text-emerald-700 dark:text-emerald-400 hover:underline inline-flex items-center gap-1">
                            <span>Hubungi WA</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>

                <!-- Email -->
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Alamat Email</span>
                    </span>
                    <p id="detail_ang_email" class="font-medium text-slate-800 dark:text-slate-200 text-xs truncate">siswa@email.com</p>
                </div>
            </div>

            <!-- Alamat Lengkap Domisili -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Alamat Lengkap Domisili</span>
                </span>
                <p id="detail_ang_alamat" class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    Alamat lengkap domisili siswa/guru...
                </p>
            </div>

            <!-- Metadata / Waktu Pendaftaran -->
            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800/60 text-[11px] text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                <span class="flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Terdaftar Pada:</span>
                </span>
                <span id="detail_ang_created" class="font-mono font-medium text-slate-700 dark:text-slate-300">-</span>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0">
            <button type="button" onclick="closeModal('modalDetailAnggota')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition-colors">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <a id="btn_detail_cetak_kartu" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak Kartu</span>
                </a>
                <button type="button" id="btn_detail_edit" onclick="openEditFromMemberDetail()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    <span>Edit Anggota</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POP-UP FILTER ANGGOTA                                              -->
<!-- ========================================================================= -->
<div id="modalFilterAnggota" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-slate-200">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Filter Data Siswa & Guru</h4>
                    <p class="text-[11px] text-slate-300">Saring daftar anggota perpustakaan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalFilterAnggota')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/anggota') ?>" method="GET" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kata Kunci (Nama / NISN / No Anggota)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Ketik kata kunci pencarian..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tipe Anggota</label>
                    <select name="tipe" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="siswa" <?= ($selectedTipe ?? '') === 'siswa' ? 'selected' : '' ?>>Siswa (Murid)</option>
                        <option value="guru" <?= ($selectedTipe ?? '') === 'guru' ? 'selected' : '' ?>>Dewan Guru</option>
                        <option value="karyawan" <?= ($selectedTipe ?? '') === 'karyawan' ? 'selected' : '' ?>>Staf / Karyawan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pilihan Kelas</label>
                    <select name="kelas" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="">Semua Kelas</option>
                        <?php if (!empty($kelasList)): ?>
                            <?php foreach ($kelasList as $kl): ?>
                                <option value="<?= esc($kl['kelas']) ?>" <?= ($selectedKelas ?? '') === $kl['kelas'] ? 'selected' : '' ?>><?= esc($kl['kelas']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Status Keanggotaan</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?= ($selectedStat ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($selectedStat ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="">Semua Gender</option>
                        <option value="L" <?= ($selectedJk ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($selectedJk ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan Data</label>
                <select name="order" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                    <option value="DESC" <?= ($order ?? 'DESC') === 'DESC' ? 'selected' : '' ?>>Terbaru Mendaftar (DESC)</option>
                    <option value="ASC" <?= ($order ?? '') === 'ASC' ? 'selected' : '' ?>>Nama Anggota A &rarr; Z</option>
                    <option value="nomor" <?= ($order ?? '') === 'nomor' ? 'selected' : '' ?>>Nomor Anggota Terkecil</option>
                </select>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/anggota') ?>" class="px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                    Reset Filter
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModal('modalFilterAnggota')" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
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
<!-- MODAL TAMBAH ANGGOTA                                                      -->
<!-- ========================================================================= -->
<div id="modalTambahAnggota" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Pendaftaran Anggota Baru</h4>
                    <p class="text-[11px] text-slate-300">Daftarkan siswa atau guru ke sistem perpustakaan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalTambahAnggota')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formTambahAnggota" action="<?= site_url('/anggota/store') ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 overflow-y-auto text-xs">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="hash" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>No. Anggota</span>
                    </label>
                    <input type="text" id="tambah_anggota_nomor" name="nomor_anggota" value="<?= esc($newNomor) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-slate-100 dark:bg-slate-800 font-mono text-navy-900 dark:text-sky-300 font-bold" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>NISN / NIP *</span>
                    </label>
                    <input type="text" id="tambah_anggota_identitas" name="no_identitas" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-mono" placeholder="Contoh: 0081234567">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Nama Lengkap Siswa / Guru *</span>
                </label>
                <input type="text" id="tambah_anggota_nama" name="nama" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-medium" placeholder="Nama lengkap sesuai dokumen">
            </div>

            <!-- Upload Pas Foto Siswa & Guru -->
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i data-lucide="camera" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Upload Pas Foto Siswa / Guru (Opsional)</span>
                </label>
                <div class="flex items-center gap-3.5">
                    <!-- Preview Box Rasio 3:4 -->
                    <div class="w-16 h-21 rounded-md border border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 relative" id="tambah_foto_preview_box">
                        <img id="tambah_foto_preview" src="" alt="Preview Pas Foto" class="w-full h-full object-cover hidden">
                        <div id="tambah_foto_placeholder" class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-center p-1">
                            <i data-lucide="image" class="w-5 h-5 mb-0.5"></i>
                            <span class="text-[8px] font-bold uppercase leading-none">3 x 4</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-1.5">
                        <input type="file" id="tambah_foto_file" name="foto_file" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewFotoTambah(this)" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 dark:file:bg-slate-700 dark:file:text-slate-200 cursor-pointer">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="text-[10.5px] text-slate-500 dark:text-slate-400 leading-tight">
                                <strong>Format:</strong> JPG, PNG, WEBP &bull; Rasio 3:4 (Pasfoto 3x4 cm / maks. 2 MB)
                            </p>
                            <button type="button" id="btn_batal_foto_tambah" onclick="resetFotoTambah()" class="hidden text-[10.5px] font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                                Batal Pilih Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="award" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Tipe Anggota *</span>
                    </label>
                    <select id="tambah_anggota_tipe" name="tipe_anggota" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="siswa">Siswa (Murid SD)</option>
                        <option value="guru">Dewan Guru / Pendidik</option>
                        <option value="karyawan">Staf / Karyawan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Kelas / Rombel / Jabatan</span>
                    </label>
                    <input type="text" id="tambah_anggota_kelas" name="kelas" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none" placeholder="Contoh: Kelas 4A atau Guru Olahraga">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Jenis Kelamin</span>
                    </label>
                    <select id="tambah_anggota_jk" name="jenis_kelamin" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>No. Kontak / WA *</span>
                    </label>
                    <input type="text" id="tambah_anggota_kontak" name="kontak" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-mono" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Email (Opsional)</span>
                    </label>
                    <input type="email" id="tambah_anggota_email" name="email" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none" placeholder="siswa@email.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Status</span>
                    </label>
                    <select id="tambah_anggota_status" name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Alamat Lengkap</span>
                </label>
                <textarea id="tambah_anggota_alamat" name="alamat" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none" placeholder="Alamat lengkap domisili..."></textarea>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modalTambahAnggota')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewTambahAnggota()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors">
                    <span>Lanjutkan & Review Data</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL REVIEW DATA SEBELUM SIMPAN ANGGOTA                                  -->
<!-- ========================================================================= -->
<div id="modalReviewTambahAnggota" class="fixed inset-0 z-[60] bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-emerald-800 text-white flex items-center justify-between border-b border-emerald-900">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-white/10 flex items-center justify-center text-emerald-200">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
                <h4 class="font-serif font-bold text-sm">Review Pendaftaran Anggota</h4>
            </div>
            <button type="button" onclick="closeModal('modalReviewTambahAnggota')" class="text-emerald-200 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5">
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-900 dark:text-emerald-200 text-[11px] flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>Pastikan data siswa atau dewan guru sudah sesuai sebelum disimpan.</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 p-3.5 space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-16 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shrink-0 flex items-center justify-center overflow-hidden">
                        <img id="rev_ang_foto" src="" alt="Pas Foto" class="w-full h-full object-cover hidden">
                        <div id="rev_ang_foto_placeholder" class="w-full h-full flex flex-col items-center justify-center text-slate-400 text-center p-1">
                            <i data-lucide="user" class="w-5 h-5 mb-0.5"></i>
                            <span class="text-[7.5px] uppercase font-bold">Tanpa Foto</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Nama Lengkap</span>
                        <span id="rev_ang_nama" class="font-bold text-slate-900 dark:text-white text-sm"></span>
                    </div>
                </div>
                <div class="pt-2.5 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">No. Anggota</span>
                        <span id="rev_ang_nomor" class="font-mono font-bold text-navy-900 dark:text-sky-300"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">No. Identitas (NISN/NIP)</span>
                        <span id="rev_ang_identitas" class="font-mono text-slate-800 dark:text-slate-200 font-semibold"></span>
                    </div>
                </div>
                <div class="pt-2.5 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Jenis Kelamin</span>
                        <span id="rev_ang_jk" class="text-slate-800 dark:text-slate-200 font-medium"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Kontak / WA</span>
                        <span id="rev_ang_kontak" class="text-slate-800 dark:text-slate-200 font-medium"></span>
                    </div>
                </div>
                <div class="pt-2.5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Alamat Tinggal</span>
                    <p id="rev_ang_alamat" class="text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed"></p>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between gap-2">
                <button type="button" onclick="closeModal('modalReviewTambahAnggota')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    &larr; Kembali & Ubah
                </button>
                <button type="button" onclick="submitFormTambahAnggota()" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Konfirmasi & Daftarkan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Anggota -->
<div id="modalEditAnggota" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-amber-300">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Ubah Data Anggota</h4>
                    <p class="text-[11px] text-slate-300">Perbarui informasi profil siswa atau guru</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalEditAnggota')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formEditAnggota" action="" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 overflow-y-auto text-xs">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="hash" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Nomor Anggota</span>
                    </label>
                    <input type="text" id="edit_nomor_anggota" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-slate-100 dark:bg-slate-800 font-mono text-slate-600 dark:text-slate-400 font-bold" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>No. Identitas *</span>
                    </label>
                    <input type="text" id="edit_no_identitas" name="no_identitas" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Nama Lengkap *</span>
                </label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-medium">
            </div>

            <!-- Upload / Perbarui Pas Foto Siswa & Guru -->
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i data-lucide="camera" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Pas Foto Siswa / Guru (Opsional)</span>
                </label>
                <div class="flex items-center gap-3.5">
                    <!-- Live Image Preview Box (Rasio 3:4) -->
                    <div class="w-16 h-21 rounded-md border border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 relative" id="edit_foto_preview_box">
                        <img id="edit_foto_preview" src="" alt="Pas Foto" class="w-full h-full object-cover hidden">
                        <div id="edit_foto_placeholder" class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-center p-1">
                            <i data-lucide="image" class="w-5 h-5 mb-0.5"></i>
                            <span class="text-[8px] font-bold uppercase leading-none">3 x 4</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-1.5">
                        <input type="file" id="edit_foto_file" name="foto_file" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewFotoEdit(this)" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 dark:file:bg-slate-700 dark:file:text-slate-200 cursor-pointer">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="text-[10.5px] text-slate-500 dark:text-slate-400 leading-tight">
                                <strong>Format:</strong> JPG, PNG, WEBP &bull; Rasio 3:4 (Pasfoto 3x4 cm / maks. 2 MB)
                            </p>
                            <label id="container_hapus_foto" class="hidden items-center gap-1.5 text-[11px] text-rose-600 dark:text-rose-400 cursor-pointer hover:underline">
                                <input type="checkbox" id="edit_hapus_foto" name="hapus_foto" value="1" onchange="toggleHapusFoto(this)" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Hapus Foto</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="award" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Tipe Anggota *</span>
                    </label>
                    <select id="edit_tipe_anggota" name="tipe_anggota" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="siswa">Siswa (Murid SD)</option>
                        <option value="guru">Dewan Guru / Pendidik</option>
                        <option value="karyawan">Staf / Karyawan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Kelas / Rombel / Jabatan</span>
                    </label>
                    <input type="text" id="edit_kelas" name="kelas" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none" placeholder="Contoh: Kelas 4A atau Guru Olahraga">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Jenis Kelamin</span>
                    </label>
                    <select id="edit_jenis_kelamin" name="jenis_kelamin" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>No. Kontak / WA *</span>
                    </label>
                    <input type="text" id="edit_kontak" name="kontak" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Email</span>
                    </label>
                    <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Status</span>
                    </label>
                    <select id="edit_status_anggota" name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Alamat Lengkap</span>
                </label>
                <textarea id="edit_alamat" name="alamat" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 dark:focus:ring-indigo-500 focus:border-navy-900 focus:outline-none"></textarea>
            </div>

            <div class="pt-3.5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modalEditAnggota')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-navy-900 hover:bg-navy-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Perbarui Data Anggota</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL IMPORT ANGGOTA DARI EXCEL / CSV                                     -->
<!-- ========================================================================= -->
<div id="modalImportAnggota" class="fixed inset-0 z-50 bg-slate-900/50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-navy-900 text-white flex items-center justify-between border-b border-navy-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-emerald-300">
                    <i data-lucide="file-up" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight">Import Data Anggota Massal</h4>
                    <p class="text-[11px] text-slate-300">Unggah data siswa/guru dari file spreadsheet Excel / CSV</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalImportAnggota')" class="text-slate-400 hover:text-white p-1 rounded-md transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/anggota/import') ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">
            <?= csrf_field() ?>

            <!-- Guide Box -->
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-750 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-navy-700 dark:text-sky-400"></i>
                        Petunjuk Format File:
                    </span>
                    <a href="<?= site_url('/anggota/template-import') ?>" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-semibold rounded-md transition-colors">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh Template CSV</span>
                    </a>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                    1. Unduh template CSV anggota dan buka di Excel / Google Sheets.<br>
                    2. Masukkan data nama, tipe (siswa/guru), kelas, NISN/NIP, gender, kontak, email, dan alamat.<br>
                    3. Simpan file format <strong>CSV (Comma Delimited)</strong> lalu unggah di bawah ini.
                </p>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Pilih File CSV (.csv):</label>
                <input type="file" name="file_csv" accept=".csv,text/csv" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-800 dark:file:bg-slate-700 dark:file:text-slate-200">
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalImportAnggota')" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    <span>Proses Import Anggota</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live Preview Pas Foto Tambah
    function previewFotoTambah(input) {
        const preview = document.getElementById('tambah_foto_preview');
        const placeholder = document.getElementById('tambah_foto_placeholder');
        const btnBatal = document.getElementById('btn_batal_foto_tambah');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                btnBatal.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            resetFotoTambah();
        }
    }

    function resetFotoTambah() {
        const fileInput = document.getElementById('tambah_foto_file');
        const preview = document.getElementById('tambah_foto_preview');
        const placeholder = document.getElementById('tambah_foto_placeholder');
        const btnBatal = document.getElementById('btn_batal_foto_tambah');
        fileInput.value = '';
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        btnBatal.classList.add('hidden');
    }

    // Live Preview Pas Foto Edit
    let currentEditFotoUrl = '';
    function previewFotoEdit(input) {
        const preview = document.getElementById('edit_foto_preview');
        const placeholder = document.getElementById('edit_foto_placeholder');
        const hapusCheck = document.getElementById('edit_hapus_foto');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if (hapusCheck) hapusCheck.checked = false;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            if (currentEditFotoUrl) {
                preview.src = currentEditFotoUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }
    }

    function toggleHapusFoto(checkbox) {
        const preview = document.getElementById('edit_foto_preview');
        const placeholder = document.getElementById('edit_foto_placeholder');
        const fileInput = document.getElementById('edit_foto_file');
        if (checkbox.checked) {
            fileInput.value = '';
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        } else {
            if (currentEditFotoUrl) {
                preview.src = currentEditFotoUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        }
    }

    function editAnggota(data) {
        document.getElementById('formEditAnggota').action = '<?= site_url('/anggota/update/') ?>' + data.id;
        document.getElementById('edit_nomor_anggota').value = data.nomor_anggota;
        document.getElementById('edit_no_identitas').value = data.no_identitas;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_tipe_anggota').value = data.tipe_anggota || 'siswa';
        document.getElementById('edit_kelas').value = data.kelas || '';
        document.getElementById('edit_jenis_kelamin').value = data.jenis_kelamin;
        document.getElementById('edit_kontak').value = data.kontak;
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_status_anggota').value = data.status;
        document.getElementById('edit_alamat').value = data.alamat || '';

        // Reset Foto Input & Preview
        const editFileInput = document.getElementById('edit_foto_file');
        const editPreview = document.getElementById('edit_foto_preview');
        const editPlaceholder = document.getElementById('edit_foto_placeholder');
        const containerHapus = document.getElementById('container_hapus_foto');
        const checkHapus = document.getElementById('edit_hapus_foto');

        editFileInput.value = '';
        checkHapus.checked = false;

        if (data.foto) {
            currentEditFotoUrl = '<?= base_url() ?>/' + data.foto;
            editPreview.src = currentEditFotoUrl;
            editPreview.classList.remove('hidden');
            editPlaceholder.classList.add('hidden');
            containerHapus.classList.remove('hidden');
            containerHapus.classList.add('inline-flex');
        } else {
            currentEditFotoUrl = '';
            editPreview.src = '';
            editPreview.classList.add('hidden');
            editPlaceholder.classList.remove('hidden');
            containerHapus.classList.add('hidden');
            containerHapus.classList.remove('inline-flex');
        }

        openModal('modalEditAnggota');
    }

    function reviewTambahAnggota() {
        const form = document.getElementById('formTambahAnggota');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const nomor = document.getElementById('tambah_anggota_nomor').value.trim();
        const identitas = document.getElementById('tambah_anggota_identitas').value.trim();
        const nama = document.getElementById('tambah_anggota_nama').value.trim();
        const jk = document.getElementById('tambah_anggota_jk').value === 'L' ? 'Laki-laki' : 'Perempuan';
        const kontak = document.getElementById('tambah_anggota_kontak').value.trim();
        const alamat = document.getElementById('tambah_anggota_alamat').value.trim() || 'Alamat tidak diisi.';

        document.getElementById('rev_ang_nama').textContent = nama;
        document.getElementById('rev_ang_nomor').textContent = nomor;
        document.getElementById('rev_ang_identitas').textContent = identitas;
        document.getElementById('rev_ang_jk').textContent = jk;
        document.getElementById('rev_ang_kontak').textContent = kontak;
        document.getElementById('rev_ang_alamat').textContent = alamat;

        // Preview Foto di Modal Review
        const tambahPreview = document.getElementById('tambah_foto_preview');
        const revFoto = document.getElementById('rev_ang_foto');
        const revPlaceholder = document.getElementById('rev_ang_foto_placeholder');
        if (!tambahPreview.classList.contains('hidden') && tambahPreview.src) {
            revFoto.src = tambahPreview.src;
            revFoto.classList.remove('hidden');
            revPlaceholder.classList.add('hidden');
        } else {
            revFoto.src = '';
            revFoto.classList.add('hidden');
            revPlaceholder.classList.remove('hidden');
        }

        openModal('modalReviewTambahAnggota');
    }

    // Detail / View Anggota
    let currentDetailMemberData = null;

    function showDetailAnggota(data) {
        currentDetailMemberData = data;

        document.getElementById('detail_ang_nama').textContent = data.nama;
        document.getElementById('detail_ang_nomor').textContent = data.nomor_anggota;
        document.getElementById('detail_ang_identitas').textContent = data.no_identitas;
        
        // Tipe & Kelas
        const tipe = (data.tipe_anggota || 'siswa');
        const tipeBadge = document.getElementById('detail_ang_tipe_badge');
        tipeBadge.textContent = tipe === 'guru' ? 'Dewan Guru' : (tipe === 'karyawan' ? 'Staf/Karyawan' : 'Siswa');
        if (tipe === 'guru') {
            tipeBadge.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60";
        } else {
            tipeBadge.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700";
        }

        const kelasText = data.kelas || (tipe === 'guru' ? 'Dewan Guru' : '-');
        document.getElementById('detail_ang_kelas_jabatan').textContent = (tipe === 'guru' ? 'Jabatan / Tugas: ' : 'Kelas: ') + kelasText;

        // Status Badge
        const statusBadge = document.getElementById('detail_ang_status_badge');
        if (data.status === 'aktif') {
            statusBadge.textContent = 'Status: Aktif';
            statusBadge.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60";
        } else {
            statusBadge.textContent = 'Status: Nonaktif';
            statusBadge.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700";
        }

        // Pasfoto
        const fotoImg = document.getElementById('detail_ang_foto');
        const fotoPlaceholder = document.getElementById('detail_ang_foto_placeholder');
        if (data.foto) {
            fotoImg.src = '<?= base_url() ?>/' + data.foto;
            fotoImg.classList.remove('hidden');
            fotoPlaceholder.classList.add('hidden');
        } else {
            fotoImg.src = '';
            fotoImg.classList.add('hidden');
            fotoPlaceholder.classList.remove('hidden');
        }

        // Sisa Data: Gender, Kontak, Email, Alamat, Pinjaman, Created_at
        document.getElementById('detail_ang_jk').textContent = data.jenis_kelamin === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)';
        document.getElementById('detail_ang_kontak').textContent = data.kontak || '-';
        
        // WhatsApp Link
        const cleanPhone = (data.kontak || '').replace(/[^0-9]/g, '');
        const waPhone = cleanPhone.startsWith('0') ? '62' + cleanPhone.slice(1) : cleanPhone;
        const waLink = document.getElementById('detail_ang_wa_link');
        if (cleanPhone) {
            waLink.href = 'https://wa.me/' + waPhone;
            waLink.classList.remove('hidden');
            waLink.classList.add('inline-flex');
        } else {
            waLink.classList.add('hidden');
            waLink.classList.remove('inline-flex');
        }

        document.getElementById('detail_ang_email').textContent = data.email || 'Tidak ada email terdaftar';
        document.getElementById('detail_ang_alamat').textContent = data.alamat || 'Alamat domisili belum dilengkapi.';
        
        const pinjamCount = parseInt(data.pinjaman_aktif || 0);
        document.getElementById('detail_ang_pinjaman').textContent = pinjamCount + ' Buku Sedang Dipinjam';

        document.getElementById('detail_ang_created').textContent = data.created_at || '-';

        // Action Link Cetak Kartu
        document.getElementById('btn_detail_cetak_kartu').href = '<?= site_url('/anggota/cetak-kartu/') ?>' + data.id;

        if (window.lucide) {
            lucide.createIcons();
        }

        openModal('modalDetailAnggota');
    }

    function openEditFromMemberDetail() {
        if (currentDetailMemberData) {
            closeModal('modalDetailAnggota');
            editAnggota(currentDetailMemberData);
        }
    }

    function submitFormTambahAnggota() {
        document.getElementById('formTambahAnggota').submit();
    }
</script>

<?= $this->endSection() ?>
