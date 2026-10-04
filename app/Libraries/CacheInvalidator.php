<?php

namespace App\Libraries;

/**
 * CacheInvalidator
 * =========================================================================
 * Centralized cache invalidation for the SIMPUS library management system.
 * 
 * Called from any controller that mutates book data (Buku, Kiosk,
 * Peminjaman, Pengembalian) to ensure the public Katalog OPAC always
 * serves fresh data after changes.
 * =========================================================================
 */
class CacheInvalidator
{
    /**
     * Cache key prefix used by Katalog controller for data caching.
     */
    private const KATALOG_PREFIX = 'katalog_';

    /**
     * Known cache keys registered by Katalog controller.
     * We maintain a registry so we can invalidate them precisely
     * without needing to scan the entire cache store.
     */
    private const KATALOG_REGISTRY_KEY = 'katalog_cache_registry';

    /**
     * Invalidate all cached Katalog data.
     *
     * This clears:
     * - All data-level caches (book list, kategori, totals)
     * - The page-level cache for /katalog routes
     *
     * Should be called after any operation that changes book data:
     * - Buku::store(), update(), delete(), importCsv()
     * - Kiosk::apiProsesPinjam(), apiProsesKembali()
     * - Peminjaman::store(), perpanjang()
     * - Pengembalian::save()
     */
    public static function invalidateKatalog(): void
    {
        $cache = \Config\Services::cache();

        // 1. Delete all registered katalog cache keys
        $registry = $cache->get(self::KATALOG_REGISTRY_KEY);
        if (is_array($registry)) {
            foreach ($registry as $key) {
                $cache->delete($key);
            }
        }
        $cache->delete(self::KATALOG_REGISTRY_KEY);

        // 2. Delete well-known fixed keys
        $fixedKeys = [
            'katalog_kategori_list',
            'katalog_total_buku',
            'katalog_total_tersedia',
            'katalog_buku_default',
            'katalog_config',
        ];
        foreach ($fixedKeys as $key) {
            $cache->delete($key);
        }

        // 3. Clear CI4 page cache for katalog routes
        // The PageCache filter stores output in writable/cache with URI-based keys
        $cachePath = WRITEPATH . 'cache/';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '*');
            if ($files) {
                foreach ($files as $file) {
                    $basename = basename($file);
                    // CI4 PageCache keys contain the URI hash
                    // We target files that might be page cache for /katalog
                    if (strpos($basename, 'katalog') !== false) {
                        @unlink($file);
                    }
                }
            }
        }

        log_message('info', '[CacheInvalidator] Katalog cache invalidated.');
    }

    /**
     * Register a cache key in the katalog registry.
     * This allows precise invalidation later.
     */
    public static function registerKatalogKey(string $key): void
    {
        $cache = \Config\Services::cache();
        $registry = $cache->get(self::KATALOG_REGISTRY_KEY);

        if (!is_array($registry)) {
            $registry = [];
        }

        if (!in_array($key, $registry, true)) {
            $registry[] = $key;
            // Registry lives as long as the longest cache TTL (10 min + buffer)
            $cache->save(self::KATALOG_REGISTRY_KEY, $registry, 900);
        }
    }
}
