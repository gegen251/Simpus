<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIMPUS SD (Sistem Informasi Manajemen Perpustakaan)</title>
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
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            700: '#233760',
                            800: '#1B2A4A',
                            900: '#101B30',
                            950: '#0A1120',
                        },
                        terracotta: {
                            400: '#D9754C',
                            500: '#C1613A',
                            600: '#AB512D',
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
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#FBF9F5] dark:bg-[#0C121E] min-h-screen flex items-center justify-center p-4 sm:p-6 selection:bg-navy-800 selection:text-white relative font-sans transition-colors duration-200">

    <!-- Top Loading Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-terracotta-500 transform -translate-x-full transition-transform duration-700 ease-out z-50"></div>

    <!-- Main Container -->
    <div class="w-full max-w-md relative z-10">

        <!-- Top Header Bar with Tombol Kembali -->
        <div class="mb-3.5 flex items-center justify-between">
            <a href="<?= site_url('/katalog') ?>" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300 hover:text-navy-900 dark:hover:text-white text-xs font-semibold shadow-2xs transition-colors group"
               title="Kembali ke Halaman Katalog Perpustakaan">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-slate-400 group-hover:text-navy-900 dark:group-hover:text-white transition-transform"></i>
                <span>Kembali ke Katalog</span>
            </a>
            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200/80 dark:border-slate-700">Portal Petugas</span>
        </div>

        <!-- Main Card Container (Flat Ledger Card) -->
        <div class="w-full bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-7 sm:p-8 relative transition-colors">
            
            <!-- Header with Official Logo -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto mb-3 rounded-lg bg-slate-50 dark:bg-slate-800 p-2 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                    <img src="<?= base_url('images/logo.png') ?>" alt="SIMPUS Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="font-serif text-2xl font-bold tracking-tight text-navy-900 dark:text-white">
                    SIMPUS SD
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Sistem Informasi Manajemen Perpustakaan</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">SD Negeri 12 Sumbawa</p>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
                    <span class="font-medium"><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-500"></i>
                    <span class="font-medium"><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" action="<?= site_url('/login/process') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Username / Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="username" name="username" value="<?= old('username', 'admin') ?>" required autofocus
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-navy-800 dark:focus:border-amber-400 focus:ring-1 focus:ring-navy-800 transition-colors placeholder:text-slate-400"
                               placeholder="Masukkan username atau email">
                    </div>
                </div>

                <!-- Password Field with View Password Toggle -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="password" name="password" value="admin123" required
                               class="w-full pl-9 pr-10 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-navy-800 dark:focus:border-amber-400 focus:ring-1 focus:ring-navy-800 transition-colors placeholder:text-slate-400"
                               placeholder="Masukkan kata sandi">
                        <!-- Toggle View Password Button -->
                        <button type="button" id="togglePasswordBtn"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors focus:outline-none"
                                title="Tampilkan / Sembunyikan Kata Sandi">
                            <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button with Loading Animation State -->
                <div class="pt-2">
                    <button type="submit" id="submitBtn"
                            class="w-full py-2.5 px-4 rounded-lg bg-navy-800 hover:bg-navy-900 dark:bg-navy-700 dark:hover:bg-navy-600 text-white text-xs font-semibold shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                        <span id="btnText" class="flex items-center gap-1.5">
                            <span>Masuk ke Sistem</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </span>
                        <span id="btnLoading" class="hidden items-center gap-1.5">
                            <svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Memverifikasi...</span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Hint Box -->
            <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs bg-slate-50 dark:bg-slate-800/60 p-3 rounded-lg border border-slate-200/80 dark:border-slate-700">
                <div>
                    <span class="font-bold text-slate-700 dark:text-slate-200 block text-[11px]">Akun Bawaan:</span>
                    <span class="font-mono text-slate-600 dark:text-slate-400 text-[11px]">admin / admin123</span>
                </div>
                <span class="px-2 py-0.5 bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-[10px] font-semibold">Lokal</span>
            </div>

            <!-- Tombol Kembali Sekunder di Bagian Bawah -->
            <div class="mt-4 text-center">
                <a href="<?= site_url('/katalog') ?>" 
                   class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-navy-900 dark:hover:text-white transition-colors font-medium">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i>
                    <span>Kembali ke Katalog Perpustakaan (OPAC)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Interactive Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // 1. Toggle Password Visibility (View Password)
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                // Toggle Icon between eye and eye-off
                toggleBtn.innerHTML = isPassword 
                    ? '<i data-lucide="eye-off" class="w-4 h-4 text-terracotta-500"></i>' 
                    : '<i data-lucide="eye" class="w-4 h-4 text-slate-400"></i>';
                lucide.createIcons();
            });
        }

        // 2. Form Submit Loading State Animation
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        const topProgressBar = document.getElementById('topProgressBar');

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', function (e) {
                // Animate top progress bar
                if (topProgressBar) {
                    topProgressBar.classList.remove('-translate-x-full');
                    topProgressBar.classList.add('translate-x-0');
                }

                // Switch button content to loading spinner
                if (btnText) btnText.classList.add('hidden');
                if (btnLoading) {
                    btnLoading.classList.remove('hidden');
                    btnLoading.classList.add('flex');
                }

                // Disable button to prevent duplicate submissions
                submitBtn.disabled = true;
            });
        }
    </script>
</body>
</html>
