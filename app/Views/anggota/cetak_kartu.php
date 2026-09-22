<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Anggota Perpustakaan - <?= esc($config['nama_perpustakaan'] ?? 'SIMPUS') ?></title>
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,500;0,600;0,700;1,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Serif:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"IBM Plex Sans"', 'sans-serif'],
                        serif: ['"IBM Plex Serif"', 'serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background-color: #FBF9F5;
            color: #1B2A4A;
        }

        .id-card {
            width: 85.6mm;
            height: 54mm;
            box-sizing: border-box;
            position: relative;
            background: #ffffff;
            border-radius: 3.5mm;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(27, 42, 74, 0.08);
            border: 1px solid #D8D2C5;
            page-break-inside: avoid;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .id-card {
                box-shadow: none !important;
                border: 0.8px solid #94a3b8 !important;
                margin-bottom: 5mm;
            }
            .card-grid {
                display: grid;
                grid-template-columns: repeat(2, 85.6mm);
                gap: 6mm;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6">

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 p-4 bg-white rounded-xl shadow-xs border border-[#E5DFD3] flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= site_url('/anggota') ?>" class="px-3.5 py-2 text-xs font-semibold text-[#5C6470] bg-[#F8F6F0] hover:bg-[#EFECE6] rounded-lg border border-[#E5DFD3] transition-colors flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Kembali ke Data Anggota</span>
            </a>
            <div>
                <h1 class="font-serif font-bold text-base text-[#1B2A4A]">Pratinjau Cetak Kartu Anggota</h1>
                <p class="text-xs text-[#5C6470]">Jumlah: <?= count($anggota) ?> kartu siap dicetak (Kertas ID Card / HVS / Glossy A4)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#1B2A4A] hover:bg-[#24375D] text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu Sekarang (Ctrl+P)</span>
            </button>
        </div>
    </div>

    <!-- Cards Container -->
    <div class="card-grid max-w-4xl mx-auto flex flex-wrap justify-center gap-6">
        <?php foreach ($anggota as $index => $a): ?>
            <div class="id-card flex flex-col justify-between p-2.5 relative">
                <!-- Card Header -->
                <div class="flex items-center gap-2 border-b border-[#E5DFD3] pb-1.5 relative z-10">
                    <div class="w-8 h-8 rounded bg-[#1B2A4A] flex items-center justify-center text-white shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="overflow-hidden leading-tight flex-1">
                        <h2 class="text-[9.5px] font-serif font-bold text-[#1B2A4A] uppercase tracking-tight truncate">
                            <?= esc($config['nama_perpustakaan'] ?? 'SD NEGERI 12 SUMBAWA') ?>
                        </h2>
                        <p class="text-[7.5px] text-[#5C6470] truncate">
                            <?= esc($config['alamat_perpustakaan'] ?? 'Jl. Pendidikan No. 01, Sumbawa') ?>
                        </p>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[7px] font-bold tracking-wider uppercase bg-[#F8F6F0] text-[#1B2A4A] border border-[#E5DFD3]">
                        <?= esc($a['tipe_anggota'] ?? 'siswa') ?>
                    </span>
                </div>

                <!-- Card Body -->
                <div class="flex gap-2.5 items-center my-auto py-1 relative z-10">
                    <!-- Photo Box (3:4 ratio standard) -->
                    <div class="w-16 h-20 bg-[#F8F6F0] rounded border border-[#E5DFD3] flex flex-col items-center justify-center text-slate-400 shrink-0 overflow-hidden relative">
                        <?php if (!empty($a['foto']) && file_exists(FCPATH . $a['foto'])): ?>
                            <img src="<?= base_url(esc($a['foto'])) ?>" alt="<?= esc($a['nama']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-[#1B2A4A]/10 text-[#1B2A4A] flex items-center justify-center font-bold text-xs">
                                <?= strtoupper(substr($a['nama'], 0, 1)) ?>
                            </div>
                            <span class="text-[7px] font-semibold text-[#5C6470] mt-1 uppercase">Pasfoto</span>
                        <?php endif; ?>
                        <div class="absolute bottom-0 inset-x-0 bg-[#1B2A4A] text-white text-[6.5px] text-center font-bold py-0.2">
                            <?= strtoupper(substr($a['tipe_anggota'] ?? 'siswa', 0, 3)) ?>
                        </div>
                    </div>

                    <!-- Member Details -->
                    <div class="flex-1 space-y-0.5 text-[8.5px] leading-tight">
                        <div class="flex items-center">
                            <span class="w-16 text-[#5C6470] text-[8px]">No. Anggota</span>
                            <span class="font-mono font-bold text-[#1B2A4A] text-[9.5px] tracking-wide">: <?= esc($a['nomor_anggota']) ?></span>
                        </div>
                        <div class="flex items-start">
                            <span class="w-16 text-[#5C6470] text-[8px]">Nama</span>
                            <span class="font-bold text-[#1B2A4A] flex-1 truncate">: <?= esc($a['nama']) ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-16 text-[#5C6470] text-[8px]"><?= $a['tipe_anggota'] === 'guru' ? 'Jabatan' : 'Kelas' ?></span>
                            <span class="font-semibold text-slate-800">: <?= esc($a['kelas'] ?: ($a['tipe_anggota'] === 'guru' ? 'Dewan Guru' : '-')) ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-16 text-[#5C6470] text-[8px]">NISN / NIP</span>
                            <span class="font-mono text-slate-800">: <?= esc($a['no_identitas'] ?: '-') ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-16 text-[#5C6470] text-[8px]">Masa Berlaku</span>
                            <span class="text-[#2F6E4E] font-semibold">: Selama Menjadi <?= ucfirst($a['tipe_anggota'] ?? 'siswa') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer (Barcode & Signature) -->
                <div class="flex items-end justify-between border-t border-[#E5DFD3] pt-1 relative z-10">
                    <!-- Barcode Display -->
                    <div class="flex flex-col items-start">
                        <svg class="barcode-svg" id="barcode-<?= $index ?>" data-code="<?= esc($a['nomor_anggota']) ?>" style="height: 22px; max-width: 140px;"></svg>
                        <span class="text-[7.5px] font-mono font-bold text-[#5C6470] tracking-wider"><?= esc($a['nomor_anggota']) ?></span>
                    </div>

                    <!-- Stamp / Signature Box -->
                    <div class="text-right leading-tight">
                        <p class="text-[6.5px] text-[#5C6470]">Kepala Perpustakaan,</p>
                        <div class="h-3.5 flex items-center justify-end">
                            <span class="text-[7.5px] font-serif italic text-[#1B2A4A] font-bold opacity-80">(Tanda Tangan)</span>
                        </div>
                        <p class="text-[7px] font-bold text-[#1B2A4A] underline"><?= esc($config['kepala_perpustakaan'] ?? 'Kepala Perpustakaan') ?></p>
                        <p class="text-[6px] text-[#5C6470]">NIP. <?= esc($config['nip_kepala'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Generate Barcode with JsBarcode -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const barcodeElements = document.querySelectorAll('.barcode-svg');
            barcodeElements.forEach(el => {
                const code = el.getAttribute('data-code');
                if (code) {
                    JsBarcode(el, code, {
                        format: "CODE128",
                        lineColor: "#1B2A4A",
                        width: 1.35,
                        height: 22,
                        displayValue: false,
                        margin: 0
                    });
                }
            });
        });
    </script>
</body>
</html>
