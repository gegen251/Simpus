<?php
/**
 * Komponen Reusable: Badge Status SIMPUS SD
 * Parameter:
 * - $status: string
 * - $size: string ('sm' | 'md', default: 'sm')
 */
$status = strtolower(trim($status ?? ''));
$size   = $size ?? 'sm';
$pad    = ($size === 'md') ? 'px-2.5 py-1 text-xs' : 'px-2 py-0.5 text-[11px]';

$configs = [
    // Sirkulasi
    'dipinjam' => [
        'bg'    => 'bg-amber-50 dark:bg-amber-950/60',
        'text'  => 'text-amber-800 dark:text-amber-300',
        'border'=> 'border-amber-200 dark:border-amber-800/60',
        'dot'   => 'bg-amber-500',
        'label' => 'Dipinjam',
    ],
    'terlambat' => [
        'bg'    => 'bg-rose-50 dark:bg-rose-950/60',
        'text'  => 'text-rose-800 dark:text-rose-300',
        'border'=> 'border-rose-200 dark:border-rose-800/60',
        'dot'   => 'bg-rose-500',
        'label' => 'Terlambat',
    ],
    'kembali' => [
        'bg'    => 'bg-emerald-50 dark:bg-emerald-950/60',
        'text'  => 'text-emerald-800 dark:text-emerald-300',
        'border'=> 'border-emerald-200 dark:border-emerald-800/60',
        'dot'   => 'bg-emerald-500',
        'label' => 'Dikembalikan',
    ],
    'dikembalikan' => [
        'bg'    => 'bg-emerald-50 dark:bg-emerald-950/60',
        'text'  => 'text-emerald-800 dark:text-emerald-300',
        'border'=> 'border-emerald-200 dark:border-emerald-800/60',
        'dot'   => 'bg-emerald-500',
        'label' => 'Dikembalikan',
    ],

    // Denda
    'lunas' => [
        'bg'    => 'bg-emerald-50 dark:bg-emerald-950/60',
        'text'  => 'text-emerald-800 dark:text-emerald-300',
        'border'=> 'border-emerald-200 dark:border-emerald-800/60',
        'dot'   => 'bg-emerald-500',
        'label' => 'Lunas',
    ],
    'belum_lunas' => [
        'bg'    => 'bg-rose-50 dark:bg-rose-950/60',
        'text'  => 'text-rose-800 dark:text-rose-300',
        'border'=> 'border-rose-200 dark:border-rose-800/60',
        'dot'   => 'bg-rose-500',
        'label' => 'Belum Lunas',
    ],
    'tidak_ada' => [
        'bg'    => 'bg-slate-50 dark:bg-slate-800/60',
        'text'  => 'text-slate-600 dark:text-slate-400',
        'border'=> 'border-slate-200 dark:border-slate-700',
        'dot'   => 'bg-slate-400',
        'label' => 'Bebas Denda',
    ],

    // Anggota & Koleksi
    'aktif' => [
        'bg'    => 'bg-emerald-50 dark:bg-emerald-950/60',
        'text'  => 'text-emerald-800 dark:text-emerald-300',
        'border'=> 'border-emerald-200 dark:border-emerald-800/60',
        'dot'   => 'bg-emerald-500',
        'label' => 'Aktif',
    ],
    'nonaktif' => [
        'bg'    => 'bg-slate-100 dark:bg-slate-800',
        'text'  => 'text-slate-600 dark:text-slate-400',
        'border'=> 'border-slate-200 dark:border-slate-700',
        'dot'   => 'bg-slate-400',
        'label' => 'Nonaktif',
    ],
    'rusak' => [
        'bg'    => 'bg-orange-50 dark:bg-orange-950/60',
        'text'  => 'text-orange-800 dark:text-orange-300',
        'border'=> 'border-orange-200 dark:border-orange-800/60',
        'dot'   => 'bg-orange-500',
        'label' => 'Rusak',
    ],
    'hilang' => [
        'bg'    => 'bg-rose-50 dark:bg-rose-950/60',
        'text'  => 'text-rose-800 dark:text-rose-300',
        'border'=> 'border-rose-200 dark:border-rose-800/60',
        'dot'   => 'bg-rose-500',
        'label' => 'Hilang',
    ],

    // Role
    'admin' => [
        'bg'    => 'bg-indigo-50 dark:bg-indigo-950/60',
        'text'  => 'text-indigo-800 dark:text-indigo-300',
        'border'=> 'border-indigo-200 dark:border-indigo-800/60',
        'dot'   => 'bg-indigo-500',
        'label' => 'Admin Utama',
    ],
    'staf' => [
        'bg'    => 'bg-slate-100 dark:bg-slate-800',
        'text'  => 'text-slate-700 dark:text-slate-300',
        'border'=> 'border-slate-200 dark:border-slate-700',
        'dot'   => 'bg-slate-400',
        'label' => 'Staf Pustaka',
    ],
];

$cfg = $configs[$status] ?? [
    'bg'    => 'bg-slate-100 dark:bg-slate-800',
    'text'  => 'text-slate-700 dark:text-slate-300',
    'border'=> 'border-slate-200 dark:border-slate-700',
    'dot'   => 'bg-slate-400',
    'label' => ucfirst($status),
];
?>

<span class="inline-flex items-center gap-1.5 <?= $pad ?> rounded-md font-semibold <?= $cfg['bg'] ?> <?= $cfg['text'] ?> border <?= $cfg['border'] ?> shadow-2xs whitespace-nowrap">
    <span class="w-1.5 h-1.5 rounded-full <?= $cfg['dot'] ?> shrink-0"></span>
    <span><?= esc($cfg['label']) ?></span>
</span>
