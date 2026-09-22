<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header Section with Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#131B2E] p-5 sm:p-6 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-[#C1613A] flex items-center justify-center text-white shadow-sm shrink-0">
                <i data-lucide="book-marked" class="w-5 h-5 text-amber-100"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-[#1B2A4A] dark:text-slate-100">Laporan Inventaris Koleksi Buku SD</h3>
                <p class="text-xs text-[#5C6470] dark:text-slate-400 mt-0.5">Daftar ketersediaan buku tematik, fiksi anak, dan eksemplar fisik perpustakaan SDN 12 Sumbawa</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="<?= site_url('/laporan/semua') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Semua Laporan
            </a>
            <a href="<?= site_url('/laporan/peminjaman') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Laporan Peminjaman
            </a>
            <a href="<?= site_url('/laporan/pengembalian') ?>" class="px-3 py-1.5 bg-[#F8F6F0] dark:bg-slate-800 hover:bg-[#EFECE6] dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 text-xs font-semibold rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors">
                Laporan Pengembalian
            </a>
            <a href="<?= site_url('/laporan/buku') ?>" class="px-3 py-1.5 bg-[#1B2A4A] text-white text-xs font-semibold rounded-lg shadow-sm border border-[#1B2A4A]">
                Laporan Koleksi Buku
            </a>
        </div>
    </div>

    <!-- Filter & Action Card (FR-24, FR-25) -->
    <div class="bg-white dark:bg-[#131B2E] p-5 sm:p-6 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm">
        <form action="<?= site_url('/laporan/buku') ?>" method="GET" class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                <div>
                    <label class="block text-[11px] font-semibold text-[#5C6470] dark:text-slate-300 uppercase tracking-wider mb-1.5">Filter Kategori</label>
                    <select name="kategori_id" class="w-full px-3.5 py-2 rounded-lg border border-[#E5DFD3] dark:border-slate-700 bg-[#FBF9F5] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#1B2A4A]/20 focus:border-[#1B2A4A] focus:outline-none transition-all">
                        <option value="">Semua Kategori Buku</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ($selectedKat ?? '') == $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#5C6470] dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Ketersediaan</label>
                    <select name="status" class="w-full px-3.5 py-2 rounded-lg border border-[#E5DFD3] dark:border-slate-700 bg-[#FBF9F5] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#1B2A4A]/20 focus:border-[#1B2A4A] focus:outline-none transition-all">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?= ($selectedStat ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif (Dapat Dipinjam)</option>
                        <option value="nonaktif" <?= ($selectedStat ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2 lg:pt-0 flex-wrap">
                <button type="submit" class="px-4 py-2 bg-[#1B2A4A] hover:bg-[#24375D] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 shadow-sm transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Filter</span>
                </button>

                <!-- 3 Export Options -->
                <a href="<?= site_url('/laporan/pdf/buku?kategori_id=' . urlencode($selectedKat ?? '') . '&status=' . urlencode($selectedStat ?? '')) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#C1613A] hover:bg-[#A95330] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format PDF">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>PDF</span>
                </a>

                <a href="<?= site_url('/laporan/word/buku?kategori_id=' . urlencode($selectedKat ?? '') . '&status=' . urlencode($selectedStat ?? '')) ?>"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2B4C7E] hover:bg-[#1E365B] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format Microsoft Word (.doc)">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Word</span>
                </a>

                <a href="<?= site_url('/laporan/excel/buku?kategori_id=' . urlencode($selectedKat ?? '') . '&status=' . urlencode($selectedStat ?? '')) ?>"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2F6E4E] hover:bg-[#25573E] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
                   title="Export Format Microsoft Excel (.xls)">
                    <i data-lucide="sheet" class="w-3.5 h-3.5"></i>
                    <span>Excel</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Preview Table Section -->
    <div class="bg-white dark:bg-[#131B2E] rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8F6F0] dark:bg-slate-800/80 text-[#5C6470] dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-[#E5DFD3] dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5 w-12 text-center">No</th>
                        <th class="px-5 py-3.5 text-center">Kode Buku</th>
                        <th class="px-5 py-3.5">Judul Buku & Penulis</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Penerbit & Tahun</th>
                        <th class="px-5 py-3.5 text-center">Total Eksemplar</th>
                        <th class="px-5 py-3.5 text-center">Stok Tersedia</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5DFD3]/60 dark:divide-slate-800/60">
                    <?php if (empty($dataLaporan)): ?>
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-[#5C6470] dark:text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600"></i>
                                Tidak ada data buku yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dataLaporan as $b): ?>
                            <tr class="hover:bg-[#F8F6F0]/50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 text-center text-[#5C6470] font-mono"><?= $no++ ?></td>
                                <td class="px-5 py-3.5 font-mono font-bold text-[#1B2A4A] dark:text-slate-200 text-center"><?= esc($b['kode_buku']) ?></td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    <div class="font-bold text-[#1B2A4A] dark:text-slate-100"><?= esc($b['judul']) ?></div>
                                    <div class="text-[10px] text-[#5C6470] dark:text-slate-400">Penulis: <?= esc($b['penulis']) ?></div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-[#F8F6F0] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-300 border border-[#E5DFD3] dark:border-slate-700">
                                        <?= esc($b['nama_kategori'] ?: 'Umum') ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-[#5C6470] dark:text-slate-400">
                                    <?= esc($b['penerbit']) ?> (<?= esc($b['tahun_terbit'] ?: '-') ?>)
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-[#1B2A4A] dark:text-slate-300">
                                    <?= $b['jumlah_eksemplar'] ?>
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold <?= $b['stok_tersedia'] > 0 ? 'text-[#2F6E4E] dark:text-emerald-400' : 'text-[#C1613A] dark:text-rose-400' ?>">
                                    <?= $b['stok_tersedia'] ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($b['status'] === 'aktif'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-[#2F6E4E] dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick='viewDetailBuku(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center justify-center p-1.5 text-[#1B2A4A] dark:text-slate-300 hover:text-white bg-[#F8F6F0] hover:bg-[#1B2A4A] dark:bg-slate-800 dark:hover:bg-slate-700 border border-[#E5DFD3] dark:border-slate-700 rounded-lg transition-colors shadow-xs" 
                                                title="Lihat Detail Buku">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <a href="<?= site_url('/buku/cetak-label/' . $b['id']) ?>" 
                                           target="_blank"
                                           class="inline-flex items-center justify-center p-1.5 text-[#1B2A4A] dark:text-slate-300 hover:text-white bg-[#F8F6F0] hover:bg-[#1B2A4A] dark:bg-slate-800 dark:hover:bg-slate-700 border border-[#E5DFD3] dark:border-slate-700 rounded-lg transition-colors shadow-xs" 
                                           title="Cetak Label Punggung & Barcode">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
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
</div>

<!-- Modal View Detail Buku Laporan -->
<div id="modalViewBukuLaporan" class="fixed inset-0 z-50 bg-[#1B2A4A]/60 hidden items-center justify-center p-4 transition-all">
    <div class="bg-white dark:bg-[#131B2E] w-full max-w-lg rounded-xl shadow-xl overflow-hidden border border-[#E5DFD3] dark:border-slate-800 flex flex-col text-xs">
        <div class="px-5 py-4 bg-[#1B2A4A] text-white flex items-center justify-between border-b border-[#1B2A4A] shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-400">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm leading-tight text-white">Detail Koleksi Buku</h4>
                    <p class="text-[11px] text-slate-300">Rincian data inventaris koleksi perpustakaan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalViewBukuLaporan')" class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-4 overflow-y-auto max-h-[80vh]">
            <div class="flex gap-4 items-center p-3.5 rounded-lg bg-[#F8F6F0] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700">
                <div class="w-14 h-18 rounded-md bg-[#EFECE6] dark:bg-slate-700 shrink-0 overflow-hidden flex items-center justify-center border border-[#E5DFD3] dark:border-slate-600">
                    <img id="vb_cover" src="" alt="Cover" class="w-full h-full object-cover hidden">
                    <i id="vb_fallback" data-lucide="book" class="w-7 h-7 text-slate-400"></i>
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <span id="vb_kode" class="font-mono font-bold text-[#1B2A4A] dark:text-indigo-400 text-xs"></span>
                    <h4 id="vb_judul" class="font-serif font-bold text-[#1B2A4A] dark:text-white text-sm line-clamp-2"></h4>
                    <p id="vb_penulis" class="text-[#5C6470] dark:text-slate-400 text-[11px]"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-[#F8F6F0] dark:bg-slate-800/50 border border-[#E5DFD3] dark:border-slate-700/70">
                    <span class="text-[10px] text-[#5C6470] dark:text-slate-400 uppercase font-bold block">Kategori</span>
                    <span id="vb_kategori" class="font-semibold text-[#1B2A4A] dark:text-slate-200"></span>
                </div>
                <div class="p-3 rounded-lg bg-[#F8F6F0] dark:bg-slate-800/50 border border-[#E5DFD3] dark:border-slate-700/70">
                    <span class="text-[10px] text-[#5C6470] dark:text-slate-400 uppercase font-bold block">Penerbit & Tahun</span>
                    <span id="vb_penerbit" class="font-semibold text-[#1B2A4A] dark:text-slate-200"></span>
                </div>
                <div class="p-3 rounded-lg bg-[#F8F6F0] dark:bg-slate-800/50 border border-[#E5DFD3] dark:border-slate-700/70">
                    <span class="text-[10px] text-[#5C6470] dark:text-slate-400 uppercase font-bold block">ISBN / Barcode</span>
                    <span id="vb_isbn" class="font-mono font-semibold text-[#1B2A4A] dark:text-slate-200"></span>
                </div>
                <div class="p-3 rounded-lg bg-[#F8F6F0] dark:bg-slate-800/50 border border-[#E5DFD3] dark:border-slate-700/70">
                    <span class="text-[10px] text-[#5C6470] dark:text-slate-400 uppercase font-bold block">Lokasi Rak</span>
                    <span id="vb_rak" class="font-semibold text-[#B8791F] dark:text-amber-400"></span>
                </div>
            </div>

            <div class="p-3 rounded-lg bg-[#F8F6F0] dark:bg-slate-800/50 border border-[#E5DFD3] dark:border-slate-700/70 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-[#5C6470] dark:text-slate-400 uppercase font-bold block">Stok Eksemplar</span>
                    <span id="vb_stok" class="font-bold text-xs text-[#1B2A4A] dark:text-slate-200"></span>
                </div>
                <span id="vb_status_badge"></span>
            </div>
        </div>

        <div class="px-5 py-3 bg-[#F8F6F0] dark:bg-slate-800 border-t border-[#E5DFD3] dark:border-slate-800 flex justify-end">
            <button type="button" onclick="closeModal('modalViewBukuLaporan')" class="px-4 py-2 text-xs font-semibold bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-[#1B2A4A] dark:text-slate-100 rounded-lg border border-[#E5DFD3] dark:border-slate-600 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function viewDetailBuku(b) {
        document.getElementById('vb_kode').textContent = b.kode_buku;
        document.getElementById('vb_judul').textContent = b.judul;
        document.getElementById('vb_penulis').textContent = 'Penulis: ' + (b.penulis || '-');
        document.getElementById('vb_kategori').textContent = b.nama_kategori || 'Umum';
        document.getElementById('vb_penerbit').textContent = (b.penerbit || '-') + ' (' + (b.tahun_terbit || '-') + ')';
        document.getElementById('vb_isbn').textContent = b.isbn || '-';
        document.getElementById('vb_rak').textContent = b.lokasi_rak || 'Rak Pustaka';
        document.getElementById('vb_stok').textContent = b.stok_tersedia + ' Tersedia dari ' + b.jumlah_eksemplar + ' Eksemplar';

        const badge = document.getElementById('vb_status_badge');
        if (b.status === 'aktif') {
            badge.innerHTML = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-[#2F6E4E] border border-emerald-200">Aktif</span>';
        } else {
            badge.innerHTML = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">Nonaktif</span>';
        }

        const coverImg = document.getElementById('vb_cover');
        const fallback = document.getElementById('vb_fallback');
        if (b.cover) {
            coverImg.src = '<?= base_url() ?>/' + b.cover;
            coverImg.classList.remove('hidden');
            fallback.classList.add('hidden');
        } else {
            coverImg.classList.add('hidden');
            fallback.classList.remove('hidden');
        }

        if (window.lucide) lucide.createIcons();
        openModal('modalViewBukuLaporan');
    }
</script>

<?= $this->endSection() ?>
