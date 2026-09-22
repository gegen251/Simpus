<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Kendala Sistem | SIMPUS SD</title>
    
    <!-- Google Fonts: IBM Plex Sans & IBM Plex Serif -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Serif:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Production Compiled Tailwind CSS & Custom Tokens -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body class="bg-slate-50 dark:bg-navy-950 text-slate-800 dark:text-slate-100 font-sans min-h-screen flex items-center justify-center p-4 selection:bg-rose-500 selection:text-white">
    <div class="max-w-lg w-full bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-800 rounded-2xl shadow-xl p-8 text-center relative overflow-hidden">
        <!-- Top Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 via-terracotta-500 to-amber-500"></div>
        
        <!-- Wrench/Alert Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-600 dark:text-rose-400 mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <!-- Status Badge -->
        <div class="inline-block px-3 py-1 mb-3 rounded-full text-xs font-semibold tracking-wider uppercase bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300">
            Sistem Mengalami Kendala
        </div>

        <h1 class="font-serif text-2xl font-bold text-navy-900 dark:text-white mb-2">
            Terjadi Kendala Teknis
        </h1>

        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
            Mohon maaf atas ketidaknyamanannya. Aplikasi sedang mengalami kendala teknis internal dan permintaan Anda tidak dapat diselesaikan saat ini.
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 bg-slate-50 dark:bg-navy-950/70 p-3 rounded-lg border border-slate-200/80 dark:border-navy-800">
            Log insiden telah disimpan ke sistem pemantauan otomatis untuk ditindaklanjuti oleh staf teknis.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="history.back()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 dark:border-navy-700 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Sebelumnya
            </button>
            <a href="<?= site_url('/dashboard') ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-navy-800 hover:bg-navy-700 dark:bg-amber-500 dark:hover:bg-amber-600 text-xs font-semibold text-white dark:text-navy-950 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Ke Beranda Utama
            </a>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-100 dark:border-navy-800/80 text-[11px] text-slate-400 dark:text-slate-500">
            SIMPUS SD Negeri 12 Sumbawa &bull; Sistem Informasi Manajemen Perpustakaan
        </div>
    </div>
</body>
</html>
