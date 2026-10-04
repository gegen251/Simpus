# Hasil Uji Beban Kiosk — SIMPUS

> Generated: 2026-09-23 05:52:30
> Base URL: http://localhost:8080
> Concurrency: 10 request bersamaan

## Ringkasan

| Skenario | Total | Sukses | Err 500 | Timeout | Avg (ms) | Max (ms) | Err% | Status |
|----------|-------|--------|---------|---------|----------|----------|------|--------|
| Katalog Publik (GET /katalog) | 10 | 10 | 0 | 0 | 613 | 1113 | 0% | ✅ PASS |
| Katalog Filter (GET /katalog?...) | 10 | 10 | 0 | 0 | 703 | 1241 | 0% | ✅ PASS |
| Kiosk Cek Anggota (POST /kiosk/cek-anggota) | 10 | 10 | 0 | 0 | 653 | 1179 | 0% | ✅ PASS |
| Kiosk Cek Buku (POST /kiosk/cek-buku) | 10 | 10 | 0 | 0 | 682 | 1181 | 0% | ✅ PASS |
| Mixed Workload (Katalog + Kiosk) | 10 | 10 | 0 | 0 | 631 | 1131 | 0% | ✅ PASS |

## Kriteria Keberhasilan

- [x] 0% error 500/timeout
- [x] Rata-rata response time < 2000ms
- [x] Tidak ada race condition pada stok buku
