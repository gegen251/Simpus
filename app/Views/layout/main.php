<!DOCTYPE html>
<html lang="id" class="transition-colors duration-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Perpustakaan') ?> - SIMPUS SD</title>
    <link rel="icon" type="image/png" href="<?= base_url('images/logo.png') ?>">
    <!-- Inline script to avoid theme flash (FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: IBM Plex Family (Enterprise Academic Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&family=IBM+Plex+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#F0F4F8',
                            100: '#D9E2EC',
                            200: '#BCCCDC',
                            700: '#243B53',
                            800: '#1B2A4A',
                            900: '#101B30',
                            950: '#0A1120',
                        },
                        terracotta: {
                            50: '#FDF6F3',
                            100: '#FCECE6',
                            500: '#C1613A',
                            600: '#AB512D',
                            700: '#8E3E1F',
                        },
                        mutedgreen: {
                            50: '#F1F7F4',
                            500: '#2F6E4E',
                            600: '#26593F',
                        },
                        mutedamber: {
                            50: '#FDF9F0',
                            500: '#B8791F',
                            600: '#9B6416',
                        }
                    },
                    fontFamily: {
                        sans: ['"IBM Plex Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        serif: ['"IBM Plex Serif"', 'serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
        }
        /* Custom clean scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .dark ::-webkit-scrollbar-track {
            background: #0F172A;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0B111E] text-slate-800 dark:text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-navy-800 selection:text-white transition-colors duration-200 relative">

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation (Bookcloth Deep Navy) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-[#101B30] dark:bg-[#080E1A] text-white flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 border-r border-slate-800/80 shadow-sm">
            <!-- Brand Section -->
            <div>
                <div class="h-16 flex items-center px-4 gap-3 border-b border-slate-800/80 bg-[#0C1527]">
                    <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-800 p-1.5 flex items-center justify-center border border-slate-700/60 shrink-0">
                        <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="overflow-hidden min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h1 class="font-serif font-bold text-base tracking-wide text-white leading-none">SIMPUS</h1>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-slate-950">SD</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate font-medium">SD Negeri 12 Sumbawa</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-145px)]">
                    <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>

                    <a href="<?= site_url('/dashboard') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'dashboard' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($active_menu ?? '') === 'dashboard' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Dashboard Literasi</span>
                    </a>

                    <div class="pt-3 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Koleksi & Pustaka</div>

                    <a href="<?= site_url('/buku') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'buku' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="book-open" class="w-4 h-4 <?= ($active_menu ?? '') === 'buku' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Koleksi Buku SD</span>
                    </a>

                    <a href="<?= site_url('/kategori') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'kategori' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="tag" class="w-4 h-4 <?= ($active_menu ?? '') === 'kategori' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Kategori Koleksi</span>
                    </a>

                    <a href="<?= site_url('/anggota') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'anggota' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="users" class="w-4 h-4 <?= ($active_menu ?? '') === 'anggota' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Data Siswa & Guru</span>
                    </a>

                    <div class="pt-3 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Layanan Sirkulasi</div>

                    <a href="<?= site_url('/peminjaman') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'peminjaman' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 <?= ($active_menu ?? '') === 'peminjaman' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Peminjaman Buku</span>
                    </a>

                    <a href="<?= site_url('/pengembalian') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'pengembalian' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="arrow-down-left" class="w-4 h-4 <?= ($active_menu ?? '') === 'pengembalian' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Pengembalian & Denda</span>
                    </a>

                    <a href="<?= site_url('/notifikasi') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'notifikasi' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="message-circle" class="w-4 h-4 <?= ($active_menu ?? '') === 'notifikasi' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Notifikasi WhatsApp</span>
                    </a>

                    <div class="pt-3 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Laporan & Pengaturan</div>

                    <a href="<?= site_url('/laporan/semua') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'laporan' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                        <i data-lucide="file-text" class="w-4 h-4 <?= ($active_menu ?? '') === 'laporan' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                        <span>Cetak Laporan</span>
                    </a>

                    <?php if ((session()->get('admin_role') ?? 'admin') === 'admin'): ?>
                        <a href="<?= site_url('/staf') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'staf' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                            <i data-lucide="shield-check" class="w-4 h-4 <?= ($active_menu ?? '') === 'staf' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                            <span>Akun Staf Pustaka</span>
                        </a>

                        <a href="<?= site_url('/pengaturan') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors <?= ($active_menu ?? '') === 'pengaturan' ? 'bg-slate-800 text-white font-semibold border-l-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' ?>">
                            <i data-lucide="settings" class="w-4 h-4 <?= ($active_menu ?? '') === 'pengaturan' ? 'text-amber-400' : 'text-slate-400' ?>"></i>
                            <span>Aturan & Profil SD</span>
                        </a>
                    <?php endif; ?>

                    <div class="pt-3 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Portal Publik</div>

                    <a href="<?= site_url('/kiosk') ?>" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 border border-slate-700/60 transition-colors">
                        <div class="flex items-center gap-2">
                            <i data-lucide="scan-line" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Layanan Mandiri Siswa</span>
                        </div>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                    </a>

                    <a href="<?= site_url('/katalog') ?>" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 border border-slate-700/60 transition-colors mt-1">
                        <div class="flex items-center gap-2">
                            <i data-lucide="globe" class="w-3.5 h-3.5 text-sky-400"></i>
                            <span>Katalog OPAC Publik</span>
                        </div>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                    </a>
                </nav>
            </div>

            <!-- User Footer in Sidebar -->
            <div class="p-3 border-t border-slate-800/80 bg-[#0C1527]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-md <?= (session()->get('admin_role') ?? 'admin') === 'admin' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30' ?> flex items-center justify-center font-bold text-xs shrink-0">
                            <?= strtoupper(substr(session()->get('admin_nama') ?? 'P', 0, 1)) ?>
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <p class="text-xs font-semibold text-white truncate"><?= esc(session()->get('admin_nama') ?? 'Pustakawan SD') ?></p>
                            <p class="text-[10px] text-slate-400 truncate"><?= (session()->get('admin_role') ?? 'admin') === 'admin' ? 'Administrator' : 'Staf Pustaka' ?> &bull; <?= esc(session()->get('admin_username') ?? 'admin') ?></p>
                        </div>
                    </div>
                    <button type="button" onclick="openModal('modalKonfirmasiLogout')" title="Keluar Sistem" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md hover:bg-slate-800 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile sidebar -->
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-slate-900/50 hidden md:hidden"></div>

        <!-- Main Content Area -->
        <div class="flex-1 md:ml-64 flex flex-col min-w-0 transition-colors duration-200">
            <!-- Top Navbar (Flat Ledger Header) -->
            <header class="h-16 bg-white dark:bg-[#0F172A] border-b border-slate-200 dark:border-slate-800 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20 transition-colors duration-200 shadow-xs">
                <div class="flex items-center gap-3">
                    <button id="toggle-sidebar" class="md:hidden p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    
                    <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 shrink-0 hidden sm:flex items-center justify-center">
                        <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS" class="w-full h-full object-contain">
                    </div>

                    <div>
                        <h2 class="text-base sm:text-lg font-serif font-bold text-slate-900 dark:text-white leading-tight"><?= esc($title ?? 'Dashboard') ?></h2>
                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">SIMPUS SD</span>
                            <span>&bull;</span>
                            <span class="hidden sm:inline"><?= date('d F Y') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Info & Theme Switcher -->
                <div class="flex items-center gap-2.5">
                    <!-- Dark / Light Mode Toggle Button -->
                    <button id="theme-toggle" type="button" onclick="toggleTheme()"
                            class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-2xs flex items-center gap-1.5 focus:outline-none focus:ring-1 focus:ring-navy-800"
                            title="Ganti Tema (Dark / Light)">
                        <span id="theme-toggle-dark-icon" class="hidden">
                            <i data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                        </span>
                        <span id="theme-toggle-light-icon" class="hidden">
                            <i data-lucide="moon" class="w-4 h-4 text-slate-600"></i>
                        </span>
                        <span id="theme-toggle-text" class="text-xs font-medium hidden lg:inline">Mode</span>
                    </button>

                    <!-- Role Badge Indicator in Top Navbar -->
                    <?php if ((session()->get('admin_role') ?? 'admin') === 'admin'): ?>
                        <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-950/60 text-xs font-semibold text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                            <span>Admin</span>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-sky-50 dark:bg-sky-950/60 text-xs font-semibold text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400"></i>
                            <span>Staf</span>
                        </div>
                    <?php endif; ?>

                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

                    <!-- Action Button: Pinjam Buku (Terracotta Solid) -->
                    <a href="<?= site_url('/peminjaman') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-terracotta-500 hover:bg-terracotta-600 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Pinjam Buku</span>
                    </a>
                </div>
            </header>

            <!-- Body Content Container -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- Render Injected View Section -->
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer (Clean Ledger Footer) -->
            <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0F172A] py-3.5 px-6 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors duration-200">
                <p>&copy; <?= date('Y') ?> SIMPUS SD &bull; <?= esc($config['nama_perpustakaan'] ?? 'SD Negeri 12 Sumbawa') ?> &bull; Sistem Informasi Manajemen Perpustakaan</p>
            </footer>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- GLOBAL TOAST NOTIFICATION CONTAINER                                       -->
    <!-- ========================================================================= -->
    <div id="toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI LOGOUT                                                    -->
    <!-- ========================================================================= -->
    <div id="modalKonfirmasiLogout" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 transition-all">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col p-6 text-center">
            <div class="w-12 h-12 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 flex items-center justify-center text-rose-600 dark:text-rose-400 mx-auto mb-3">
                <i data-lucide="log-out" class="w-6 h-6"></i>
            </div>

            <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white mb-1.5">Konfirmasi Keluar Sistem</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                Apakah Anda yakin ingin mengakhiri sesi kerja di <strong class="text-slate-700 dark:text-slate-300">SIMPUS SD</strong>? Pastikan seluruh pencatatan transaksi sirkulasi telah disimpan.
            </p>

            <!-- User Info Card -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center gap-3 text-left mb-5">
                <div class="w-9 h-9 rounded-md <?= (session()->get('admin_role') ?? 'admin') === 'admin' ? 'bg-amber-500/20 text-amber-500 border border-amber-500/30' : 'bg-sky-500/20 text-sky-500 border border-sky-500/30' ?> flex items-center justify-center font-bold text-xs shrink-0">
                    <?= strtoupper(substr(session()->get('admin_nama') ?? 'P', 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate"><?= esc(session()->get('admin_nama') ?? 'Pustakawan SD') ?></p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate"><?= esc(session()->get('admin_username') ?? 'admin') ?> &bull; <?= (session()->get('admin_role') ?? 'admin') === 'admin' ? 'Administrator' : 'Staf Pustaka' ?></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-2.5">
                <button type="button" onclick="closeModal('modalKonfirmasiLogout')" class="w-full py-2 px-3 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </button>
                <a href="<?= site_url('/logout') ?>" class="w-full py-2 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Ya, Keluar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Theme Switcher & Sidebar Script -->
    <script>
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const textLabel = document.getElementById('theme-toggle-text');

            if (isDark) {
                darkIcon?.classList.remove('hidden');
                lightIcon?.classList.add('hidden');
                if (textLabel) textLabel.innerText = 'Gelap';
            } else {
                darkIcon?.classList.add('hidden');
                lightIcon?.classList.remove('hidden');
                if (textLabel) textLabel.innerText = 'Terang';
            }
            if (window.lucide) lucide.createIcons();
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcons();

            // Trigger custom event so page charts can adapt colors
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { dark: !isDark } }));
        }

        // Sidebar mobile toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('toggle-sidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        // Generic Modal Helper
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                if (window.lucide) lucide.createIcons();
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // =====================================================================
        // FLOATING TOAST NOTIFICATION SYSTEM
        // =====================================================================
        function escapeHTML(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML.replace(/\n/g, '<br>').replace(/&lt;br\s*\/?&gt;/gi, '<br>');
        }

        function showToast(title, message, type = 'success', duration = 4000) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto transform transition-all duration-200 ease-out translate-y-[-6px] opacity-0 bg-white dark:bg-slate-900 rounded-xl p-3.5 shadow-md border flex items-start gap-3 relative overflow-hidden text-xs';
            
            let borderClass = 'border-slate-200 dark:border-slate-800';
            let iconHtml = '';
            let barColor = 'bg-emerald-500';

            if (type === 'success') {
                borderClass = 'border-emerald-200 dark:border-emerald-800/80';
                barColor = 'bg-emerald-500';
                iconHtml = '<div class="w-7 h-7 rounded-md bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0"><i data-lucide="check-circle-2" class="w-4 h-4"></i></div>';
            } else if (type === 'error') {
                borderClass = 'border-rose-200 dark:border-rose-800/80';
                barColor = 'bg-rose-500';
                iconHtml = '<div class="w-7 h-7 rounded-md bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>';
            } else if (type === 'warning') {
                borderClass = 'border-amber-200 dark:border-amber-800/80';
                barColor = 'bg-amber-500';
                iconHtml = '<div class="w-7 h-7 rounded-md bg-amber-50 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0"><i data-lucide="alert-triangle" class="w-4 h-4"></i></div>';
            } else {
                borderClass = 'border-sky-200 dark:border-sky-800/80';
                barColor = 'bg-sky-500';
                iconHtml = '<div class="w-7 h-7 rounded-md bg-sky-50 dark:bg-sky-950/80 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0"><i data-lucide="info" class="w-4 h-4"></i></div>';
            }

            toast.classList.add(...borderClass.split(' '));
            const safeTitle = escapeHTML(title);
            const safeMessage = escapeHTML(message);

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1 pr-2 min-w-0">
                    <h5 class="font-bold text-slate-900 dark:text-slate-100 leading-tight mb-0.5">${safeTitle}</h5>
                    <p class="text-slate-600 dark:text-slate-300 leading-snug break-words">${safeMessage}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-0.5 transition-colors" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-slate-100 dark:bg-slate-800">
                    <div class="h-full ${barColor} transition-all ease-linear" style="width: 100%; transition-duration: ${duration}ms;"></div>
                </div>
            `;

            container.appendChild(toast);
            if (window.lucide) lucide.createIcons();

            // Trigger enter animation
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-6px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
                const bar = toast.querySelector('.absolute div');
                if (bar) {
                    setTimeout(() => { bar.style.width = '0%'; }, 20);
                }
            });

            // Auto dismiss
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 250);
            }, duration);
        }

        // Initialize icons on DOM load and trigger flash toasts
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcons();
            if (window.lucide) lucide.createIcons();

            <?php if (session()->getFlashdata('success')): ?>
                showToast('Berhasil!', <?= json_encode(session()->getFlashdata('success')) ?>, 'success', 4500);
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                showToast('Perhatian / Gagal', <?= json_encode(session()->getFlashdata('error')) ?>, 'error', 5000);
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                showToast('Peringatan', <?= json_encode(session()->getFlashdata('warning')) ?>, 'warning', 4500);
            <?php endif; ?>
            <?php if (session()->getFlashdata('info')): ?>
                showToast('Informasi', <?= json_encode(session()->getFlashdata('info')) ?>, 'info', 4500);
            <?php endif; ?>
        });

        // Global Confirm Delete with POST and CSRF Protection
        function confirmDelete(url, message = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.') {
            if (window.Swal) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Data',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 shadow-xl',
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold text-xs shadow-xs',
                        cancelButton: 'rounded-lg px-4 py-2 font-medium text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '<?= csrf_token() ?>';
                        csrfInput.value = '<?= csrf_hash() ?>';
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else if (confirm(message)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
