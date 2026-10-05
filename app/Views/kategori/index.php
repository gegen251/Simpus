<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-navy-800 text-white flex items-center justify-center shrink-0">
                <i data-lucide="tag" class="w-5 h-5 text-amber-400"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-slate-900 dark:text-white">Kategori Koleksi Buku SD</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Klasifikasi bacaan: buku pelajaran tematik, fabel, komik sains, dan budi pekerti</p>
            </div>
        </div>
        <button onclick="openModal('modalTambahKategori')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-navy-800 hover:bg-navy-900 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="plus" class="w-3.5 h-3.5 text-amber-400"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs">
        <!-- Quick Search Form -->
        <form action="<?= site_url('/kategori') ?>" method="GET" class="flex-1 max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama kategori atau subjek bacaan..."
                   class="w-full pl-9 pr-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none transition-colors">
            <?php if (!empty($order) && $order !== 'ASC'): ?>
                <input type="hidden" name="order" value="<?= esc($order) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openModal('modalFilterKategori')" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-2xs transition-colors">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Filter</span>
                <?php if (!empty($activeFilterCount)): ?>
                    <span class="w-4 h-4 rounded-full bg-navy-800 dark:bg-amber-500 text-white dark:text-slate-950 text-[10px] flex items-center justify-center font-bold">
                        <?= $activeFilterCount ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
    </div>

    <!-- Active Filter Tags Bar -->
    <?php if (!empty($activeFilterCount)): ?>
        <div class="flex flex-wrap items-center justify-between gap-2 px-3.5 py-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 shadow-2xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="font-semibold text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">Filter:</span>
                <?php if (!empty($keyword)): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Kata Kunci: "<strong><?= esc($keyword) ?></strong>"
                    </span>
                <?php endif; ?>
                <?php if ($order === 'DESC'): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">
                        Urutan: <strong>Z &rarr; A</strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('modalFilterKategori')" class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:underline">
                    Ubah Filter
                </button>
                <span class="text-slate-300 dark:text-slate-600">|</span>
                <a href="<?= site_url('/kategori') ?>" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    <i data-lucide="x" class="w-3 h-3"></i>
                    Reset Filter
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table of Categories -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3">Keterangan / Subjek</th>
                        <th class="px-4 py-3 text-center">Jumlah Judul Buku</th>
                        <th class="px-4 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($kategori)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i data-lucide="tag" class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-2"></i>
                                <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Belum ada data kategori yang sesuai</p>
                                <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci pada form pencarian.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($kategori as $kat): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 dark:text-slate-500 font-mono"><?= $no++ ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs"><?= esc($kat['nama_kategori']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 max-w-xs">
                                    <?= esc($kat['keterangan'] ?: '-') ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <?= (int)$kat['total_buku'] ?> Judul
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="editKategori(<?= htmlspecialchars(json_encode($kat)) ?>)" class="p-1.5 text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 dark:hover:bg-amber-900 border border-amber-200 dark:border-amber-800 rounded transition-colors" title="Edit">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="confirmDelete('<?= site_url('/kategori/delete/' . $kat['id']) ?>', 'Hapus kategori <?= esc(addslashes($kat['nama_kategori'])) ?>? Pastikan tidak ada buku yang masih terkait dengan kategori ini.')" class="p-1.5 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 border border-rose-200 dark:border-rose-800 rounded transition-colors" title="Hapus Kategori">
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
<!-- MODAL POP-UP FILTER KATEGORI                                              -->
<!-- ========================================================================= -->
<div id="modalFilterKategori" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Filter Kategori Koleksi</h4>
            </div>
            <button type="button" onclick="closeModal('modalFilterKategori')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= site_url('/kategori') ?>" method="GET" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kata Kunci (Nama / Deskripsi)</label>
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama kategori..."
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan Nama Kategori</label>
                <select name="order" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none">
                    <option value="ASC" <?= ($order ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Nama Kategori A &rarr; Z</option>
                    <option value="DESC" <?= ($order ?? '') === 'DESC' ? 'selected' : '' ?>>Nama Kategori Z &rarr; A</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <a href="<?= site_url('/kategori') ?>" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    Reset Filter
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModal('modalFilterKategori')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
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
<!-- MODAL TAMBAH KATEGORI                                                     -->
<!-- ========================================================================= -->
<div id="modalTambahKategori" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Tambah Kategori</h4>
            </div>
            <button onclick="closeModal('modalTambahKategori')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formTambahKategori" action="<?= site_url('/kategori/store') ?>" method="POST" class="p-5 space-y-3.5 text-xs">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="tambah_kat_nama" name="nama_kategori" required 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none" 
                       placeholder="Contoh: Tematik SD Kelas 1">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Keterangan / Cakupan Subjek
                </label>
                <textarea id="tambah_kat_keterangan" name="keterangan" rows="3" 
                          class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none" 
                          placeholder="Deskripsi ringkas cakupan materi..."></textarea>
            </div>
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalTambahKategori')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="button" onclick="reviewTambahKategori()" class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold bg-navy-800 hover:bg-navy-900 text-white rounded-lg transition-colors">
                    <span>Lanjutkan & Review</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL REVIEW DATA SEBELUM SIMPAN KATEGORI                                 -->
<!-- ========================================================================= -->
<div id="modalReviewTambahKategori" class="fixed inset-0 z-[60] bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Review Data Kategori</h4>
            </div>
            <button type="button" onclick="closeModal('modalReviewTambahKategori')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3.5">
            <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-[11px] flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span>Pastikan nama kategori dan subjek bacaan sudah tepat.</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-3.5 space-y-2">
                <div>
                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Nama Kategori</span>
                    <span id="rev_kat_nama" class="font-bold text-slate-900 dark:text-white text-xs"></span>
                </div>
                <div class="pt-2">
                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider block">Keterangan / Subjek</span>
                    <p id="rev_kat_keterangan" class="text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed"></p>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between gap-2">
                <button type="button" onclick="closeModal('modalReviewTambahKategori')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    &larr; Ubah
                </button>
                <button type="button" id="btnSubmitTambahKategori" onclick="submitFormTambahKategori(this)" class="px-4 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Konfirmasi & Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT KATEGORI                                                       -->
<!-- ========================================================================= -->
<div id="modalEditKategori" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="px-5 py-3.5 bg-[#101B30] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-amber-400"></i>
                <h4 class="font-serif font-bold text-sm">Ubah Kategori</h4>
            </div>
            <button onclick="closeModal('modalEditKategori')" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formEditKategori" action="" method="POST" class="p-5 space-y-3.5 text-xs">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_nama_kategori" name="nama_kategori" required 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Keterangan / Cakupan Subjek
                </label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" 
                          class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-800 focus:outline-none"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditKategori')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-navy-800 hover:bg-navy-900 text-white rounded-lg transition-colors">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editKategori(data) {
        document.getElementById('formEditKategori').action = '<?= site_url('/kategori/update/') ?>' + data.id;
        document.getElementById('edit_nama_kategori').value = data.nama_kategori;
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        openModal('modalEditKategori');
    }

    function reviewTambahKategori() {
        const form = document.getElementById('formTambahKategori');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const nama = document.getElementById('tambah_kat_nama').value.trim();
        const keterangan = document.getElementById('tambah_kat_keterangan').value.trim() || 'Tidak ada keterangan khusus.';

        document.getElementById('rev_kat_nama').textContent = nama;
        document.getElementById('rev_kat_keterangan').textContent = keterangan;

        openModal('modalReviewTambahKategori');
    }

    function submitFormTambahKategori(btn) {
        const targetBtn = btn || document.getElementById('btnSubmitTambahKategori');
        if (window.setButtonLoading && !window.setButtonLoading(targetBtn, 'Menyimpan Kategori...')) {
            return;
        }
        document.getElementById('formTambahKategori').submit();
    }
</script>

<?= $this->endSection() ?>
