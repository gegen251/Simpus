<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Mandiri Siswa - <?= esc($config['nama_perpustakaan'] ?? 'SIMPUS Perpustakaan SD Negeri 12 Sumbawa') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('images/logo.png') ?>">
    
    <!-- Google Fonts: IBM Plex Family (Enterprise Academic Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&family=IBM+Plex+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Theme Early Initialization Script -->
    <script>
        if (localStorage.getItem('simpus_kiosk_theme') === 'dark' || (!localStorage.getItem('simpus_kiosk_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Production Compiled Tailwind CSS & Custom Tokens -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <!-- Lucide Icons (v0.468.0 — pinned) -->
    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    <!-- CDN Fallback Loader (self-hosted Lucide as backup) -->
    <script>window.__BASE_URL__ = '<?= base_url() ?>';</script>
    <script src="<?= base_url('js/cdn-fallback.js') ?>"></script>
</head>
<body class="bg-[#FBF9F5] dark:bg-[#0C1222] text-[#2C3E50] dark:text-slate-100 min-h-screen flex flex-col justify-between selection:bg-[#1B2A4A] selection:text-white transition-colors duration-200">

    <!-- Header Terintegrasi SIMPUS -->
    <header class="p-3.5 sm:p-4 bg-white/95 dark:bg-[#131B2E]/95 backdrop-blur-md border-b border-[#E5DFD3] dark:border-slate-800 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
            <!-- Brand Section -->
            <a href="<?= site_url('/katalog') ?>" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-lg bg-[#F4EFE6] dark:bg-slate-800 p-1.5 flex items-center justify-center border border-[#E5DFD3] dark:border-slate-700 shadow-xs shrink-0">
                    <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-serif font-bold text-lg text-[#1B2A4A] dark:text-slate-100 leading-none">SIMPUS</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#B8791F]/15 text-[#B8791F] dark:text-amber-300 border border-[#B8791F]/30">SDN 12</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#C1613A]/10 text-[#C1613A] dark:text-orange-300 border border-[#C1613A]/20">LAYANAN MANDIRI</span>
                    </div>
                    <p class="text-[11px] text-[#5C6470] dark:text-slate-400 mt-0.5">
                        Layanan Mandiri Siswa &bull; <span class="font-semibold text-[#1B2A4A] dark:text-slate-200"><?= esc($config['nama_perpustakaan'] ?? 'SD Negeri 12 Sumbawa') ?></span>
                    </p>
                </div>
            </a>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" id="btnThemeToggle" 
                        class="px-3 py-1.5 bg-[#F4EFE6] hover:bg-[#EAE4D6] dark:bg-slate-800 dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors flex items-center gap-1.5 text-xs font-semibold" 
                        title="Beralih Mode Terang / Gelap">
                    <i data-lucide="sun" class="w-4 h-4 hidden dark:inline text-amber-400"></i>
                    <i data-lucide="moon" class="w-4 h-4 inline dark:hidden text-slate-600"></i>
                    <span class="hidden sm:inline" id="labelTheme">Mode</span>
                </button>

                <!-- Fullscreen Toggle -->
                <button type="button" onclick="toggleFullscreen()" class="p-2 bg-[#F4EFE6] hover:bg-[#EAE4D6] dark:bg-slate-800 dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors" title="Layar Penuh">
                    <i data-lucide="maximize" class="w-4 h-4"></i>
                </button>
                <a href="<?= site_url('/katalog') ?>" class="px-3 py-1.5 text-xs font-semibold text-[#1B2A4A] dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-[#F4EFE6] dark:hover:bg-slate-700 rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="book-open" class="w-4 h-4 text-[#C1613A]"></i>
                    <span class="hidden sm:inline">Katalog OPAC</span>
                </a>
                <a href="<?= site_url('/dashboard') ?>" class="px-3 py-1.5 text-xs font-semibold text-[#1B2A4A] dark:text-slate-200 bg-[#F4EFE6] hover:bg-[#EAE4D6] dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg border border-[#E5DFD3] dark:border-slate-700 transition-colors flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-4 h-4 text-[#1B2A4A] dark:text-slate-300"></i>
                    <span class="hidden md:inline">Panel Staf</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 flex flex-col justify-center my-auto">
        


        <!-- Mode Switcher Tabs -->
        <div class="flex items-center justify-center mb-5">
            <div class="inline-flex p-1 bg-white dark:bg-[#131B2E] rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-xs">
                <button type="button" onclick="switchKioskMode('pinjam')" id="tabPinjam"
                        class="px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors bg-[#1B2A4A] text-white shadow-xs">
                    <i data-lucide="book-up-2" class="w-4 h-4 text-amber-400"></i>
                    <span>Peminjaman Mandiri</span>
                </button>
                <button type="button" onclick="switchKioskMode('kembali')" id="tabKembali"
                        class="px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors text-[#5C6470] dark:text-slate-400 hover:text-[#1B2A4A] dark:hover:text-white hover:bg-[#F4EFE6] dark:hover:bg-slate-800">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 text-[#C1613A]"></i>
                    <span>Pengembalian Mandiri</span>
                </button>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- MODE 1: PEMINJAMAN MANDIRI -->
        <!-- ================================================================= -->
        <div id="sectionPinjam" class="space-y-5">
            
            <!-- Step 1: Scan Kartu Anggota -->
            <div id="stepAnggota" class="bg-white dark:bg-[#131B2E] p-5 sm:p-7 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-[#1B2A4A] text-white font-serif font-bold flex items-center justify-center text-xs shrink-0">
                            1
                        </span>
                        <div>
                            <h2 class="font-serif font-bold text-base sm:text-lg text-[#1B2A4A] dark:text-white">Identifikasi Anggota Siswa / Guru</h2>
                            <p class="text-xs text-[#5C6470] dark:text-slate-400">Ketik nomor identitas anggota (NISN atau ID anggota perpustakaan)</p>
                        </div>
                    </div>
                </div>

                <!-- Input Scanner Kartu -->
                <form id="formCekAnggota" onsubmit="event.preventDefault(); submitCekAnggota();" class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-1">
                        <i data-lucide="user-check" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="text" id="inputAnggota" autofocus required
                               class="w-full pl-10 pr-3.5 py-3 rounded-lg bg-[#FBF9F5] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 focus:border-[#1B2A4A] dark:focus:border-amber-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none text-[#1B2A4A] dark:text-white font-mono text-sm sm:text-base tracking-wider placeholder:text-slate-400 placeholder:text-xs placeholder:font-sans transition-all"
                               placeholder="Ketik NISN atau ID anggota perpustakaan...">
                    </div>

                    <button type="submit" id="btnCekAnggota"
                            class="px-5 sm:px-7 py-3 bg-[#1B2A4A] hover:bg-[#24375D] text-white font-serif font-bold text-xs sm:text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                        <span>Periksa Kartu</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-amber-400"></i>
                    </button>
                </form>

                <!-- Feedback Area Anggota -->
                <div id="feedbackAnggota" class="mt-3 text-xs font-semibold hidden"></div>
            </div>

            <!-- Step 2: Identitas Anggota & Scan Buku -->
            <div id="stepBuku" class="hidden space-y-5 animate-in fade-in duration-200">
                
                <!-- Info Siswa Terverifikasi -->
                <div class="bg-[#1B2A4A] text-white p-5 sm:p-6 rounded-xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 border border-[#24375D]">
                    <div class="flex items-center gap-3.5">
                        <div class="w-14 h-14 rounded-lg bg-white/10 border border-white/20 flex items-center justify-center text-amber-300 text-xl font-serif font-bold shrink-0" id="avatarAnggota">
                            A
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Anggota Terverifikasi
                                </span>
                                <span class="text-xs text-slate-300 font-mono" id="dispNomorAnggota">AG-1001</span>
                            </div>
                            <h3 class="font-serif font-bold text-lg sm:text-xl text-white mt-1" id="dispNamaAnggota">Nama Siswa</h3>
                            <p class="text-xs text-slate-300" id="dispKelasAnggota">Kelas 4A &bull; Siswa</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 bg-white/10 p-3 px-4 rounded-lg border border-white/15">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-300 block uppercase tracking-wider font-bold">Sisa Kuota Pinjam</span>
                            <span class="font-serif font-bold text-xl text-amber-300" id="dispSisaKuota">1</span>
                            <span class="text-xs text-slate-300">/ <span id="dispMaxPinjam">2</span> buku</span>
                        </div>
                        <button type="button" onclick="resetKioskSession()" class="px-3 py-1.5 text-xs bg-white/10 hover:bg-rose-900/60 text-white rounded-md border border-white/20 transition-colors ml-1" title="Ganti Kartu / Selesai">
                            Ganti Kartu
                        </button>
                    </div>
                </div>

                <!-- Step 2 Input Scan Buku -->
                <div class="bg-white dark:bg-[#131B2E] p-5 sm:p-7 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-8 h-8 rounded-lg bg-[#C1613A] text-white font-serif font-bold flex items-center justify-center text-xs shrink-0">
                            2
                        </span>
                        <div>
                            <h2 class="font-serif font-bold text-base sm:text-lg text-[#1B2A4A] dark:text-white">Pilih Buku yang Ingin Dipinjam</h2>
                            <p class="text-xs text-[#5C6470] dark:text-slate-400">Ketik kode buku yang ingin dipinjam (misal: BK-0001)</p>
                        </div>
                    </div>

                    <form id="formCekBuku" onsubmit="event.preventDefault(); submitCekBuku();" class="flex flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1">
                            <i data-lucide="book" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                            <input type="text" id="inputBuku" required
                                   class="w-full pl-10 pr-3.5 py-3 rounded-lg bg-[#FBF9F5] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 focus:border-[#1B2A4A] dark:focus:border-amber-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none text-[#1B2A4A] dark:text-white font-mono text-sm sm:text-base tracking-wider placeholder:text-slate-400 placeholder:text-xs placeholder:font-sans transition-all"
                                   placeholder="Ketik kode buku (misal: BK-0001)...">
                        </div>

                        <button type="submit" id="btnCekBuku"
                                class="px-5 sm:px-7 py-3 bg-[#C1613A] hover:bg-[#A95330] text-white font-serif font-bold text-xs sm:text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                            <span>Ambil Buku</span>
                            <i data-lucide="plus-circle" class="w-4 h-4 text-amber-200"></i>
                        </button>
                    </form>

                    <!-- Preview Buku Terdeteksi -->
                    <div id="previewBuku" class="mt-5 p-4 bg-[#F8F6F0] dark:bg-slate-900 rounded-lg border border-[#E5DFD3] dark:border-slate-800 hidden flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-16 rounded-md bg-[#EFECE6] dark:bg-slate-800 flex items-center justify-center text-slate-400 shrink-0 border border-[#E5DFD3] dark:border-slate-700 overflow-hidden" id="previewBukuCover">
                                <i data-lucide="book" class="w-5 h-5 text-[#1B2A4A]"></i>
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#1B2A4A]/10 dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-200 border border-[#1B2A4A]/20" id="prevKodeBuku">BK-0001</span>
                                <h4 class="font-serif font-bold text-sm text-[#1B2A4A] dark:text-white mt-1 line-clamp-1" id="prevJudulBuku">Judul Buku</h4>
                                <p class="text-xs text-[#5C6470] dark:text-slate-400 mt-0.5" id="prevKategoriBuku">Kategori &bull; Rak A1</p>
                            </div>
                        </div>

                        <button type="button" onclick="konfirmasiPinjamBuku()" id="btnKonfirmasiPinjam"
                                class="px-5 py-2.5 bg-[#2F6E4E] hover:bg-[#25573E] text-white font-serif font-bold text-xs sm:text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>Konfirmasi Peminjaman</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <!-- ================================================================= -->
        <!-- MODE 2: PENGEMBALIAN MANDIRI -->
        <!-- ================================================================= -->
        <div id="sectionKembali" class="hidden space-y-5">
            <div class="bg-white dark:bg-[#131B2E] p-5 sm:p-7 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm relative">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-8 h-8 rounded-lg bg-[#2F6E4E] text-white font-bold flex items-center justify-center text-xs shrink-0">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </span>
                    <div>
                        <h2 class="font-serif font-bold text-base sm:text-lg text-[#1B2A4A] dark:text-white">Kembalikan Buku Perpustakaan</h2>
                        <p class="text-xs text-[#5C6470] dark:text-slate-400">Ketik kode buku atau nomor transaksi peminjaman buku</p>
                    </div>
                </div>

                <form id="formKembaliBuku" onsubmit="event.preventDefault(); submitKembaliBuku();" class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-1">
                        <i data-lucide="book-check" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="text" id="inputKodeKembali" required
                               class="w-full pl-10 pr-3.5 py-3 rounded-lg bg-[#FBF9F5] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 focus:border-[#1B2A4A] dark:focus:border-amber-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none text-[#1B2A4A] dark:text-white font-mono text-sm sm:text-base tracking-wider placeholder:text-slate-400 placeholder:text-xs placeholder:font-sans transition-all"
                               placeholder="Ketik kode buku (misal: BK-0001)...">
                    </div>

                    <button type="submit" id="btnKembaliBuku"
                            class="px-5 sm:px-7 py-3 bg-[#2F6E4E] hover:bg-[#25573E] text-white font-serif font-bold text-xs sm:text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                        <span>Kembalikan Sekarang</span>
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </button>
                </form>

                <div id="feedbackKembali" class="mt-3 text-xs font-semibold hidden"></div>
            </div>
        </div>

    </main>

    <!-- Footer Terintegrasi SIMPUS -->
    <footer class="p-3.5 text-center text-xs text-[#5C6470] dark:text-slate-400 border-t border-[#E5DFD3] dark:border-slate-800 bg-white/80 dark:bg-[#0A101D]/80">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px]">
            <span>&copy; <?= date('Y') ?> SIMPUS SD &bull; <?= esc($config['nama_perpustakaan'] ?? 'SD Negeri 12 Sumbawa') ?> &bull; Capstone Project UT Sistem Informasi 2026</span>
            <span class="text-[#1B2A4A] dark:text-slate-300 font-mono font-semibold">Durasi Pinjam: <?= $config['durasi_pinjam_default'] ?? 5 ?> Hari | Denda: Rp <?= number_format($config['tarif_denda_per_hari'] ?? 500, 0, ',', '.') ?>/hari</span>
        </div>
    </footer>



    <!-- ================================================================= -->
    <!-- MODAL STRUK DIGITAL (RECEIPT SIMPUS THEME) -->
    <!-- ================================================================= -->
    <div id="modalReceipt" class="fixed inset-0 z-50 bg-[#1B2A4A]/70 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-[#131B2E] text-[#1B2A4A] dark:text-white rounded-xl shadow-xl border border-[#E5DFD3] dark:border-slate-800 w-full max-w-md overflow-hidden text-xs">
            <!-- Modal Header -->
            <div class="p-5 bg-[#1B2A4A] text-white text-center relative border-b border-[#1B2A4A]">
                <div class="w-12 h-12 rounded-lg bg-white/10 text-amber-400 mx-auto flex items-center justify-center mb-2">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-serif font-bold text-lg text-white" id="rcptTitle">Transaksi Berhasil!</h3>
                <p class="text-[11px] text-slate-300 mt-0.5" id="rcptSubtitle">Bukti transaksi layanan mandiri siswa perpustakaan</p>
            </div>

            <!-- Receipt Content (Stamping card style) -->
            <div class="p-5 space-y-3.5 font-mono bg-[#F8F6F0] dark:bg-slate-900/60">
                <div class="p-3.5 bg-white dark:bg-[#131B2E] rounded-lg border border-[#E5DFD3] dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between border-b border-dashed border-[#E5DFD3] dark:border-slate-800 pb-1.5">
                        <span class="text-[#5C6470] font-sans">KODE TRANSAKSI:</span>
                        <span class="font-bold text-[#1B2A4A] dark:text-white" id="rcptKodeTrx">-</span>
                    </div>
                    <div class="flex justify-between border-b border-dashed border-[#E5DFD3] dark:border-slate-800 pb-1.5">
                        <span class="text-[#5C6470] font-sans">PEMUSTAKA:</span>
                        <span class="font-bold text-[#1B2A4A] dark:text-white" id="rcptNama">-</span>
                    </div>
                    <div class="flex justify-between border-b border-dashed border-[#E5DFD3] dark:border-slate-800 pb-1.5">
                        <span class="text-[#5C6470] font-sans">JUDUL BUKU:</span>
                        <span class="font-bold text-[#1B2A4A] dark:text-white text-right max-w-[180px] truncate" id="rcptBuku">-</span>
                    </div>
                    <div class="flex justify-between" id="rowJatuhTempo">
                        <span class="text-[#5C6470] font-sans">JATUH TEMPO:</span>
                        <span class="font-bold text-[#C1613A] dark:text-orange-400" id="rcptTgl">-</span>
                    </div>
                    <div class="flex justify-between hidden" id="rowDenda">
                        <span class="text-[#5C6470] font-sans">DENDA:</span>
                        <span class="font-bold text-[#C1613A] dark:text-rose-400" id="rcptDenda">Rp 0</span>
                    </div>
                </div>

                <div class="text-center text-[11px] text-[#5C6470] dark:text-slate-400 font-sans leading-relaxed">
                    Rawat buku perpustakaan dengan baik dan kembalikan sebelum tanggal jatuh tempo. Terima kasih! 📚
                </div>
            </div>

            <div class="p-3.5 bg-white dark:bg-[#131B2E] border-t border-[#E5DFD3] dark:border-slate-800 flex items-center justify-center">
                <button type="button" onclick="closeReceiptModal()" class="w-full py-2.5 bg-[#1B2A4A] hover:bg-[#24375D] text-white font-serif font-bold text-xs rounded-lg shadow-sm transition-colors">
                    Selesai & Sesi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts & Audio Synth -->
    <script>
        lucide.createIcons();

        let currentAnggota = null;
        let selectedBuku = null;

        // Theme Toggle Functionality (Terang / Gelap)
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('simpus_kiosk_theme', isDark ? 'dark' : 'light');
            lucide.createIcons();
        }

        // Audio Feedback Web Audio API Synth
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function playChime(isSuccess = true) {
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                if (isSuccess) {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                    osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.1); // E5
                    osc.frequency.setValueAtTime(783.99, audioCtx.currentTime + 0.2); // G5
                    gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.45);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.5);
                } else {
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(220, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.35);
                }
            } catch (e) {
                console.log('Audio blocked');
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    alert(`Gagal layar penuh: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        function switchKioskMode(mode) {
            const tabPinjam = document.getElementById('tabPinjam');
            const tabKembali = document.getElementById('tabKembali');
            const secPinjam = document.getElementById('sectionPinjam');
            const secKembali = document.getElementById('sectionKembali');

            if (mode === 'pinjam') {
                tabPinjam.className = "px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors bg-[#1B2A4A] text-white shadow-xs";
                tabKembali.className = "px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors text-[#5C6470] dark:text-slate-400 hover:text-[#1B2A4A] dark:hover:text-white hover:bg-[#F4EFE6] dark:hover:bg-slate-800";
                secPinjam.classList.remove('hidden');
                secKembali.classList.add('hidden');
                document.getElementById('inputAnggota').focus();
            } else {
                tabKembali.className = "px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors bg-[#2F6E4E] text-white shadow-xs";
                tabPinjam.className = "px-5 sm:px-7 py-2.5 rounded-lg font-serif font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors text-[#5C6470] dark:text-slate-400 hover:text-[#1B2A4A] dark:hover:text-white hover:bg-[#F4EFE6] dark:hover:bg-slate-800";
                secKembali.classList.remove('hidden');
                secPinjam.classList.add('hidden');
                document.getElementById('inputKodeKembali').focus();
            }
        }

        async function submitCekAnggota() {
            const input = document.getElementById('inputAnggota');
            const val = input.value.trim();
            const feedback = document.getElementById('feedbackAnggota');
            if (!val) return;

            feedback.className = "mt-3 text-xs font-semibold text-[#1B2A4A] dark:text-amber-300 flex items-center justify-center gap-2";
            feedback.innerHTML = '<svg class="animate-spin w-4 h-4 text-[#1B2A4A] dark:text-amber-300 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Memverifikasi kartu anggota siswa...</span>';
            feedback.classList.remove('hidden');

            try {
                const formData = new FormData();
                formData.append('identifier', val);

                const res = await fetch('<?= site_url('/kiosk/cek-anggota') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    playChime(true);
                    currentAnggota = data.anggota;
                    document.getElementById('dispNamaAnggota').textContent = data.anggota.nama;
                    document.getElementById('dispNomorAnggota').textContent = data.anggota.nomor_anggota;
                    document.getElementById('dispKelasAnggota').textContent = (data.anggota.kelas || '-') + ' • ' + (data.anggota.tipe_anggota || 'Siswa');
                    document.getElementById('dispSisaKuota').textContent = data.sisa_kuota;
                    document.getElementById('dispMaxPinjam').textContent = data.max_pinjam;
                    const avatarEl = document.getElementById('avatarAnggota');
                    if (data.anggota.foto) {
                        avatarEl.innerHTML = `<img src="<?= base_url() ?>/${data.anggota.foto}" alt="${data.anggota.nama}" class="w-full h-full object-cover rounded-lg">`;
                    } else {
                        avatarEl.textContent = data.anggota.nama.charAt(0).toUpperCase();
                    }

                    feedback.classList.add('hidden');
                    const stepBukuEl = document.getElementById('stepBuku');
                    stepBukuEl.classList.remove('hidden');
                    setTimeout(() => {
                        stepBukuEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        document.getElementById('inputBuku').focus();
                    }, 100);
                } else {
                    playChime(false);
                    feedback.className = "mt-3 text-xs font-semibold text-[#C1613A] dark:text-rose-400 p-3 bg-rose-50 dark:bg-rose-950/60 rounded-lg border border-rose-200 dark:border-rose-900 flex items-start gap-2 leading-relaxed";
                    feedback.innerHTML = `<i data-lucide="alert-triangle" class="w-4 h-4 text-[#C1613A] shrink-0 mt-0.5"></i><div>${data.message || "Gagal memverifikasi kartu anggota."}</div>`;
                    lucide.createIcons();
                    input.select();
                    feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } catch (err) {
                playChime(false);
                feedback.className = "mt-3 text-xs font-semibold text-[#C1613A] dark:text-rose-400 p-3 bg-rose-50 dark:bg-rose-950/60 rounded-lg border border-rose-200 dark:border-rose-900 flex items-start gap-2";
                feedback.innerHTML = `<i data-lucide="wifi-off" class="w-4 h-4 text-[#C1613A] shrink-0 mt-0.5"></i><div>Terjadi kesalahan koneksi saat memverifikasi data.</div>`;
                lucide.createIcons();
            }
        }

        async function submitCekBuku() {
            const input = document.getElementById('inputBuku');
            const val = input.value.trim();
            if (!val) return;

            try {
                const formData = new FormData();
                formData.append('kode_buku', val);

                const res = await fetch('<?= site_url('/kiosk/cek-buku') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    playChime(true);
                    selectedBuku = data.buku;
                    document.getElementById('prevKodeBuku').textContent = data.buku.kode_buku;
                    document.getElementById('prevJudulBuku').textContent = data.buku.judul;
                    document.getElementById('prevKategoriBuku').textContent = (data.buku.nama_kategori || 'Koleksi') + ' • ' + (data.buku.lokasi_rak || 'Rak Utama');

                    const coverEl = document.getElementById('previewBukuCover');
                    if (data.buku.cover) {
                        coverEl.innerHTML = `<img src="<?= base_url() ?>/${data.buku.cover}" alt="${data.buku.judul}" class="w-full h-full object-cover rounded-md">`;
                    } else {
                        coverEl.innerHTML = '<i data-lucide="book" class="w-5 h-5 text-[#1B2A4A]"></i>';
                    }

                    const preview = document.getElementById('previewBuku');
                    preview.classList.remove('hidden');
                    preview.classList.add('flex');
                    setTimeout(() => {
                        preview.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);

                    const btnKonfirmasi = document.getElementById('btnKonfirmasiPinjam');
                    if (!data.tersedia) {
                        btnKonfirmasi.disabled = true;
                        btnKonfirmasi.className = "px-5 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 font-serif font-bold text-xs sm:text-sm rounded-lg cursor-not-allowed";
                        btnKonfirmasi.textContent = "Stok Kosong di Rak";
                    } else {
                        btnKonfirmasi.disabled = false;
                        btnKonfirmasi.className = "px-5 py-2.5 bg-[#2F6E4E] hover:bg-[#25573E] text-white font-serif font-bold text-xs sm:text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2";
                        btnKonfirmasi.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4"></i><span>Konfirmasi Peminjaman</span>';
                    }
                    lucide.createIcons();
                } else {
                    playChime(false);
                    alert(data.message || "Buku tidak ditemukan.");
                    input.select();
                }
            } catch (e) {
                playChime(false);
                alert("Kesalahan koneksi saat memeriksa buku.");
            }
        }

        async function konfirmasiPinjamBuku() {
            if (!currentAnggota || !selectedBuku) return;

            const btn = document.getElementById('btnKonfirmasiPinjam');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4 text-white inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Menyimpan Transaksi...</span>';

            try {
                const formData = new FormData();
                formData.append('anggota_id', currentAnggota.id);
                formData.append('buku_id', selectedBuku.id);

                const res = await fetch('<?= site_url('/kiosk/pinjam') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    playChime(true);
                    showReceiptModal({
                        title: 'Peminjaman Berhasil!',
                        subtitle: 'Buku berhasil dipinjam secara mandiri',
                        kodeTrx: data.receipt.kode_transaksi,
                        nama: data.receipt.nama_anggota,
                        buku: data.receipt.judul_buku,
                        tgl: data.receipt.tanggal_jatuh_tempo,
                        showDenda: false
                    });
                } else {
                    playChime(false);
                    alert(data.message || "Gagal memproses peminjaman.");
                    btn.disabled = false;
                    btn.textContent = "Konfirmasi Peminjaman";
                }
            } catch (e) {
                playChime(false);
                alert("Kesalahan server saat memproses pinjaman.");
                btn.disabled = false;
            }
        }

        async function submitKembaliBuku() {
            const input = document.getElementById('inputKodeKembali');
            const val = input.value.trim();
            const feedback = document.getElementById('feedbackKembali');
            if (!val) return;

            feedback.className = "mt-3 text-xs font-semibold text-[#2F6E4E] dark:text-emerald-400 flex items-center justify-center gap-2";
            feedback.innerHTML = '<svg class="animate-spin w-4 h-4 text-[#2F6E4E] dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Mencari data transaksi peminjaman buku...</span>';
            feedback.classList.remove('hidden');

            try {
                const formData = new FormData();
                formData.append('kode', val);

                const res = await fetch('<?= site_url('/kiosk/kembali') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    playChime(true);
                    feedback.classList.add('hidden');
                    input.value = '';

                    showReceiptModal({
                        title: 'Pengembalian Berhasil!',
                        subtitle: 'Buku telah dikembalikan dan stok di rak diperbarui',
                        kodeTrx: data.receipt.kode_transaksi,
                        nama: data.receipt.nama_anggota,
                        buku: data.receipt.judul_buku,
                        tgl: data.receipt.tanggal_kembali,
                        showDenda: (data.receipt.denda > 0),
                        denda: 'Rp ' + Number(data.receipt.denda).toLocaleString('id-ID') + (data.receipt.terlambat_hari > 0 ? ` (Terlambat ${data.receipt.terlambat_hari} hari)` : '')
                    });
                } else {
                    playChime(false);
                    feedback.className = "mt-3 text-xs font-semibold text-[#C1613A] dark:text-rose-400 p-3 bg-rose-50 dark:bg-rose-950/60 rounded-lg border border-rose-200 dark:border-rose-900";
                    feedback.textContent = data.message || "Gagal memproses pengembalian.";
                    input.select();
                }
            } catch (e) {
                playChime(false);
                feedback.className = "mt-3 text-xs font-semibold text-[#C1613A] dark:text-rose-400";
                feedback.textContent = "Kesalahan server saat pengembalian.";
            }
        }



        function showReceiptModal({ title, subtitle, kodeTrx, nama, buku, tgl, showDenda = false, denda = '' }) {
            document.getElementById('rcptTitle').textContent = title;
            document.getElementById('rcptSubtitle').textContent = subtitle;
            document.getElementById('rcptKodeTrx').textContent = kodeTrx;
            document.getElementById('rcptNama').textContent = nama;
            document.getElementById('rcptBuku').textContent = buku;
            document.getElementById('rcptTgl').textContent = tgl;

            const rowDenda = document.getElementById('rowDenda');
            if (showDenda) {
                rowDenda.classList.remove('hidden');
                document.getElementById('rcptDenda').textContent = denda;
            } else {
                rowDenda.classList.add('hidden');
            }

            document.getElementById('modalReceipt').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('modalReceipt').classList.add('hidden');
            resetKioskSession();
        }

        function resetKioskSession() {
            currentAnggota = null;
            selectedBuku = null;
            document.getElementById('inputAnggota').value = '';
            document.getElementById('inputBuku').value = '';
            document.getElementById('previewBuku').classList.add('hidden');
            document.getElementById('stepBuku').classList.add('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.getElementById('inputAnggota').focus();
        }

        // =================================================================
        // LISTENER GLOBAL SCANNER FISIK (USB / WIRELESS SCANNER TEMBAK)
        // =================================================================
        let scanBuffer = '';
        let lastKeyTime = Date.now();

        window.addEventListener('keydown', function(e) {
            const modalReceipt = document.getElementById('modalReceipt');
            if (modalReceipt && !modalReceipt.classList.contains('hidden')) {
                return;
            }

            if (e.target && e.target.tagName === 'INPUT') {
                if (e.key === 'Enter') {
                    scanBuffer = '';
                    return;
                }
                return;
            }

            const currentTime = Date.now();
            const timeDiff = currentTime - lastKeyTime;
            lastKeyTime = currentTime;

            if (e.key === 'Enter') {
                if (scanBuffer.length >= 2) {
                    e.preventDefault();
                    handleGlobalBarcodeScan(scanBuffer.trim());
                }
                scanBuffer = '';
                return;
            }

            if (e.key && e.key.length === 1) {
                if (timeDiff < 80 || scanBuffer.length === 0) {
                    scanBuffer += e.key;
                } else {
                    scanBuffer = e.key;
                }

                setTimeout(() => {
                    const now = Date.now();
                    if (now - lastKeyTime > 300 && scanBuffer.length > 0) {
                        scanBuffer = '';
                    }
                }, 350);
            }
        });

        function handleGlobalBarcodeScan(code) {
            if (!code) return;

            const secKembali = document.getElementById('sectionKembali');
            const isKembali = secKembali && !secKembali.classList.contains('hidden');

            if (isKembali) {
                const input = document.getElementById('inputKodeKembali');
                if (input) {
                    input.value = code;
                    submitKembaliBuku();
                }
            } else {
                const stepBuku = document.getElementById('stepBuku');
                const isStepBuku = stepBuku && !stepBuku.classList.contains('hidden');

                if (isStepBuku) {
                    const inputBuku = document.getElementById('inputBuku');
                    if (inputBuku) {
                        inputBuku.value = code;
                        submitCekBuku();
                    }
                } else {
                    const inputAnggota = document.getElementById('inputAnggota');
                    if (inputAnggota) {
                        inputAnggota.value = code;
                        submitCekAnggota();
                    }
                }
            }
        }
    </script>
</body>
</html>
