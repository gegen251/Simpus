<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Punggung & Barcode Buku - <?= esc($config['nama_perpustakaan'] ?? 'SIMPUS') ?></title>
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,500;0,600;0,700;1,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Serif:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Production Compiled Tailwind CSS & Custom Tokens -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* Container Lembar Kertas A4 */
        .sheet-container {
            width: 196mm;
            margin: 0 auto;
        }

        /* GRID LABEL LENGKAP: 2 Kolom (95mm x 36mm per label) */
        .grid-all {
            display: grid;
            grid-template-columns: repeat(2, 95mm);
            gap: 2.5mm 6mm;
            justify-content: center;
        }

        /* GRID LABEL PUNGGUNG SAJA: 4 Kolom (46mm x 36mm) */
        .grid-spine {
            display: grid;
            grid-template-columns: repeat(4, 46mm);
            gap: 2.5mm 4mm;
            justify-content: center;
        }

        /* GRID BARCODE SAJA: 2 Kolom (95mm x 32mm) */
        .grid-barcode {
            display: grid;
            grid-template-columns: repeat(2, 95mm);
            gap: 2.5mm 6mm;
            justify-content: center;
        }

        /* KARTU LABEL BASE */
        .label-card {
            background: #ffffff;
            border: 1px solid #475569;
            border-radius: 1.8mm;
            page-break-inside: avoid;
            break-inside: avoid;
            overflow: hidden;
            position: relative;
        }

        /* Dimensi Mode Lengkap */
        .card-all {
            width: 95mm;
            height: 36mm;
            display: flex;
        }

        /* Dimensi Mode Punggung */
        .card-spine {
            width: 46mm;
            height: 36mm;
            display: flex;
            flex-direction: column;
        }

        /* Dimensi Mode Barcode */
        .card-barcode {
            width: 95mm;
            height: 32mm;
            display: flex;
            flex-direction: column;
            padding: 1.5mm 3mm;
        }

        /* Sisi Punggung (Call Number) */
        .spine-part {
            border-right: 1px dashed #94a3b8;
            padding: 1.5mm 1.5mm 1mm 1.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            background: #fafafa;
            min-width: 0;
            flex-shrink: 0;
        }
        .card-all .spine-part {
            width: 32mm;
            height: 100%;
        }
        .card-spine .spine-part {
            width: 100%;
            height: 100%;
            border-right: none;
            padding: 2mm 3mm;
        }

        /* Sisi Barcode */
        .barcode-part {
            padding: 1.5mm 2.5mm 1mm 2.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: #ffffff;
            min-width: 0;
        }
        .card-all .barcode-part {
            flex: 1;
            height: 100%;
        }
        .card-barcode .barcode-part {
            width: 100%;
            height: 100%;
        }

        /* Judul Buku Clamped 2 Baris (Tidak Terpotong Sembarangan) */
        .book-title-clamped {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.25;
            font-size: 7.5px;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
            max-height: 2.55em;
            word-break: break-word;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet-container {
                width: 100% !important;
                margin: 0 !important;
            }
            .label-card {
                border: 0.8px solid #475569 !important;
                box-shadow: none !important;
            }
            .spine-part {
                background: #f8fafc !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 text-slate-800 bg-[#FBF9F5]">

    <!-- Top Action & Config Bar (No Print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 p-4 sm:p-5 bg-white rounded-xl shadow-xs border border-[#E5DFD3]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="<?= site_url('/buku') ?>" class="px-3.5 py-2 text-xs font-semibold text-[#5C6470] bg-[#F8F6F0] hover:bg-[#EFECE6] rounded-lg border border-[#E5DFD3] transition-colors flex items-center gap-1.5 shrink-0">
                    <span>&larr;</span>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="font-serif font-bold text-base text-[#1B2A4A] flex items-center gap-2">
                        <span>Pratinjau Cetak Label Buku</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-[#1B2A4A]/10 text-[#1B2A4A] rounded border border-[#1B2A4A]/20">
                            <?= esc($totalLabels ?? count($bukuList)) ?> Label Fisik
                        </span>
                    </h1>
                    <p class="text-xs text-[#5C6470] mt-0.5">
                        Total: <strong><?= esc($totalJudul ?? count($bukuList)) ?> Judul Buku</strong> |
                        Mode: <strong><?= ($qtyMode ?? 'single') === 'eksemplar' ? 'Per Eksemplar Fisik' : '1 Label per Judul' ?></strong>
                    </p>
                </div>
            </div>

            <!-- Interactive Filters & Controls -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Select Label Type -->
                <div class="flex items-center bg-[#F8F6F0] p-1 rounded-lg border border-[#E5DFD3] text-xs font-semibold text-[#5C6470]">
                    <button type="button" onclick="switchLayout('all')" id="btn-tab-all" class="tab-btn px-3 py-1.5 rounded-md bg-white text-[#1B2A4A] shadow-xs font-bold transition-all">
                        Lengkap
                    </button>
                    <button type="button" onclick="switchLayout('spine')" id="btn-tab-spine" class="tab-btn px-3 py-1.5 rounded-md text-[#5C6470] hover:text-[#1B2A4A] transition-all">
                        Punggung
                    </button>
                    <button type="button" onclick="switchLayout('barcode')" id="btn-tab-barcode" class="tab-btn px-3 py-1.5 rounded-md text-[#5C6470] hover:text-[#1B2A4A] transition-all">
                        Barcode
                    </button>
                </div>

                <!-- Print Button -->
                <button onclick="window.print()" class="px-4 py-2 bg-[#1B2A4A] hover:bg-[#24375D] text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Sekarang (Ctrl+P)</span>
                </button>
            </div>
        </div>

        <!-- Tips Cetak Rapi -->
        <div class="mt-3 pt-3 border-t border-[#E5DFD3]/70 flex flex-wrap items-center justify-between text-[11px] text-[#5C6470] gap-2">
            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-[#B8791F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <strong>Tips Cetak Rapi:</strong> Pada dialog print browser, pilih <em>Paper Size: A4</em>, <em>Margins: None / Minimum</em>, dan centang <em>"Background graphics"</em>.</span>
            <span class="font-mono text-[10px] text-[#1B2A4A] font-semibold">Ukuran Stiker: 95mm &times; 36mm</span>
        </div>
    </div>

    <!-- Sheet Container -->
    <div class="sheet-container">
        <div id="labelsGrid" class="grid-all">
            <?php 
                // Format Nama Perpustakaan / Sekolah untuk Label Punggung (Lebar 32mm)
                $namaPerpusFull = $config['nama_perpustakaan'] ?? 'PERPUSTAKAAN';

                // Bersihkan atau perpendek nama jika terlalu panjang agar tidak terpotong tanda kurung
                if (preg_match('/^(.*?)\s*\((.*?)\)$/', $namaPerpusFull, $matchesName)) {
                    $namaSekolah = trim($matchesName[1]);
                    $namaPerpus  = trim($matchesName[2]);
                    // Singkat 'SD Negeri ' menjadi 'SDN ' agar muat rapi di 1 baris
                    $headerSpine = str_ireplace(['SD Negeri ', 'Sekolah Dasar Negeri '], 'SDN ', $namaSekolah);
                    if (strlen($headerSpine) > 22) {
                        $headerSpine = $namaPerpus;
                    }
                } else {
                    $headerSpine = str_ireplace(['SD Negeri ', 'Sekolah Dasar Negeri '], 'SDN ', $namaPerpusFull);
                }
            ?>

            <?php foreach ($bukuList as $index => $b): ?>
                <?php 
                    // Format Call Number / Nomor Panggil
                    $ddc = '899.221'; // Default sastra indonesia
                    $namaKat = strtolower($b['nama_kategori'] ?? '');
                    if (str_contains($namaKat, 'tematik') || str_contains($namaKat, 'pelajaran')) {
                        $ddc = '372.1';
                    } elseif (str_contains($namaKat, 'sains') || str_contains($namaKat, 'ensiklopedia')) {
                        $ddc = '500';
                    } elseif (str_contains($namaKat, 'agama') || str_contains($namaKat, 'karakter')) {
                        $ddc = '297';
                    } elseif (str_contains($namaKat, 'kamus') || str_contains($namaKat, 'bahasa')) {
                        $ddc = '499.221';
                    } elseif (str_contains($namaKat, 'komik') || str_contains($namaKat, 'cerita') || str_contains($namaKat, 'dongeng')) {
                        $ddc = '899.223';
                    }

                    // 3 Huruf Pengarang Kapital
                    $penulisClean = preg_replace('/[^a-zA-Z]/', '', $b['penulis'] ?? 'NN');
                    $tigaHurufPenulis = strtoupper(substr($penulisClean, 0, 3));

                    // 1 Huruf Judul Kecil
                    $judulWords = explode(' ', trim($b['judul'] ?? 'b'));
                    $firstWord = strtolower($judulWords[0] ?? 'b');
                    if (in_array($firstWord, ['si', 'sang', 'the', 'a']) && isset($judulWords[1])) {
                        $satuHurufJudul = strtolower(substr($judulWords[1], 0, 1));
                    } else {
                        $satuHurufJudul = strtolower(substr($firstWord, 0, 1));
                    }

                    $salinanKe = $b['salinan_ke'] ?? 1;
                    $totalSalinan = $b['total_salinan'] ?? $b['jumlah_eksemplar'];
                ?>

                <!-- Label Item -->
                <div class="label-card card-all" data-id="<?= $b['id'] ?>">
                    
                    <!-- SISI KIRI: LABEL PUNGGUNG BUKU (SPINE) -->
                    <div class="spine-part">
                        <div class="text-[6.5px] font-black uppercase tracking-tight text-indigo-950 border-b border-slate-300 pb-0.5 leading-tight text-center whitespace-normal" title="<?= esc($namaPerpusFull) ?>">
                            <?= esc(strtoupper($headerSpine)) ?>
                        </div>
                        
                        <div class="my-auto font-bold text-slate-900 leading-tight space-y-0.5 py-0.5">
                            <div class="text-[10.5px] font-mono tracking-wider text-indigo-950 font-black"><?= $ddc ?></div>
                            <div class="text-[10px] uppercase font-mono tracking-widest text-slate-800 font-bold"><?= $tigaHurufPenulis ?></div>
                            <div class="text-[9.5px] font-mono lowercase text-slate-700 font-bold"><?= $satuHurufJudul ?></div>
                            <?php if ($totalSalinan > 1): ?>
                                <div class="text-[7.5px] font-mono text-indigo-700 font-extrabold">c.<?= $salinanKe ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="text-[6.5px] font-bold text-slate-600 border-t border-slate-300 pt-0.5 leading-none text-center truncate px-0.5" title="<?= esc($b['lokasi_rak'] ?: 'Rak Utama') ?>">
                            <?= esc($b['lokasi_rak'] ?: 'Rak Utama') ?>
                        </div>
                    </div>

                    <!-- SISI KANAN: BARCODE & KODE BUKU -->
                    <div class="barcode-part">
                        <!-- Judul Buku 2 Baris Rapih -->
                        <div class="w-full px-0.5">
                            <div class="book-title-clamped" title="<?= esc($b['judul']) ?>">
                                <?= esc($b['judul']) ?>
                            </div>
                        </div>

                        <!-- Barcode SVG & Kode Buku -->
                        <div class="my-auto flex flex-col items-center justify-center w-full py-0.5">
                            <svg class="barcode-svg" id="barcode-<?= $index ?>" data-code="<?= esc($b['kode_buku']) ?>" style="height: 20px; max-width: 100%;"></svg>
                            <span class="text-[7.5px] font-mono font-bold text-slate-900 tracking-wider mt-0.5 leading-none"><?= esc($b['kode_buku']) ?></span>
                        </div>

                        <!-- Footer Kategori & Salinan Eksemplar -->
                        <div class="w-full flex items-center justify-between text-[6.5px] border-t border-slate-200 pt-0.5 px-0.5 leading-none">
                            <span class="truncate text-slate-500 font-medium mr-1.5 flex-1 text-left" title="<?= esc($b['nama_kategori'] ?? 'Koleksi SD') ?>">
                                <?= esc($b['nama_kategori'] ?? 'Koleksi SD') ?>
                            </span>
                            <span class="font-bold text-indigo-900 shrink-0 whitespace-nowrap bg-indigo-50 px-1 py-0.5 rounded border border-indigo-200/80 text-[6px]">
                                <?= $totalSalinan > 1 ? "Salinan {$salinanKe}/{$totalSalinan}" : "Eks: {$b['jumlah_eksemplar']}" ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Script Generasi Barcode & Switch Layout -->
    <script>
        function generateBarcodes() {
            const elements = document.querySelectorAll('.barcode-svg');
            elements.forEach(el => {
                const code = el.getAttribute('data-code');
                if (code) {
                    JsBarcode(el, code, {
                        format: "CODE128",
                        lineColor: "#000000",
                        width: 1.25,
                        height: 20,
                        displayValue: false,
                        margin: 0
                    });
                }
            });
        }

        function switchLayout(mode) {
            const grid = document.getElementById('labelsGrid');
            const cards = document.querySelectorAll('.label-card');
            const spineParts = document.querySelectorAll('.spine-part');
            const barcodeParts = document.querySelectorAll('.barcode-part');

            // Update Tab Button Styles
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-white', 'text-indigo-700', 'shadow-xs', 'font-bold');
                b.classList.add('text-slate-600');
            });
            const activeBtn = document.getElementById('btn-tab-' + mode);
            if (activeBtn) {
                activeBtn.classList.add('bg-white', 'text-indigo-700', 'shadow-xs', 'font-bold');
                activeBtn.classList.remove('text-slate-600');
            }

            if (mode === 'all') {
                grid.className = 'grid-all';
                cards.forEach(c => {
                    c.className = 'label-card card-all';
                });
                spineParts.forEach(s => s.classList.remove('hidden'));
                barcodeParts.forEach(b => b.classList.remove('hidden'));
            } else if (mode === 'spine') {
                grid.className = 'grid-spine';
                cards.forEach(c => {
                    c.className = 'label-card card-spine';
                });
                spineParts.forEach(s => s.classList.remove('hidden'));
                barcodeParts.forEach(b => b.classList.add('hidden'));
            } else if (mode === 'barcode') {
                grid.className = 'grid-barcode';
                cards.forEach(c => {
                    c.className = 'label-card card-barcode';
                });
                spineParts.forEach(s => s.classList.add('hidden'));
                barcodeParts.forEach(b => b.classList.remove('hidden'));
            }

            generateBarcodes();
        }

        document.addEventListener('DOMContentLoaded', () => {
            generateBarcodes();
            <?php if (($labelType ?? 'all') !== 'all'): ?>
                switchLayout('<?= esc($labelType) ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html>
