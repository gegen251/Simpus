<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto space-y-5">
    <!-- Back & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="<?= site_url('/peminjaman') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Peminjaman</span>
        </a>

        <button onclick="window.print()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak Bukti Struk</span>
        </button>
    </div>

    <!-- Printable Receipt Card -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs p-8 relative overflow-hidden print:border-none print:shadow-none transition-colors">
        <!-- Top Institutional Header -->
        <div class="text-center pb-6 border-b-2 border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-12 h-12 rounded-lg bg-slate-50 dark:bg-slate-800 p-2 flex items-center justify-center mx-auto mb-2.5 border border-slate-200 dark:border-slate-700">
                <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
            </div>
            <h2 class="font-serif font-bold text-lg text-navy-900 dark:text-white">SD NEGERI 12 SUMBAWA</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Sistem Informasi Perpustakaan (SIMPUS)</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500">Bukti Transaksi Peminjaman Koleksi Buku</p>
            <div class="mt-3 inline-block font-mono text-xs px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-md text-slate-800 dark:text-slate-200 font-bold border border-slate-200 dark:border-slate-700">
                <?= esc($detail['kode_transaksi']) ?>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-5 border-b border-slate-100 dark:border-slate-800 text-xs">
            <div class="space-y-1.5">
                <span class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block text-[10px]">Identitas Peminjam</span>
                <div class="font-bold text-sm text-slate-900 dark:text-slate-100"><?= esc($detail['nama_anggota']) ?></div>
                <div class="text-slate-600 dark:text-slate-400 font-mono">No. Anggota: <?= esc($detail['nomor_anggota']) ?></div>
                <div class="text-slate-600 dark:text-slate-400">Kontak: <?= esc($detail['kontak_anggota']) ?></div>
            </div>
            <div class="space-y-1.5 sm:text-right">
                <span class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block text-[10px]">Status &amp; Petugas</span>
                <div>
                    <?php if ($detail['status'] === 'dipinjam'): ?>
                        <span class="px-2.5 py-0.5 rounded-md font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 text-[10px] border border-sky-200 dark:border-sky-800">Sedang Dipinjam</span>
                    <?php elseif ($detail['status'] === 'terlambat'): ?>
                        <span class="px-2.5 py-0.5 rounded-md font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-[10px] border border-amber-200 dark:border-amber-800">Terlambat</span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 rounded-md font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[10px] border border-emerald-200 dark:border-emerald-800">Sudah Dikembalikan</span>
                    <?php endif; ?>
                </div>
                <div class="text-slate-600 dark:text-slate-400 mt-1">Dicatat oleh: <strong><?= esc($detail['nama_admin'] ?: 'Administrator') ?></strong></div>
                <div class="text-slate-400 dark:text-slate-500 text-[10px] font-mono">Waktu: <?= date('d F Y H:i', strtotime($detail['created_at'])) ?> WITA</div>
            </div>
        </div>

        <!-- Book Details Box -->
        <div class="py-5 border-b border-slate-100 dark:border-slate-800">
            <h4 class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2.5">Detail Buku Dipinjam</h4>
            <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="font-mono text-[10px] text-terracotta-700 dark:text-terracotta-400 font-bold block"><?= esc($detail['kode_buku']) ?></span>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mt-0.5"><?= esc($detail['judul_buku']) ?></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penulis: <?= esc($detail['penulis_buku']) ?> &bull; Kategori: <?= esc($detail['nama_kategori'] ?: 'Umum') ?></p>
                </div>
                <div class="text-left sm:text-right text-xs">
                    <span class="text-slate-400 dark:text-slate-500 text-[10px] block">Penempatan:</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300"><?= esc($detail['lokasi_rak'] ?: 'Rak Utama') ?></span>
                </div>
            </div>
        </div>

        <!-- Schedule Dates -->
        <div class="grid grid-cols-2 gap-3.5 py-5 border-b border-slate-100 dark:border-slate-800 text-xs">
            <div class="bg-slate-50 dark:bg-slate-800/60 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold">Tanggal Peminjaman</span>
                <span class="font-bold text-slate-800 dark:text-slate-200 text-xs font-mono mt-1 block">
                    <?= date('d F Y', strtotime($detail['tanggal_pinjam'])) ?>
                </span>
            </div>
            <div class="bg-amber-50/70 dark:bg-amber-950/30 p-3 rounded-lg border border-amber-200 dark:border-amber-800/60">
                <span class="text-amber-800 dark:text-amber-300 block text-[10px] uppercase font-bold">Batas Pengembalian (Jatuh Tempo)</span>
                <span class="font-bold text-amber-900 dark:text-amber-200 text-xs font-mono mt-1 block">
                    <?= date('d F Y', strtotime($detail['tanggal_jatuh_tempo'])) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($detail['catatan'])): ?>
            <div class="py-3.5 border-b border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold mb-1">Catatan Peminjaman</span>
                <p class="text-slate-600 dark:text-slate-400 italic"><?= esc($detail['catatan']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Warning & Signature footer -->
        <div class="pt-5 text-center space-y-4">
            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                Harap rawat buku dengan baik dan kembalikan sebelum atau tepat pada tanggal jatuh tempo. Keterlambatan dikenakan denda sesuai peraturan perpustakaan sekolah.
            </p>
            <div class="pt-5 grid grid-cols-2 text-xs text-slate-600 dark:text-slate-400">
                <div>
                    <p class="mb-14">Peminjam,</p>
                    <p class="font-bold underline text-slate-800 dark:text-slate-200"><?= esc($detail['nama_anggota']) ?></p>
                </div>
                <div>
                    <p class="mb-14">Petugas Layanan,</p>
                    <p class="font-bold underline text-slate-800 dark:text-slate-200"><?= esc($detail['nama_admin'] ?: 'Petugas Perpustakaan') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
