<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($config['nama_perpustakaan'] ?? 'SIMPUS Perpustakaan SD Negeri 12 Sumbawa') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('images/logo.png') ?>">
    
    <!-- Google Fonts: IBM Plex Family (Enterprise Academic Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&family=IBM+Plex+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Production Compiled Tailwind CSS & Custom Tokens -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <!-- Lucide Icons (v0.468.0 — pinned) -->
    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    <!-- CDN Fallback Loader (self-hosted Lucide as backup) -->
    <script>window.__BASE_URL__ = '<?= base_url() ?>';</script>
    <script src="<?= base_url('js/cdn-fallback.js') ?>"></script>
</head>
<body class="bg-[#FBF9F5] dark:bg-[#0C1222] text-[#2C3E50] dark:text-slate-100 font-sans antialiased min-h-screen flex flex-col overflow-x-hidden selection:bg-[#1B2A4A] selection:text-white transition-colors duration-200">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 bg-white/95 dark:bg-[#131B2E]/95 backdrop-blur-md border-b border-[#E5DFD3]/80 dark:border-slate-800 shadow-2xs transition-colors">
        <div class="mobile-viewport-shell max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 min-h-16 sm:h-20 py-2 sm:py-0 flex items-center justify-between gap-2 min-w-0">
            <!-- Brand with Official Logo -->
            <a href="<?= site_url('/katalog') ?>" class="flex items-center gap-2 sm:gap-3.5 group focus:outline-none min-w-0 overflow-hidden">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-white dark:bg-slate-800 p-1.5 sm:p-2 flex items-center justify-center border border-[#E5DFD3] dark:border-slate-700 shadow-2xs shrink-0 transition-transform group-hover:scale-[1.02]">
                    <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col justify-center min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="font-serif font-bold text-lg sm:text-xl text-[#1B2A4A] dark:text-slate-100 leading-none tracking-tight">SIMPUS</span>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold tracking-wide bg-[#B8791F]/10 text-[#B8791F] dark:text-amber-300 border border-[#B8791F]/25 leading-none">SDN 12</span>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold tracking-wide bg-[#1B2A4A]/10 text-[#1B2A4A] dark:text-sky-300 border border-[#1B2A4A]/20 leading-none">OPAC</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-1.5 mt-1.5 min-w-0">
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs text-[#5C6470] dark:text-slate-400 font-medium leading-none whitespace-nowrap">
                            <?= esc($config['nama_perpustakaan'] ?? 'SD Negeri 12 Sumbawa') ?>
                        </span>
                    </div>
                </div>
            </a>

            <!-- Right Actions (Theme Toggle, Divider, & Login/Dashboard Button) -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <!-- Theme Toggle Button -->
                <button id="themeToggle" class="h-10 w-10 sm:h-11 sm:w-11 rounded-lg sm:rounded-xl bg-white hover:bg-[#F4EFE6] dark:bg-slate-800 dark:hover:bg-slate-700 text-[#5C6470] dark:text-slate-300 transition-all border border-[#E5DFD3] dark:border-slate-700 flex items-center justify-center shadow-2xs shrink-0 focus:outline-none focus:ring-2 focus:ring-[#1B2A4A]/20" title="Ubah Tema Tampilan">
                    <i data-lucide="moon" class="w-4 h-4 hidden dark:block text-amber-400"></i>
                    <i data-lucide="sun" class="w-4 h-4 block dark:hidden text-[#B8791F]"></i>
                </button>

                <!-- Tombol Layanan Mandiri Kiosk Siswa -->
                <a href="<?= site_url('/kiosk') ?>" target="_blank"
                   aria-label="Layanan Mandiri Siswa" title="Layanan Mandiri Siswa (Kiosk)"
                   class="inline-flex items-center justify-center gap-1.5 sm:gap-2 h-10 w-10 sm:w-auto sm:h-11 px-0 sm:px-3.5 rounded-lg sm:rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white hover:bg-[#F4EFE6] dark:bg-slate-800 dark:hover:bg-slate-700 shadow-2xs hover:shadow-xs transition-all border border-[#E5DFD3] dark:border-slate-700 shrink-0">
                    <i data-lucide="scan-line" class="w-4 h-4 text-amber-500"></i>
                    <span class="hidden md:inline">Layanan Mandiri</span>
                </a>

                <!-- Subtle Vertical Divider -->
                <div class="hidden sm:block h-6 w-px bg-[#E5DFD3] dark:bg-slate-700"></div>

                <!-- Login / Dashboard Petugas Button -->
                <?php if (session()->get('logged_in')): ?>
                    <a href="<?= site_url('/dashboard') ?>" 
                       aria-label="Buka panel staf" title="Panel Staf"
                       class="inline-flex items-center justify-center gap-1.5 sm:gap-2 h-10 w-10 sm:w-auto sm:h-11 px-0 sm:px-5 rounded-lg sm:rounded-xl text-xs font-semibold text-white bg-[#1B2A4A] hover:bg-[#24375D] shadow-xs hover:shadow-md transition-all border border-[#1B2A4A] shrink-0">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-400"></i>
                        <span class="hidden sm:inline">Panel Staf</span>
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('/login') ?>" 
                       aria-label="Masuk sebagai staf" title="Masuk Staf"
                       class="inline-flex items-center justify-center gap-1.5 sm:gap-2 h-10 w-10 sm:w-auto sm:h-11 px-0 sm:px-5 rounded-lg sm:rounded-xl text-xs font-semibold text-white bg-[#1B2A4A] hover:bg-[#24375D] shadow-xs hover:shadow-md transition-all border border-[#1B2A4A] shrink-0">
                        <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                        <span class="hidden sm:inline">Masuk Staf</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="mobile-viewport-shell flex-1 max-w-7xl w-full min-w-0 mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-7 space-y-5 sm:space-y-7">

        <!-- Hero Section with Deep Navy Library Tone -->
        <section class="w-full min-w-0 rounded-xl overflow-hidden bg-[#1B2A4A] text-white p-5 sm:p-10 shadow-sm border border-[#24375D]">
            <div class="max-w-3xl mx-auto text-center space-y-4">
                <!-- Chip Badge -->
                <div class="inline-flex items-center justify-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[11px] sm:text-xs font-semibold border border-white/15 text-amber-300 leading-snug">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span class="min-w-0">Katalog OPAC Terbuka &bull; Koleksi Perpustakaan SDN 12 Sumbawa</span>
                </div>
                
                <h1 class="font-serif font-bold text-xl sm:text-3.5xl text-white tracking-tight leading-snug break-words">
                    Temukan Buku & Bahan Pustaka Siswa
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto leading-relaxed">
                    Telusuri buku paket tematik Kurikulum Merdeka, fiksi cerita anak nusantara, ensiklopedia bergambar, dan referensi guru dengan status rak real-time.
                </p>

                <!-- Search Bar Form -->
                <form action="<?= site_url('/katalog') ?>" method="GET" class="pt-2 max-w-2xl mx-auto">
                    <div class="flex flex-col gap-2 sm:block">
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none">
                                <i data-lucide="search" class="w-5 h-5 text-amber-400"></i>
                            </div>
                            <input type="text" name="q" value="<?= esc($keyword ?? '') ?>"
                                   placeholder="Judul, penulis, ISBN, atau kategori..."
                                   class="w-full pl-12 pr-11 sm:pr-36 py-3.5 rounded-lg bg-white dark:bg-[#131B2E] text-[#1B2A4A] dark:text-white placeholder-slate-400 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-400 border border-[#E5DFD3] dark:border-slate-700 transition-all">

                            <div class="absolute right-2 flex items-center gap-1.5">
                            <?php if (!empty($keyword)): ?>
                                <a href="<?= site_url('/katalog') ?>" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white" title="Reset Pencarian">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="hidden sm:inline-flex px-5 py-2 rounded-md bg-[#C1613A] hover:bg-[#A95330] text-white font-bold text-sm shadow-xs transition-colors">
                                Cari Buku
                            </button>
                            </div>
                        </div>
                        <button type="submit" class="sm:hidden w-full min-h-11 px-5 py-2.5 rounded-lg bg-[#C1613A] hover:bg-[#A95330] text-white font-bold text-sm shadow-xs transition-colors">
                            Cari Buku
                        </button>
                    </div>

                    <!-- Filter Ketersediaan Toggle -->
                    <div class="flex flex-wrap items-center justify-center gap-3 mt-3.5 text-xs">
                        <label class="inline-flex w-full sm:w-auto items-start sm:items-center gap-2 cursor-pointer bg-white/10 hover:bg-white/15 px-3 py-2 sm:py-1 rounded-md border border-white/15 transition-colors text-left">
                            <input type="checkbox" name="ketersediaan" value="tersedia" <?= ($ketersediaan === 'tersedia') ? 'checked' : '' ?> onchange="this.form.submit()" class="mt-0.5 sm:mt-0 rounded text-[#C1613A] focus:ring-0 shrink-0">
                            <span class="text-slate-200 font-medium leading-snug">Hanya tampilkan buku yang sedang tersedia di rak</span>
                        </label>
                    </div>
                </form>
            </div>
        </section>

        <!-- Quick Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- 1. Total Koleksi -->
            <div class="bg-white dark:bg-[#131B2E] p-4 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#1B2A4A] text-white flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-5 h-5 text-amber-300"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#5C6470] dark:text-slate-400 tracking-wider block">Koleksi Judul</span>
                    <span class="text-base font-bold text-[#1B2A4A] dark:text-white font-serif"><?= number_format($totalBuku) ?> <span class="text-xs font-normal text-[#5C6470]">Judul</span></span>
                </div>
            </div>

            <!-- 2. Siap Dipinjam -->
            <div class="bg-white dark:bg-[#131B2E] p-4 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#2F6E4E] text-white flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-100"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#5C6470] dark:text-slate-400 tracking-wider block">Siap Dipinjam</span>
                    <span class="text-base font-bold text-[#2F6E4E] dark:text-emerald-400 font-mono"><?= number_format($totalTersedia) ?> <span class="text-xs font-normal text-[#5C6470]">Judul</span></span>
                </div>
            </div>

            <!-- 3. Kategori Bidang -->
            <div class="bg-white dark:bg-[#131B2E] p-4 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#B8791F] text-white flex items-center justify-center shrink-0">
                    <i data-lucide="tags" class="w-5 h-5 text-amber-100"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#5C6470] dark:text-slate-400 tracking-wider block">Kategori Rak</span>
                    <span class="text-base font-bold text-[#B8791F] dark:text-amber-400 font-serif"><?= count($kategori) ?> <span class="text-xs font-normal text-[#5C6470]">Bidang</span></span>
                </div>
            </div>

            <!-- 4. Durasi Pinjam -->
            <div class="bg-white dark:bg-[#131B2E] p-4 rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#2B4C7E] text-white flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-100"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#5C6470] dark:text-slate-400 tracking-wider block">Masa Pinjam Siswa</span>
                    <span class="text-base font-bold text-[#2B4C7E] dark:text-sky-400 font-mono"><?= esc($config['durasi_pinjam_default'] ?? '5') ?> Hari <span class="text-xs font-normal text-[#5C6470]">/ buku</span></span>
                </div>
            </div>
        </div>

        <!-- Category Filter Pills Bar -->
        <section class="space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-[#5C6470] dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-[#C1613A]"></i>
                    <span>Saring Kategori Buku</span>
                </h3>
                <span class="text-[11px] text-[#5C6470] dark:text-slate-400 font-mono"><?= count($kategori) ?> Kategori</span>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="<?= site_url('/katalog' . (!empty($keyword) ? '?q=' . urlencode($keyword) : '')) ?>" 
                   class="shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors <?= empty($selectedKat) ? 'bg-[#1B2A4A] text-white shadow-xs border border-[#1B2A4A]' : 'bg-white dark:bg-[#131B2E] text-[#5C6470] dark:text-slate-300 hover:bg-[#F4EFE6] dark:hover:bg-slate-800 border border-[#E5DFD3] dark:border-slate-800' ?>">
                    Semua Koleksi (<?= $totalBuku ?>)
                </a>

                <?php foreach ($kategori as $k): ?>
                    <a href="<?= site_url('/katalog?kategori=' . $k['id'] . (!empty($keyword) ? '&q=' . urlencode($keyword) : '')) ?>" 
                       class="shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors <?= ($selectedKat == $k['id']) ? 'bg-[#1B2A4A] text-white shadow-xs border border-[#1B2A4A]' : 'bg-white dark:bg-[#131B2E] text-[#5C6470] dark:text-slate-300 hover:bg-[#F4EFE6] dark:hover:bg-slate-800 border border-[#E5DFD3] dark:border-slate-800' ?>">
                        <?= esc($k['nama_kategori']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Books Grid Section -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 text-xs text-[#5C6470] dark:text-slate-400 border-b border-[#E5DFD3] dark:border-slate-800 pb-2">
                <div>
                    Menampilkan <strong><?= count($buku) ?></strong> judul buku 
                    <?php if (!empty($keyword)): ?>
                        untuk kata kunci "<strong><?= esc($keyword) ?></strong>"
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-1.5 text-[#2F6E4E] dark:text-emerald-400 font-semibold font-mono text-xs">
                    <span class="w-2 h-2 rounded-full bg-[#2F6E4E]"></span>
                    <?= $totalTersedia ?> Judul Siap Dipinjam
                </div>
            </div>

            <?php if (empty($buku)): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white dark:bg-[#131B2E] rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm space-y-3">
                    <div class="w-14 h-14 rounded-lg bg-[#F8F6F0] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 flex items-center justify-center mx-auto text-[#C1613A]">
                        <i data-lucide="book-x" class="w-7 h-7"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-serif font-bold text-base text-[#1B2A4A] dark:text-white">Buku Belum Ditemukan</h4>
                        <p class="text-xs text-[#5C6470] dark:text-slate-400 max-w-sm mx-auto">
                            Coba gunakan kata kunci yang lebih umum atau periksa kembali ejaan judul dan nama penulis.
                        </p>
                    </div>
                    <a href="<?= site_url('/katalog') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-[#1B2A4A] text-white hover:bg-[#24375D] transition-colors shadow-xs">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Tampilkan Semua Koleksi</span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Book Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    <?php foreach ($buku as $b): ?>
                        <div class="bg-white dark:bg-[#131B2E] rounded-xl border border-[#E5DFD3] dark:border-slate-800 shadow-sm hover:border-[#1B2A4A] dark:hover:border-slate-600 transition-colors flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Book Cover & Floating Badges -->
                            <div class="relative h-52 bg-[#F4EFE6] dark:bg-slate-800 p-3 flex items-center justify-center border-b border-[#E5DFD3] dark:border-slate-800">
                                <?php if (!empty($b['cover']) && file_exists(FCPATH . $b['cover'])): ?>
                                    <img src="<?= base_url($b['cover']) ?>" alt="<?= esc($b['judul']) ?>" 
                                         class="h-full max-w-[85%] object-contain rounded shadow-sm">
                                <?php else: ?>
                                    <!-- Decorative Book Spine Fallback -->
                                    <div class="w-24 h-36 rounded bg-[#1B2A4A] text-white flex flex-col items-center justify-between p-2.5 text-center border border-[#24375D] shadow-sm">
                                        <div class="w-6 h-6 rounded bg-white/10 text-amber-400 flex items-center justify-center">
                                            <i data-lucide="book" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="text-[9px] font-serif font-bold leading-tight line-clamp-3 text-amber-200"><?= esc($b['judul']) ?></span>
                                        <span class="text-[8px] font-mono text-slate-400">SDN 12</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Category Floating Pill -->
                                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded text-[10px] font-bold bg-white dark:bg-[#131B2E] text-[#1B2A4A] dark:text-slate-200 border border-[#E5DFD3] dark:border-slate-700 shadow-2xs max-w-[70%] truncate">
                                    <?= esc($b['nama_kategori'] ?? 'Umum') ?>
                                </span>

                                <!-- Stock Status Floating Pill -->
                                <div class="absolute bottom-2.5 right-2.5">
                                    <?php if ($b['stok_tersedia'] > 0): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-[#2F6E4E] text-white shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            Tersedia (<?= $b['stok_tersedia'] ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-[#C1613A] text-white shadow-2xs">
                                            Sedang Dipinjam
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Book Details Card Content -->
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div class="space-y-1">
                                    <h4 class="font-serif font-bold text-sm text-[#1B2A4A] dark:text-white leading-snug line-clamp-2" title="<?= esc($b['judul']) ?>">
                                        <?= esc($b['judul']) ?>
                                    </h4>
                                    <p class="text-[11px] text-[#5C6470] dark:text-slate-400 truncate flex items-center gap-1">
                                        <span>Penulis:</span>
                                        <span class="font-medium text-[#1B2A4A] dark:text-slate-300"><?= esc($b['penulis'] ?: '-') ?></span>
                                    </p>
                                </div>

                                <!-- Metadata Information (Rak & Kode) -->
                                <div class="space-y-1 pt-2 border-t border-[#E5DFD3]/70 dark:border-slate-800 text-[11px]">
                                    <div class="flex items-center justify-between text-[#5C6470] dark:text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="archive" class="w-3.5 h-3.5 text-[#B8791F]"></i>
                                            Lokasi Rak:
                                        </span>
                                        <span class="font-bold text-[#1B2A4A] dark:text-slate-200"><?= esc($b['lokasi_rak'] ?: 'Rak Pustaka') ?></span>
                                    </div>
                                    <div class="flex items-center justify-between text-[#5C6470] dark:text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="barcode" class="w-3.5 h-3.5 text-[#2B4C7E]"></i>
                                            Kode Buku:
                                        </span>
                                        <span class="font-mono text-[#1B2A4A] dark:text-slate-300 font-semibold"><?= esc($b['kode_buku']) ?></span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <button type="button" 
                                        onclick='showDetailModal(<?= json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                        class="w-full py-2 rounded-lg bg-[#1B2A4A] hover:bg-[#24375D] text-white font-semibold text-xs flex items-center justify-center gap-2 border border-[#1B2A4A] shadow-2xs transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-amber-400"></i>
                                    <span>Lihat Sinopsis & Detail</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Modal Detail Sinopsis Buku -->
    <div id="modalDetail" class="fixed inset-0 z-50 bg-[#1B2A4A]/60 hidden items-center justify-center p-2 sm:p-4 transition-all">
        <div class="bg-white dark:bg-[#131B2E] w-full max-w-2xl rounded-xl shadow-xl overflow-hidden border border-[#E5DFD3] dark:border-slate-800 max-h-[90vh] flex flex-col text-xs">
            
            <!-- Modal Header -->
            <div class="px-4 sm:px-5 py-3 sm:py-4 bg-[#1B2A4A] text-white flex items-center justify-between gap-3 border-b border-[#1B2A4A] shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-400">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-serif font-bold text-sm leading-tight text-white truncate">Rincian Buku & Sinopsis Koleksi</h4>
                        <p class="hidden sm:block text-[11px] text-slate-300">Informasi lengkap ketersediaan koleksi perpustakaan SDN 12 Sumbawa</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-5 text-xs">
                <div class="flex flex-col sm:flex-row gap-5 items-center sm:items-start">
                    <!-- Cover Box -->
                    <div class="w-32 h-44 shrink-0 rounded-lg overflow-hidden border border-[#E5DFD3] dark:border-slate-700 bg-[#F4EFE6] dark:bg-slate-800 relative flex items-center justify-center">
                        <img id="mCoverImg" src="" alt="Cover" class="w-full h-full object-contain">
                        <div id="mCoverPlaceholder" class="w-full h-full hidden flex-col items-center justify-center p-3 text-center bg-[#1B2A4A] text-white">
                            <i data-lucide="book" class="w-8 h-8 mb-1 text-amber-400"></i>
                            <span class="text-[10px] font-bold text-slate-200">Tanpa Cover Fisik</span>
                        </div>
                    </div>

                    <!-- Main Info -->
                    <div class="flex-1 space-y-2 text-center sm:text-left">
                        <span id="mKategori" class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-[#F4EFE6] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-200 border border-[#E5DFD3] dark:border-slate-700"></span>
                        
                        <h3 id="mJudul" class="font-serif font-bold text-base text-[#1B2A4A] dark:text-white leading-snug"></h3>
                        
                        <div class="space-y-1 text-[#5C6470] dark:text-slate-400 pt-1">
                            <p class="flex items-center justify-center sm:justify-start gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Penulis: <strong id="mPenulis" class="text-[#1B2A4A] dark:text-slate-200 font-semibold"></strong></span>
                            </p>
                            <p class="flex items-center justify-center sm:justify-start gap-1.5">
                                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Penerbit: <span id="mPenerbit" class="font-medium text-[#1B2A4A] dark:text-slate-300"></span> (<span id="mTahun"></span>)</span>
                            </p>
                            <p class="flex items-center justify-center sm:justify-start gap-1.5">
                                <i data-lucide="hash" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>ISBN: <span id="mIsbn" class="font-mono font-semibold text-[#1B2A4A] dark:text-slate-200"></span></span>
                            </p>
                        </div>

                        <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span id="mRak" class="px-2.5 py-1 rounded-md bg-[#F4EFE6] dark:bg-slate-800 text-[#1B2A4A] dark:text-slate-200 border border-[#E5DFD3] dark:border-slate-700 font-semibold flex items-center gap-1.5">
                                <i data-lucide="archive" class="w-3.5 h-3.5 text-[#B8791F]"></i>
                                <span id="mRakText"></span>
                            </span>
                            <span id="mStokBadge" class="px-2.5 py-1 rounded-md font-bold"></span>
                        </div>
                    </div>
                </div>

                <!-- Synopsis / Description -->
                <div class="space-y-1.5 border-t border-[#E5DFD3] dark:border-slate-800 pt-3.5">
                    <h5 class="font-bold text-[#1B2A4A] dark:text-slate-100 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#C1613A]"></i>
                        <span>Sinopsis / Ringkasan Buku:</span>
                    </h5>
                    <div class="bg-[#F8F6F0] dark:bg-slate-800/60 p-3.5 rounded-lg border border-[#E5DFD3] dark:border-slate-700">
                        <p id="mDeskripsi" class="text-[#5C6470] dark:text-slate-300 leading-relaxed whitespace-pre-line text-justify"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-[#F8F6F0] dark:bg-slate-800 border-t border-[#E5DFD3] dark:border-slate-800 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-[#1B2A4A] dark:text-slate-100 border border-[#E5DFD3] dark:border-slate-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="bg-white dark:bg-[#0A101D] border-t border-[#E5DFD3] dark:border-slate-800 py-6 text-xs text-[#5C6470] dark:text-slate-400 transition-colors mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pb-4 border-b border-[#E5DFD3]/60 dark:border-slate-800">
                <!-- Brand Info in Footer -->
                <div class="flex items-center gap-3 text-center md:text-left">
                    <div class="w-9 h-9 rounded-lg bg-[#F4EFE6] dark:bg-slate-800 p-1 border border-[#E5DFD3] dark:border-slate-700 shrink-0">
                        <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-bold text-[#1B2A4A] dark:text-slate-200 text-sm font-serif">
                            <?= esc($config['nama_perpustakaan'] ?? 'Perpustakaan SD Negeri 12 Sumbawa') ?>
                        </div>
                        <p class="text-[11px] text-[#5C6470] dark:text-slate-400">
                            <?= esc($config['alamat_perpustakaan'] ?? 'Jl. Pendidikan No. 01, Sumbawa') ?>
                        </p>
                    </div>
                </div>

                <!-- Operating Hours & Contact Badge -->
                <div class="flex flex-wrap items-center justify-center gap-3 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F4EFE6] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 text-[#1B2A4A] dark:text-slate-300">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-[#B8791F]"></i>
                        <span>Senin - Sabtu: 07.30 - 13.30 WITA</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F4EFE6] dark:bg-slate-800 border border-[#E5DFD3] dark:border-slate-700 text-[#1B2A4A] dark:text-slate-300 font-mono">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-[#2F6E4E]"></i>
                        <span><?= esc($config['kontak_perpustakaan'] ?? '0812-3456-7890') ?></span>
                    </span>
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left text-[11px] text-[#5C6470] dark:text-slate-500">
                <p>
                    &copy; <?= date('Y') ?> <strong>SIMPUS SD</strong> &bull; Perpustakaan SD Negeri 12 Sumbawa &bull; Capstone Project UT Sistem Informasi 2026
                </p>
                <div class="flex items-center gap-4">
                    <a href="<?= site_url('/katalog') ?>" class="hover:text-[#1B2A4A] dark:hover:text-white transition-colors">Katalog OPAC</a>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <a href="<?= site_url('/login') ?>" class="hover:text-[#1B2A4A] dark:hover:text-white transition-colors">Login Staf</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script for Dark Mode, Modal & Icon Initializer -->
    <script>
        // Dark Mode Logic
        const themeToggle = document.getElementById('themeToggle');
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        themeToggle.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            if (window.lucide) lucide.createIcons();
        });

        // Detail Modal Functions
        function showDetailModal(buku) {
            document.getElementById('mJudul').textContent = buku.judul || '-';
            document.getElementById('mKategori').textContent = buku.nama_kategori || 'Umum';
            document.getElementById('mPenulis').textContent = buku.penulis || '-';
            document.getElementById('mPenerbit').textContent = buku.penerbit || '-';
            document.getElementById('mTahun').textContent = buku.tahun_terbit || '-';
            document.getElementById('mIsbn').textContent = buku.isbn || '-';
            document.getElementById('mRakText').textContent = 'Lokasi: ' + (buku.lokasi_rak || 'Rak Pustaka');
            document.getElementById('mDeskripsi').textContent = buku.deskripsi || 'Belum ada ringkasan sinopsis untuk koleksi buku ini.';

            const stokBadge = document.getElementById('mStokBadge');
            if (parseInt(buku.stok_tersedia) > 0) {
                stokBadge.className = 'px-2.5 py-1 rounded-md bg-emerald-50 text-[#2F6E4E] dark:bg-emerald-950/60 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5';
                stokBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-[#2F6E4E]"></span> Stok: ' + buku.stok_tersedia + ' Eksemplar Siap Pinjam';
            } else {
                stokBadge.className = 'px-2.5 py-1 rounded-md bg-rose-50 text-[#C1613A] dark:bg-rose-950/60 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800';
                stokBadge.textContent = 'Semua Buku Sedang Dipinjam';
            }

            const coverImg = document.getElementById('mCoverImg');
            const coverPlaceholder = document.getElementById('mCoverPlaceholder');
            if (buku.cover) {
                coverImg.src = '<?= base_url() ?>/' + buku.cover;
                coverImg.classList.remove('hidden');
                coverPlaceholder.classList.add('hidden');
                coverPlaceholder.classList.remove('flex');
            } else {
                coverImg.classList.add('hidden');
                coverPlaceholder.classList.remove('hidden');
                coverPlaceholder.classList.add('flex');
            }

            const modal = document.getElementById('modalDetail');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (window.lucide) lucide.createIcons();
        }

        function closeDetailModal() {
            const modal = document.getElementById('modalDetail');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close on ESC key or backdrop click
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDetailModal();
        });
        document.getElementById('modalDetail').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalDetail')) closeDetailModal();
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
