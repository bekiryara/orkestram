# NEXT TASK (Tek Aktif Gorev)

Durum: `DONE`  
Sorumlu: `ajan`  
Baslangic: `2026-03-14 02:20`  
Hedef Bitis: `2026-03-14 02:45`

## Gorev Tanimi
- Listing kartini tek partial standardina al ve home/listings/service-category ekranlarinda ayni karti kullan.

## Kapsam (In)
- [x] `frontend/partials/listing-card.blade.php` ortak kart partiali (iki app)
- [x] `home`, `listings`, `service-category` ekranlarinda ortak karta gecis (iki app)
- [x] Home + category aksinda kart ozellikleri (`cardAttributesByListing`) parity hizasi (iki app)

## Kapsam Disi (Out)
- [x] Fiyat/icerik metin stratejisi degisikligi
- [x] Yeni business logic veya yeni endpoint

## Kabul Kriteri
- [x] Ana sayfa, ilanlar ve kategori kartlari ayni markup ile render olur
- [x] Kart ozellikleri (ornek: enstruman/sure) home + listings + kategori akislarda gorunur
- [x] `scripts/smoke-test.ps1 -App both` PASS

## Zorunlu Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both
```

## Tamamlandiginda Isaretlenecekler
- [x] `docs/WORKLOG.md` kaydi eklendi
- [x] `docs/PROJECT_STATUS_TR.md` guncellendi
