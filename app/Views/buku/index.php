<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-navy-800 text-white flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-5 h-5 text-amber-400"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-slate-900 dark:text-white">Koleksi Buku Sekolah Dasar</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Inventaris buku pelajaran tematik kurikulum merdeka, fabel, dan cerita nusantara</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openModalCetakLabel()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors shadow-2xs" title="Cetak Label Punggung & Barcode Buku">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"></i>
                <span class="hidden sm:inline">Cetak Label</span>
            </button>
            <button type="button" onclick="openModal('modalImportBuku')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors shadow-2xs">
                <i data-lucide="file-up" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"></i>
                <span>Import CSV</span>
            </button>
            <button onclick="openModal('modalFilterBuku')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors shadow-2xs">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"></i>
                <span>Filter</span>
                <?php if (!empty($activeFiltersCount) && $activeFiltersCount > 0): ?>
                    <span class="w-4 h-4 rounded-full bg-navy-800 dark:bg-amber-500 text-white dark:text-slate-950 text-[10px] font-bold flex items-center justify-center">
                        <?= $activeFiltersCount ?>
                    </span>
                <?php endif; ?>
            </button>
            <button onclick="openModal('modalTambahBuku')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-navy-800 hover:bg-navy-900 dark:bg-navy-700 dark:hover:bg-navy-600 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Tambah Buku</span>
            </button>
        </div>
    </div>

    <!-- Active Filters Notification Bar (If Filters Applied) -->
    <?php if (!empty($activeFiltersCount) && $activeFiltersCount > 0): ?>
        <div class="bg-slate-50 dark:bg-slate-800/60 p-3 px-4 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-2.5 text-xs shadow-2xs">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-slate-500"></i>
                    Filter Aktif (<?= $activeFiltersCount ?>):
                </span>
                <?php if (!empty($keyword)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Kata Kunci: "<strong><?= esc($keyword) ?></strong>"
                    </span>
                <?php endif; ?>
                <?php if (!empty($selectedKat)): ?>
                    <?php 
                        $katNama = 'Kategori #' . $selectedKat;
                        foreach ($kategori as $k) {
                            if ($k['id'] == $selectedKat) { $katNama = $k['nama_kategori']; break; }
                        }
                    ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Kategori: <strong><?= esc($katNama) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($selectedRak)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Rak: <strong><?= esc($selectedRak) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($ketersediaan)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Stok: <strong><?= $ketersediaan === 'tersedia' ? 'Tersedia' : 'Habis' ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($selectedStat)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Status: <strong><?= ucfirst($selectedStat) ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('modalFilterBuku')" class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:underline">
                    Ubah Filter
                </button>
                <span class="text-slate-300 dark:text-slate-600">|</span>
                <a href="<?= site_url('/buku') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    <i data-lucide="x" class="w-3 h-3"></i>
                    Reset Filter
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table Information Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400 px-0.5">
        <div class="flex items-center gap-2 font-medium">
            <span>Menampilkan <strong><?= count($buku) ?></strong> judul koleksi SD</span>
            <span>&bull;</span>
            <span class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-300">
                <i data-lucide="arrow-down-az" class="w-3.5 h-3.5 text-slate-400"></i>
                Urutan: <strong>Kode Buku (ASC)</strong>
            </span>
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500">
            <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400"></i>
            <span>Klik baris atau tombol aksi untuk rincian & verifikasi cover</span>
        </div>
    </div>

    <!-- Clean Flat Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-3 py-3 w-10 text-center">
                            <input type="checkbox" id="checkAllBuku" class="rounded border-slate-300 text-navy-800 focus:ring-navy-800 w-4 h-4 cursor-pointer" title="Pilih Semua Buku" onchange="toggleSelectAllBuku(this)">
                        </th>
                        <th class="px-3 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3 w-20 text-center">Sampul</th>
                        <th class="px-4 py-3">Nama Buku & Penulis</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-center">Ketersediaan Stok</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($buku)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i data-lucide="book-x" class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-2"></i>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada data buku yang sesuai</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Coba ubah kata kunci atau kriteria filter.</p>
                                <button onclick="openModal('modalFilterBuku')" class="mt-3 px-3 py-1.5 bg-navy-800 text-white rounded-lg text-xs font-semibold hover:bg-navy-900 transition-colors">
                                    Buka Filter
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($buku as $b): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- Checkbox Pilihan -->
                                <td class="px-3 py-3 text-center">
                                    <input type="checkbox" name="buku_select[]" value="<?= $b['id'] ?>" class="check-buku rounded border-slate-300 text-navy-800 focus:ring-navy-800 w-4 h-4 cursor-pointer" onchange="updateFloatingBar()">
                                </td>

                                <!-- No Urut -->
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500 font-mono text-xs">
                                    <?= $no++ ?>
                                </td>

                                <!-- Sampul -->
                                <td class="px-4 py-3 text-center">
                                    <div onclick='viewBuku(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                         class="w-10 h-14 mx-auto shrink-0 rounded overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer"
                                         title="Klik untuk melihat detail & sampul">
                                        <?php if (!empty($b['cover'])): ?>
                                            <img src="<?= base_url(esc($b['cover'])) ?>" alt="<?= esc($b['judul']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <i data-lucide="book" class="w-4 h-4"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Nama Buku -->
                                <td class="px-4 py-3">
                                    <div onclick='viewBuku(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                         class="font-semibold text-slate-900 dark:text-slate-100 text-xs leading-snug hover:text-navy-800 dark:hover:text-amber-400 transition-colors cursor-pointer line-clamp-2"
                                         title="Klik untuk membuka detail lengkap">
                                        <?= esc($b['judul']) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 flex items-center gap-1.5 font-mono">
                                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1 py-0.2 rounded border border-slate-200 dark:border-slate-700 text-[10px]">
                                            <?= esc($b['kode_buku']) ?>
                                        </span>
                                        <span>&bull;</span>
                                        <span class="truncate max-w-[200px] font-sans"><?= esc($b['penulis']) ?></span>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <?= esc($b['nama_kategori'] ?: 'Umum') ?>
                                    </span>
                                </td>

                                <!-- Ketersediaan Stok -->
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 font-bold <?= $b['stok_tersedia'] > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                                        <span class="text-xs"><?= $b['stok_tersedia'] ?></span>
                                        <span class="text-slate-400 dark:text-slate-500 text-[11px] font-normal">/ <?= $b['jumlah_eksemplar'] ?> eks</span>
                                    </div>
                                    <div class="text-[10px] font-medium <?= $b['stok_tersedia'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                                        <?= $b['stok_tersedia'] > 0 ? 'Tersedia di Rak' : 'Habis Dipinjam' ?>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <?php if ($b['status'] === 'aktif'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Kolom Aksi -->
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- View -->
                                        <button type="button" onclick='viewBuku(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                                class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-navy-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded border border-slate-200 dark:border-slate-700 transition-colors" 
                                                title="Lihat Detail">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Edit -->
                                        <button type="button" onclick='editBuku(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                                class="p-1.5 text-amber-600 dark:text-amber-400 hover:text-amber-700 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800 rounded transition-colors" 
                                                title="Edit Buku">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Cetak Label -->
                                        <a href="<?= site_url('/buku/cetak-label/' . $b['id']) ?>" target="_blank"
                                           class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-navy-900 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded transition-colors" 
                                           title="Cetak Label Punggung">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                        </a>

                                        <!-- Hapus -->
                                        <button type="button" 
                                                onclick="confirmDelete('<?= site_url('/buku/delete/' . $b['id']) ?>', 'Apakah Anda yakin ingin menghapus buku <?= esc(addslashes($b['judul'])) ?>? Buku yang sedang dalam status dipinjam tidak dapat dihapus.')" 
                                                class="p-1.5 text-rose-600 dark:text-rose-400 hover:text-rose-700 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 rounded transition-colors" 
                                                title="Hapus Buku">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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
<!-- MODAL VIEW DETAIL BUKU                                                    -->
<!-- ========================================================================= -->
<div id="modalViewBuku" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="book-marked" class="w-4 h-4 text-amber-400"></i>
                <div>
                    <h4 class="font-serif font-bold text-sm">Rincian Buku SD</h4>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalViewBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 overflow-y-auto space-y-4 text-xs">
            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <!-- Cover Display -->
                <div class="w-24 h-32 shrink-0 rounded overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 relative">
                    <img id="view_cover_img" src="" alt="Cover Buku" class="w-full h-full object-cover">
                    <div id="view_cover_placeholder" class="w-full h-full hidden flex-col items-center justify-center text-slate-400 p-2 text-center">
                        <i data-lucide="book" class="w-6 h-6 mb-1"></i>
                        <span class="text-[9px] font-medium">Tanpa Sampul</span>
                    </div>
                </div>

                <!-- Main Book Details -->
                <div class="space-y-1.5 flex-1 text-center sm:text-left">
                    <div class="flex items-center justify-center sm:justify-start gap-1.5 flex-wrap">
                        <span id="view_kode_buku_badge" class="font-mono font-bold px-2 py-0.5 rounded bg-slate-800 text-white text-[10px]"></span>
                        <span id="view_kategori_badge" class="font-medium px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[10px]"></span>
                        <span id="view_status_pill" class="font-bold px-2 py-0.5 rounded text-[10px]"></span>
                    </div>
                    <h3 id="view_judul" class="font-serif font-bold text-sm sm:text-base text-slate-900 dark:text-white leading-snug pt-0.5"></h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs">
                        Karya: <strong id="view_penulis" class="text-slate-800 dark:text-slate-200 font-semibold"></strong>
                    </p>

                    <div class="pt-2 flex items-center justify-center sm:justify-start gap-2">
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded text-center">
                            <span class="block text-[9px] uppercase font-bold text-slate-400">Tersedia</span>
                            <span id="view_stok_tersedia_num" class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0</span>
                        </div>
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded text-center">
                            <span class="block text-[9px] uppercase font-bold text-slate-400">Total Fisik</span>
                            <span id="view_total_eksemplar_text" class="text-sm font-bold text-slate-700 dark:text-slate-300">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Structured Metadata Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nomor ISBN</span>
                    <span id="view_isbn" class="font-mono text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Penerbit</span>
                    <span id="view_penerbit" class="text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tahun Terbit</span>
                    <span id="view_tahun_terbit" class="text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Lokasi Rak</span>
                    <span id="view_lokasi_rak" class="text-slate-800 dark:text-slate-200"></span>
                </div>
            </div>

            <!-- Sinopsis / Deskripsi -->
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Sinopsis & Ringkasan</span>
                <p id="view_deskripsi" class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs"></p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 px-5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <button type="button" id="btnSwitchToEdit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold rounded-lg text-xs transition-colors">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit Data</span>
            </button>
            <button type="button" onclick="closeModal('modalViewBuku')" class="px-4 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL FILTER BUKU                                                         -->
<!-- ========================================================================= -->
<div id="modalFilterBuku" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Filter Koleksi Buku SD</h4>
            </div>
            <button type="button" onclick="closeModal('modalFilterBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/buku') ?>" method="GET" class="p-5 overflow-y-auto space-y-3.5 text-xs">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kata Kunci / Judul / ISBN</label>
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul buku, kode, nomor ISBN..."
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kategori Buku</label>
                    <select name="kategori" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="">-- Semua Kategori --</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ($selectedKat ?? '') == $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="<?= esc($selectedRak ?? '') ?>" list="listRakSuggestions" placeholder="Semua rak..."
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                    <datalist id="listRakSuggestions">
                        <?php if (!empty($rakList)): ?>
                            <?php foreach ($rakList as $r): ?>
                                <option value="<?= esc($r['lokasi_rak']) ?>"></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Penulis</label>
                    <input type="text" name="penulis" value="<?= esc($penulis ?? '') ?>" placeholder="Nama pengarang..."
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Penerbit</label>
                    <input type="text" name="penerbit" value="<?= esc($penerbit ?? '') ?>" placeholder="Nama penerbit..."
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Ketersediaan</label>
                    <select name="ketersediaan" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="">Semua</option>
                        <option value="tersedia" <?= ($ketersediaan ?? '') === 'tersedia' ? 'selected' : '' ?>>Tersedia (&gt; 0)</option>
                        <option value="habis" <?= ($ketersediaan ?? '') === 'habis' ? 'selected' : '' ?>>Habis Dipinjam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="">Semua</option>
                        <option value="aktif" <?= ($selectedStat ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($selectedStat ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan Kode</label>
                    <select name="order" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="ASC" <?= ($order ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>ASC (Kecil)</option>
                        <option value="DESC" <?= ($order ?? '') === 'DESC' ? 'selected' : '' ?>>DESC (Besar)</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <a href="<?= site_url('/buku') ?>" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    Reset Filter
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModal('modalFilterBuku')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-navy-800 hover:bg-navy-900 text-white rounded-lg transition-colors">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH BUKU BARU                                                    -->
<!-- ========================================================================= -->
<div id="modalTambahBuku" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 max-h-[90vh] flex flex-col">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Input Koleksi Buku Baru</h4>
            </div>
            <button onclick="closeModal('modalTambahBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formTambahBuku" action="<?= site_url('/buku/store') ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 overflow-y-auto text-xs">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kode Buku (Otomatis)</label>
                    <input type="text" id="tambah_buku_kode" name="kode_buku" value="<?= esc($newKode) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-slate-100 dark:bg-slate-800 font-mono text-slate-700 dark:text-slate-300" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">ISBN</label>
                    <input type="text" id="tambah_buku_isbn" name="isbn" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none" placeholder="978-623-...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                <input type="text" id="tambah_buku_judul" name="judul" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none" placeholder="Masukkan judul buku...">
            </div>

            <!-- Upload Cover Buku -->
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sampul / Cover Buku (Opsional)</label>
                <input type="file" id="tambah_buku_cover" name="cover_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 cursor-pointer">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Format: JPG, PNG, WEBP</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Penulis <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah_buku_penulis" name="penulis" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none" placeholder="Nama pengarang">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Penerbit <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah_buku_penerbit" name="penerbit" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none" placeholder="Nama penerbit">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select id="tambah_buku_kategori" name="kategori_id" required class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= esc($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Terbit</label>
                    <input type="number" id="tambah_buku_tahun" name="tahun_terbit" min="1900" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Total Eksemplar <span class="text-rose-500">*</span></label>
                    <input type="number" id="tambah_buku_eksemplar" name="jumlah_eksemplar" min="1" value="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Lokasi Rak</label>
                    <input type="text" id="tambah_buku_rak" name="lokasi_rak" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none" placeholder="Cth: Rak Cerita-A1">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Status Buku</label>
                    <select id="tambah_buku_status" name="status" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="aktif">Aktif (Dapat Dipinjam)</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sinopsis / Catatan Deskripsi</label>
                <textarea id="tambah_buku_deskripsi" name="deskripsi" rows="2" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none" placeholder="Keterangan singkat tentang isi bacaan buku..."></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalTambahBuku')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewTambahBuku()" class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold bg-navy-800 hover:bg-navy-900 text-white rounded-lg transition-colors">
                    <span>Lanjutkan & Review</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL REVIEW DATA SEBELUM SIMPAN BUKU                                      -->
<!-- ========================================================================= -->
<div id="modalReviewTambahBuku" class="fixed inset-0 z-[60] bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col text-xs max-h-[92vh]">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Review Data Buku Baru</h4>
            </div>
            <button type="button" onclick="closeModal('modalReviewTambahBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5 overflow-y-auto">
            <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-[11px] flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span>Periksa kembali data buku sebelum disimpan ke katalog.</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-3.5 space-y-2">
                <div>
                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Judul Buku</span>
                    <span id="rev_buku_judul" class="font-bold text-slate-900 dark:text-white text-xs"></span>
                </div>
                <div class="pt-2 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Kode Buku</span>
                        <span id="rev_buku_kode" class="font-mono font-bold text-navy-900 dark:text-sky-300"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Kategori</span>
                        <span id="rev_buku_kategori" class="font-semibold text-slate-700 dark:text-slate-300"></span>
                    </div>
                </div>
                <div class="pt-2 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Penulis</span>
                        <span id="rev_buku_penulis" class="text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Penerbit</span>
                        <span id="rev_buku_penerbit" class="text-slate-800 dark:text-slate-200"></span>
                    </div>
                </div>
                <div class="pt-2 grid grid-cols-3 gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">ISBN</span>
                        <span id="rev_buku_isbn" class="font-mono text-slate-700 dark:text-slate-300"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Tahun</span>
                        <span id="rev_buku_tahun" class="text-slate-700 dark:text-slate-300"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Total Fisik</span>
                        <span id="rev_buku_eksemplar" class="font-bold text-emerald-600 dark:text-emerald-400"></span>
                    </div>
                </div>
                <div class="pt-2 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Lokasi Rak</span>
                        <span id="rev_buku_rak" class="text-slate-700 dark:text-slate-300"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Sampul</span>
                        <span id="rev_buku_cover" class="text-slate-700 dark:text-slate-300 truncate block"></span>
                    </div>
                </div>
                <div class="pt-2">
                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Sinopsis</span>
                    <p id="rev_buku_deskripsi" class="text-slate-600 dark:text-slate-300 italic text-[11px] mt-0.5"></p>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between gap-2">
                <button type="button" onclick="closeModal('modalReviewTambahBuku')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    &larr; Ubah
                </button>
                <button type="button" id="btnSubmitTambahBuku" onclick="submitFormTambahBuku(this)" class="px-4 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Konfirmasi & Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT BUKU                                                           -->
<!-- ========================================================================= -->
<div id="modalEditBuku" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Ubah Informasi Koleksi Buku</h4>
            </div>
            <button type="button" onclick="closeModal('modalEditBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formEditBuku" action="" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 overflow-y-auto text-xs">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kode Buku</label>
                    <input type="text" id="edit_kode_buku" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-slate-100 dark:bg-slate-800 font-mono text-slate-600 dark:text-slate-400 font-bold" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">ISBN</label>
                    <input type="text" id="edit_isbn" name="isbn" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Judul Buku *</label>
                <input type="text" id="edit_judul" name="judul" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none font-medium">
            </div>

            <!-- Edit Cover Buku -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 space-y-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Ganti Sampul / Cover Buku</label>
                <div class="flex items-center gap-3">
                    <img id="edit_cover_thumb" src="" alt="Cover saat ini" class="w-10 h-14 object-cover rounded border border-slate-200 dark:border-slate-700 hidden">
                    <input type="file" name="cover_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 cursor-pointer">
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500">Biarkan kosong jika tidak ingin mengubah sampul saat ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Penulis *</label>
                    <input type="text" id="edit_penulis" name="penulis" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Penerbit *</label>
                    <input type="text" id="edit_penerbit" name="penerbit" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kategori *</label>
                    <select id="edit_kategori_id" name="kategori_id" required class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= esc($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Terbit</label>
                    <input type="number" id="edit_tahun_terbit" name="tahun_terbit" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Total Eksemplar *</label>
                    <input type="number" id="edit_jumlah_eksemplar" name="jumlah_eksemplar" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Lokasi Rak</label>
                    <input type="text" id="edit_lokasi_rak" name="lokasi_rak" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Status</label>
                    <select id="edit_status" name="status" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                        <option value="aktif">Aktif (Dapat Dipinjam)</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sinopsis / Catatan</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="2" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modalEditBuku')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-navy-800 hover:bg-navy-900 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL IMPORT BUKU DARI EXCEL / CSV                                        -->
<!-- ========================================================================= -->
<div id="modalImportBuku" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="file-up" class="w-4 h-4 text-emerald-400"></i>
                <h4 class="font-serif font-bold text-sm">Import Koleksi Buku Massal</h4>
            </div>
            <button type="button" onclick="closeModal('modalImportBuku')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/buku/import') ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">
            <?= csrf_field() ?>

            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-slate-500"></i>
                        Petunjuk Format File:
                    </span>
                    <a href="<?= site_url('/buku/template-import') ?>" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-semibold rounded text-[11px] transition-colors">
                        <i data-lucide="download" class="w-3 h-3"></i>
                        <span>Template CSV</span>
                    </a>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    1. Unduh template CSV di atas dan buka dengan Excel / Google Sheets.<br>
                    2. Masukkan judul, penulis, penerbit, tahun, kategori, eksemplar, dan rak.<br>
                    3. Simpan sebagai format <strong>CSV (Comma Delimited)</strong> lalu unggah di bawah.
                </p>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih File CSV (.csv):</label>
                <input type="file" name="file_csv" accept=".csv,text/csv" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200">
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalImportBuku')" class="px-3.5 py-1.5 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentSelectedBuku = null;

    function viewBuku(data) {
        currentSelectedBuku = data;
        document.getElementById('view_kode_buku_badge').textContent = data.kode_buku;
        document.getElementById('view_kategori_badge').textContent = data.nama_kategori || 'Umum';
        
        // Status pill
        const statusPill = document.getElementById('view_status_pill');
        if (data.status === 'aktif') {
            statusPill.textContent = 'Aktif (Dapat Dipinjam)';
            statusPill.className = 'font-bold px-2 py-0.5 rounded text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800';
        } else {
            statusPill.textContent = 'Nonaktif';
            statusPill.className = 'font-bold px-2 py-0.5 rounded text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300';
        }

        document.getElementById('view_judul').textContent = data.judul;
        document.getElementById('view_penulis').textContent = data.penulis;
        document.getElementById('view_isbn').textContent = data.isbn || '-';
        document.getElementById('view_penerbit').textContent = data.penerbit;
        document.getElementById('view_tahun_terbit').textContent = data.tahun_terbit || '-';
        document.getElementById('view_lokasi_rak').textContent = data.lokasi_rak || 'Belum diatur';
        document.getElementById('view_stok_tersedia_num').textContent = data.stok_tersedia;
        document.getElementById('view_total_eksemplar_text').textContent = data.jumlah_eksemplar + ' eks';
        document.getElementById('view_deskripsi').textContent = data.deskripsi || 'Tidak ada catatan sinopsis khusus untuk buku ini.';

        // Cover image display
        const coverImg = document.getElementById('view_cover_img');
        const coverPlaceholder = document.getElementById('view_cover_placeholder');
        if (data.cover) {
            coverImg.src = '<?= base_url() ?>/' + data.cover;
            coverImg.classList.remove('hidden');
            coverPlaceholder.classList.add('hidden');
            coverPlaceholder.classList.remove('flex');
        } else {
            coverImg.src = '';
            coverImg.classList.add('hidden');
            coverPlaceholder.classList.remove('hidden');
            coverPlaceholder.classList.add('flex');
        }

        openModal('modalViewBuku');
    }

    document.getElementById('btnSwitchToEdit')?.addEventListener('click', function() {
        if (currentSelectedBuku) {
            closeModal('modalViewBuku');
            setTimeout(() => {
                editBuku(currentSelectedBuku);
            }, 150);
        }
    });

    function editBuku(data) {
        document.getElementById('formEditBuku').action = '<?= site_url('/buku/update/') ?>' + data.id;
        document.getElementById('edit_kode_buku').value = data.kode_buku;
        document.getElementById('edit_isbn').value = data.isbn || '';
        document.getElementById('edit_judul').value = data.judul;
        document.getElementById('edit_penulis').value = data.penulis;
        document.getElementById('edit_penerbit').value = data.penerbit;
        document.getElementById('edit_kategori_id').value = data.kategori_id;
        document.getElementById('edit_tahun_terbit').value = data.tahun_terbit || '';
        document.getElementById('edit_jumlah_eksemplar').value = data.jumlah_eksemplar;
        document.getElementById('edit_lokasi_rak').value = data.lokasi_rak || '';
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';

        const thumb = document.getElementById('edit_cover_thumb');
        if (data.cover) {
            thumb.src = '<?= base_url() ?>/' + data.cover;
            thumb.classList.remove('hidden');
        } else {
            thumb.src = '';
            thumb.classList.add('hidden');
        }

        openModal('modalEditBuku');
    }

    function reviewTambahBuku() {
        const form = document.getElementById('formTambahBuku');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const judul = document.getElementById('tambah_buku_judul').value.trim();
        const kode = document.getElementById('tambah_buku_kode').value.trim();
        const isbn = document.getElementById('tambah_buku_isbn').value.trim() || '-';
        const penulis = document.getElementById('tambah_buku_penulis').value.trim();
        const penerbit = document.getElementById('tambah_buku_penerbit').value.trim();
        const katSelect = document.getElementById('tambah_buku_kategori');
        const kategori = katSelect.options[katSelect.selectedIndex]?.text || '-';
        const tahun = document.getElementById('tambah_buku_tahun').value.trim() || '-';
        const eksemplar = document.getElementById('tambah_buku_eksemplar').value.trim() + ' eks';
        const rak = document.getElementById('tambah_buku_rak').value.trim() || 'Belum diatur';
        const deskripsi = document.getElementById('tambah_buku_deskripsi').value.trim() || 'Tidak ada catatan khusus.';

        const coverInput = document.getElementById('tambah_buku_cover');
        let coverName = 'Tidak mengunggah sampul';
        if (coverInput.files && coverInput.files.length > 0) {
            coverName = coverInput.files[0].name + ' (' + Math.round(coverInput.files[0].size / 1024) + ' KB)';
        }

        document.getElementById('rev_buku_judul').textContent = judul;
        document.getElementById('rev_buku_kode').textContent = kode;
        document.getElementById('rev_buku_kategori').textContent = kategori;
        document.getElementById('rev_buku_penulis').textContent = penulis;
        document.getElementById('rev_buku_penerbit').textContent = penerbit;
        document.getElementById('rev_buku_isbn').textContent = isbn;
        document.getElementById('rev_buku_tahun').textContent = tahun;
        document.getElementById('rev_buku_eksemplar').textContent = eksemplar;
        document.getElementById('rev_buku_rak').textContent = rak;
        document.getElementById('rev_buku_cover').textContent = coverName;
        document.getElementById('rev_buku_deskripsi').textContent = deskripsi;

        openModal('modalReviewTambahBuku');
    }

    function submitFormTambahBuku(btn) {
        const targetBtn = btn || document.getElementById('btnSubmitTambahBuku');
        if (window.setButtonLoading && !window.setButtonLoading(targetBtn, 'Menyimpan Buku...')) {
            return;
        }
        document.getElementById('formTambahBuku').submit();
    }

    // =========================================================================
    // FITUR CETAK LABEL MULTI-SELECT & FLOATING BAR
    // =========================================================================
    function toggleSelectAllBuku(master) {
        const checkboxes = document.querySelectorAll('.check-buku');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateFloatingBar();
    }

    function updateFloatingBar() {
        const checked = document.querySelectorAll('.check-buku:checked');
        const count = checked.length;
        const bar = document.getElementById('floatingBar');
        const countEl = document.getElementById('selectedCount');
        const optTerpilih = document.getElementById('optSumberTerpilih');
        const labelTerpilihText = document.getElementById('lblTerpilihText');

        if (countEl) countEl.textContent = count;
        if (labelTerpilihText) labelTerpilihText.textContent = `Buku yang Dicentang (${count} buku)`;

        if (count > 0) {
            bar?.classList.remove('hidden');
            if (optTerpilih) optTerpilih.disabled = false;
        } else {
            bar?.classList.add('hidden');
            const radioTerpilih = document.getElementById('sumber_terpilih');
            if (radioTerpilih && radioTerpilih.checked) {
                document.getElementById('sumber_semua').checked = true;
            }
            if (optTerpilih) optTerpilih.disabled = true;
        }
    }

    function clearSelectedBuku() {
        document.querySelectorAll('.check-buku').forEach(cb => cb.checked = false);
        const master = document.getElementById('checkAllBuku');
        if (master) master.checked = false;
        updateFloatingBar();
    }

    function openModalCetakLabel(forceSelected = false) {
        const checked = document.querySelectorAll('.check-buku:checked');
        if (forceSelected || checked.length > 0) {
            document.getElementById('sumber_terpilih').checked = true;
        }
        openModal('modalCetakLabel');
    }

    function submitCetakLabel() {
        const sumber = document.querySelector('input[name="sumber_label"]:checked')?.value || 'semua';
        const labelType = document.querySelector('input[name="modal_label_type"]:checked')?.value || 'all';
        const qtyMode = document.querySelector('input[name="modal_qty_mode"]:checked')?.value || 'single';
        const katId = document.getElementById('label_kategori_id')?.value || '';

        let url = '<?= site_url('/buku/cetak-label') ?>?label_type=' + labelType + '&qty_mode=' + qtyMode;

        if (sumber === 'terpilih') {
            const checked = document.querySelectorAll('.check-buku:checked');
            const ids = Array.from(checked).map(cb => cb.value);
            if (ids.length === 0) {
                alert('Silakan centang minimal satu buku terlebih dahulu.');
                return;
            }
            url += '&ids=' + ids.join(',');
        } else if (sumber === 'kategori' && katId) {
            url += '&kategori_id=' + katId;
        }

        closeModal('modalCetakLabel');
        window.open(url, '_blank');
    }
</script>

<!-- FLOATING ACTION BAR MULTI-SELECT -->
<div id="floatingBar" class="fixed bottom-6 inset-x-0 mx-auto max-w-xl bg-[#101B30] text-white p-3 px-5 rounded-xl shadow-xl flex items-center justify-between z-40 border border-slate-700 hidden transition-all">
    <div class="flex items-center gap-2.5">
        <span class="w-6 h-6 rounded bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-xs" id="selectedCount">0</span>
        <span class="text-xs font-medium text-slate-200">Buku Dipilih untuk Dicetak</span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" onclick="openModalCetakLabel(true)" class="px-3 py-1.5 bg-terracotta-500 hover:bg-terracotta-600 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
            <span>Cetak Label Terpilih</span>
        </button>
        <button type="button" onclick="clearSelectedBuku()" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg transition-colors">
            Batal
        </button>
    </div>
</div>

<!-- MODAL OPSIONAL CETAK LABEL BUKU -->
<div id="modalCetakLabel" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-lg overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4 text-amber-400"></i>
                <div>
                    <h3 class="font-serif font-bold text-sm">Opsi Cetak Label Buku</h3>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalCetakLabel')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form Options Body -->
        <div class="p-5 space-y-4 text-xs">
            <!-- Pilihan 1: Sumber Buku -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider text-[10px]">
                    1. Pilihan Buku yang Dicetak
                </label>
                <div class="space-y-1.5">
                    <label id="optSumberTerpilih" class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="radio" name="sumber_label" id="sumber_terpilih" value="terpilih" class="text-navy-800 focus:ring-navy-800">
                        <span class="font-medium text-slate-800 dark:text-slate-200" id="lblTerpilihText">Buku yang Dicentang (0 buku)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="radio" name="sumber_label" id="sumber_semua" value="semua" checked class="text-navy-800 focus:ring-navy-800">
                        <span class="font-medium text-slate-800 dark:text-slate-200">Seluruh Koleksi Buku Aktif (Semua)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="radio" name="sumber_label" id="sumber_kategori" value="kategori" class="text-navy-800 focus:ring-navy-800">
                        <div class="flex-1 flex items-center justify-between gap-2">
                            <span class="font-medium text-slate-800 dark:text-slate-200">Berdasarkan Kategori:</span>
                            <select id="label_kategori_id" class="px-2 py-1 text-xs border rounded bg-white dark:bg-slate-800 dark:border-slate-700 text-slate-700 dark:text-slate-200">
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pilihan 2: Format / Tipe Label -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider text-[10px]">
                    2. Format Desain Label
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-center flex flex-col items-center gap-1 transition-colors">
                        <input type="radio" name="modal_label_type" value="all" checked class="text-navy-800 focus:ring-navy-800">
                        <span class="font-bold text-[11px] text-slate-800 dark:text-slate-200">Lengkap</span>
                        <span class="text-[9px] text-slate-400">Punggung + Barcode</span>
                    </label>
                    <label class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-center flex flex-col items-center gap-1 transition-colors">
                        <input type="radio" name="modal_label_type" value="spine" class="text-navy-800 focus:ring-navy-800">
                        <span class="font-bold text-[11px] text-slate-800 dark:text-slate-200">Punggung</span>
                        <span class="text-[9px] text-slate-400">Call Number</span>
                    </label>
                    <label class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-center flex flex-col items-center gap-1 transition-colors">
                        <input type="radio" name="modal_label_type" value="barcode" class="text-navy-800 focus:ring-navy-800">
                        <span class="font-bold text-[11px] text-slate-800 dark:text-slate-200">Barcode</span>
                        <span class="text-[9px] text-slate-400">Barcode & Judul</span>
                    </label>
                </div>
            </div>

            <!-- Pilihan 3: Jumlah Salinan Label -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider text-[10px]">
                    3. Jumlah Salinan Label
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer flex items-center gap-2 transition-colors">
                        <input type="radio" name="modal_qty_mode" value="single" checked class="text-navy-800 focus:ring-navy-800">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-[11px]">1 Label per Judul</span>
                            <span class="text-[9px] text-slate-400">Hemat stiker</span>
                        </div>
                    </label>
                    <label class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer flex items-center gap-2 transition-colors">
                        <input type="radio" name="modal_qty_mode" value="eksemplar" class="text-navy-800 focus:ring-navy-800">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-[11px]">Sesuai Eksemplar</span>
                            <span class="text-[9px] text-slate-400">c.1, c.2, dst.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 px-5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
            <button type="button" onclick="closeModal('modalCetakLabel')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                Batal
            </button>
            <button type="button" onclick="submitCetakLabel()" class="px-4 py-1.5 bg-navy-800 hover:bg-navy-900 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Buka Pratinjau & Cetak</span>
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
