# Location Workspace (Deterministik)

Bu alan il/ilce/mahalle sozlugunu DB'den bagimsiz sekilde sabitlemek icindir.

## Kaynak
1. `D:\stack-data\catalog-dataset\out\manifests\options\districts.tr.json`
2. `D:\stack-data\catalog-dataset\out\manifests\options\neighborhoods.tr.json`

## Cikti
1. `05-db-ready/cities_v1.csv`
2. `05-db-ready/districts_v1.csv`
3. `05-db-ready/neighborhoods_v1.csv`
4. `05-db-ready/manifest_v1.json`
5. `03-analysis/location_anomalies_v1.txt`

## Uretim Komutu
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\build-location-snapshot.ps1
```

## Kural
1. DB'ye dogrudan ham json basma.
2. Sadece `manifest_v1.json` ile gelen snapshot'tan import et.
3. Sorun olursa ayni versiyonu tekrar bas (idempotent).

## Guncel Durum (2026-03-11)
1. Snapshot uretildi ve `docs/category-catalog/ready/locations_v1/` altina alindi.
2. Import deterministic servisle yapiliyor:
   - `cities`, `districts`, `neighborhoods` tablolari snapshot + checksum ile beslenir.
3. `listings.city_id` / `district_id` / `neighborhood_id` alanlari bu sozluk tablolara baglidir.
4. Tasarim amaci:
   - serbest metin konum sapmalarini engellemek
   - admin/owner ilan girisinde il-ilce-mahalle secimini deterministic kilitlemek.
5. Geriye uyumluluk:
   - mevcut public filtre/route davranisi icin `listings.city` / `listings.district` alanlari ID'den turetilerek yazilir.

## Kapanis Notu (2026-03-11)
1. Location fazi (il/ilce/mahalle sozlugu + admin/owner deterministic giris) tamamlandi.
2. `/ilanlar` filtre ekraninda sehir seciminde ilceler anlik (sayfa yenilemeden) dolacak sekilde duzeltildi.
3. Iki app parity dogrulandi (`orkestram` + `izmirorkestra`).
4. Service area deterministic fazi tamamlandi:
   - `listing_service_areas.city_id` + `district_id` aktif
   - migration backfill ile mevcut satirlar ID'ye eslendi (munkun oldugu kadar)
   - coverage sorgusu ID-oncelikli + string fallback olacak sekilde guncellendi.
