<?php
/**
 * Komponen Reusable: Skeleton Loading State SIMPUS SD
 * Parameter:
 * - $rows: int (jumlah baris skeleton, default: 4)
 * - $type: string ('table' | 'cards' | 'simple', default: 'table')
 */
$rows = $rows ?? 4;
$type = $type ?? 'table';
?>

<?php if ($type === 'cards'): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 animate-pulse">
        <?php for ($i = 0; $i < $rows; $i++): ?>
            <div class="bg-white dark:bg-[#131B2E] p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="h-36 rounded-lg skeleton-shimmer"></div>
                <div class="h-4 w-3/4 rounded skeleton-shimmer"></div>
                <div class="h-3 w-1/2 rounded skeleton-shimmer"></div>
                <div class="flex justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="h-3 w-1/4 rounded skeleton-shimmer"></div>
                    <div class="h-3 w-1/4 rounded skeleton-shimmer"></div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

<?php elseif ($type === 'simple'): ?>
    <div class="space-y-2.5 animate-pulse py-2">
        <?php for ($i = 0; $i < $rows; $i++): ?>
            <div class="h-10 rounded-lg skeleton-shimmer"></div>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <!-- Table Shimmer Rows -->
    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#131B2E] shadow-xs animate-pulse">
        <div class="h-11 bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-800"></div>
        <div class="p-4 space-y-3.5">
            <?php for ($i = 0; $i < $rows; $i++): ?>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-lg skeleton-shimmer shrink-0"></div>
                    <div class="h-4 w-1/4 rounded skeleton-shimmer"></div>
                    <div class="h-4 w-1/3 rounded skeleton-shimmer"></div>
                    <div class="h-4 w-1/6 rounded skeleton-shimmer"></div>
                    <div class="h-4 w-16 ml-auto rounded skeleton-shimmer"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>
