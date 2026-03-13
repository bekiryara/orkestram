# Category Catalog

Bu dizin kategori setlerinin temiz ve yuklemeye hazir kaynagidir.

## Klasorler
- `ready/`: Uretime hazir aktif kategori setleri (tek dogru kaynak).
- `backlog/`: Pasif havuz ve sonradan aktive edilecek kategoriler.
- `archive/`: Eski surumler ve gecmis ciktilar.

## Kurallar
- Deneme dosyasi bu klasore girmez.
- `ready/` icindeki dosyalar deterministic olmalidir.
- Her degisiklikte `ready/manifest.csv` guncellenir.

## Mevcut Surum
- Active set: `ready/FINAL_CATEGORY_ACTIVE_READY_V1.txt`
- Active CSV: `ready/categories_active_ready_v1.csv`
- Passive lock: `backlog/FINAL_CATEGORY_LOCK_V1.txt`
- Passive CSV: `backlog/categories_passive_pool_v1.csv`
