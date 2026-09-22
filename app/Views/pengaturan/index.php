<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto space-y-5">
    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-lg bg-navy-900 dark:bg-navy-800 flex items-center justify-center text-white shrink-0 shadow-xs">
            <i data-lucide="settings" class="w-5 h-5 text-amber-300"></i>
        </div>
        <div>
            <h3 class="font-serif font-bold text-lg text-navy-900 dark:text-slate-100">Pengaturan SIMPUS &amp; Kebijakan Sirkulasi</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi kebijakan peminjaman ramah anak, tarif denda edukatif, serta profil resmi Sekolah Dasar</p>
        </div>
    </div>

    <form action="<?= site_url('/pengaturan/update') ?>" method="POST" class="space-y-5">
        <?= csrf_field() ?>

        <!-- Card 1: Aturan Bisnis Sirkulasi -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm text-navy-900 dark:text-slate-100">Aturan &amp; Batasan Peminjaman Siswa SD</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Validasi peminjaman buku pelajaran &amp; cerita anak serta perhitungan denda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Durasi Peminjaman *</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="durasi_pinjam_default" min="1" max="60" value="<?= esc($pengaturan['durasi_pinjam_default'] ?? '5') ?>" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-semibold focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 pointer-events-none">Hari</span>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Rekomendasi SD: 5 hari (siklus mingguan)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="coins" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Tarif Denda (Per Hari) *</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-500 font-semibold pointer-events-none">Rp</span>
                        <input type="number" name="tarif_denda_per_hari" min="0" step="500" value="<?= esc($pengaturan['tarif_denda_per_hari'] ?? '500') ?>" required
                               class="w-full pl-8 pr-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-semibold focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Rekomendasi SD: Rp 500 / hari (edukatif)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Maksimal Buku Dipinjam *</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="max_pinjam_buku" min="1" max="10" value="<?= esc($pengaturan['max_pinjam_buku'] ?? '2') ?>" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-semibold focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 pointer-events-none">Buku</span>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Rekomendasi SD: 2 buku per siswa</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Identitas Instansi & Kop Laporan -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-sm text-navy-900 dark:text-slate-100">Identitas Sekolah Dasar &amp; Format Cetak Laporan</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Digunakan pada kop surat dan tanda tangan dokumen resmi PDF, Word &amp; Excel</p>
                </div>
            </div>

            <div class="space-y-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="school" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Nama Instansi Perpustakaan *</span>
                    </label>
                    <input type="text" name="nama_perpustakaan" value="<?= esc($pengaturan['nama_perpustakaan'] ?? 'SIMPUS - Sistem Informasi Manajemen Perpustakaan') ?>" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Alamat Lengkap &amp; Kontak *</span>
                    </label>
                    <textarea name="alamat_perpustakaan" rows="2" required
                              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none"><?= esc($pengaturan['alamat_perpustakaan'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Nama Kepala / Penanggung Jawab *</span>
                        </label>
                        <input type="text" name="kepala_perpustakaan" value="<?= esc($pengaturan['kepala_perpustakaan'] ?? 'Dr. H. Ahmad Dahlan, M.Pd') ?>" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i data-lucide="id-card" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>NIP / Nomor Identitas Pegawai</span>
                        </label>
                        <input type="text" name="nip_kepala" value="<?= esc($pengaturan['nip_kepala'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-1 focus:ring-navy-900 focus:border-navy-900 focus:outline-none font-mono">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-navy-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4 text-amber-300"></i>
                <span>Simpan Seluruh Pengaturan</span>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
