<?php
/**
 * Komponen Reusable: Empty State SIMPUS SD
 * Parameter:
 * - $icon: string (nama Lucide icon, default: 'inbox')
 * - $title: string (judul kondisi kosong)
 * - $message: string (penjelasan singkat / instruksi)
 * - $actionUrl: string|null (URL tombol tindakan)
 * - $actionText: string|null (Label tombol tindakan)
 * - $actionIcon: string|null (Icon Lucide tombol tindakan)
 * - $actionModal: string|null (Target modal jika aksi membuka modal JS)
 */
$icon        = $icon ?? 'inbox';
$title       = $title ?? 'Belum Ada Data';
$message     = $message ?? 'Data yang Anda cari tidak ditemukan atau belum pernah ditambahkan.';
$actionUrl   = $actionUrl ?? null;
$actionText  = $actionText ?? null;
$actionIcon  = $actionIcon ?? 'plus-circle';
$actionModal = $actionModal ?? null;
?>

<div class="text-center py-12 px-4 bg-white dark:bg-[#131B2E] rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3.5 my-3">
    <!-- Contextual Soft Icon Container -->
    <div class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center mx-auto text-[#C1613A] dark:text-[#E07A5F] shadow-2xs">
        <i data-lucide="<?= esc($icon) ?>" class="w-7 h-7 stroke-[1.75]"></i>
    </div>

    <!-- Editorial Hierarchy Content -->
    <div class="space-y-1 max-w-md mx-auto">
        <h4 class="font-serif font-bold text-base text-[#1B2A4A] dark:text-slate-100 tracking-tight">
            <?= esc($title) ?>
        </h4>
        <p class="text-xs text-[#5C6470] dark:text-slate-400 leading-relaxed">
            <?= esc($message) ?>
        </p>
    </div>

    <!-- Purposeful Action Button (WCAG AA Compliant) -->
    <?php if ($actionUrl || $actionModal): ?>
        <div class="pt-1">
            <?php if ($actionModal): ?>
                <button type="button" 
                        onclick="<?= esc($actionModal) ?>" 
                        class="btn-touch px-4 py-2 bg-[#1B2A4A] hover:bg-[#243B53] text-white text-xs font-semibold rounded-lg shadow-xs transition-colors gap-2 border border-[#1B2A4A]">
                    <i data-lucide="<?= esc($actionIcon) ?>" class="w-4 h-4 text-amber-300"></i>
                    <span><?= esc($actionText) ?></span>
                </button>
            <?php else: ?>
                <a href="<?= esc($actionUrl) ?>" 
                   class="btn-touch px-4 py-2 bg-[#1B2A4A] hover:bg-[#243B53] text-white text-xs font-semibold rounded-lg shadow-xs transition-colors gap-2 border border-[#1B2A4A]">
                    <i data-lucide="<?= esc($actionIcon) ?>" class="w-4 h-4 text-amber-300"></i>
                    <span><?= esc($actionText) ?></span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
