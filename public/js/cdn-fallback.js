/**
 * CDN Fallback Loader — SIMPUS Perpustakaan
 * =========================================================================
 * Detects when CDN-loaded libraries fail and loads local fallback copies.
 * This ensures the application remains functional even when CDN providers
 * (unpkg.com, cdn.jsdelivr.net) are unreachable or slow.
 * =========================================================================
 */
(function() {
    'use strict';

    /**
     * Load a script dynamically and return a Promise.
     */
    function loadScript(src) {
        return new Promise(function(resolve, reject) {
            var script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Check each library after DOM is interactive and load fallbacks if needed.
     */
    function checkFallbacks() {
        // Lucide Icons — check if `lucide` global exists
        if (typeof window.lucide === 'undefined') {
            console.warn('[CDN Fallback] Lucide Icons CDN failed — loading local copy.');
            loadScript(window.__BASE_URL__ + 'js/lucide.min.js').then(function() {
                if (typeof window.lucide !== 'undefined' && window.lucide.createIcons) {
                    window.lucide.createIcons();
                    console.info('[CDN Fallback] Lucide Icons loaded from local fallback.');
                }
            }).catch(function() {
                console.error('[CDN Fallback] Lucide Icons local fallback also failed!');
            });
        }

        // Chart.js — check if `Chart` global exists (only on pages that load it)
        if (document.querySelector('script[src*="chart.js"]') && typeof window.Chart === 'undefined') {
            console.warn('[CDN Fallback] Chart.js CDN failed — dashboard charts may not render.');
            // Chart.js is only used on dashboard; not critical for kiosk/katalog
        }

        // SweetAlert2 — check if `Swal` global exists (only on pages that load it)
        if (document.querySelector('script[src*="sweetalert2"]') && typeof window.Swal === 'undefined') {
            console.warn('[CDN Fallback] SweetAlert2 CDN failed — using native confirm() as fallback.');
            // Application already has native confirm() fallback in confirmDelete()
        }
    }

    // Run checks when DOM is ready (scripts should have loaded by then)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkFallbacks);
    } else {
        // DOM already loaded, check immediately
        setTimeout(checkFallbacks, 100);
    }
})();
