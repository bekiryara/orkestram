# Icerik Tasima Rehberi (CSV)

Bu adim, yeni sistemde sayfa/ilan/sehir icerigini toplu yuklemek icin kullanilir.

## 1) Hazir CSV Sablonlari

1. `D:\orkestram\docs\import-pages.template.csv`
2. `D:\orkestram\docs\import-listings.template.csv`
3. `D:\orkestram\docs\import-city-pages.template.csv`

Bu dosyalari cogaltip gercek icerikle doldur.

## 2) Import Komutu (Tek Script)

Ornek:

```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\import-content.ps1 -App orkestram -Type pages -CsvPath D:\orkestram\docs\my-pages.csv -Published
```

Desteklenen tipler:
1. `pages`
2. `listings`
3. `city-pages`

Desteklenen app:
1. `orkestram`
2. `izmirorkestra`

## 3) Hemen Sonra Kontrol

1. `http://localhost:8180/admin/pages` (veya 8181)
2. `http://localhost:8180/` ana sayfa kartlari
3. ilgili detay URL:
   - `/sayfa/{slug}`
   - `/ilan/{slug}`
   - `/sehir/{slug}`

## 4) Kural

1. Once 20 kritik URL icerigini tasi.
2. Sonra kalan batch import.
3. Redirectler hala kapali kalacak (faz 6'ya kadar).

## 5) Zorunlu Alan Kurallari

Import sirasinda zorunlu alanlar bos birakilmamalidir.

1. pages:
- Zorunlu: slug, title, template, status
- SEO onerilen: seo_title, seo_description

2. listings:
- Zorunlu: slug, name, status
- SEO onerilen: seo_title, seo_description

3. city-pages:
- Zorunlu: slug, city, title, status
- SEO onerilen: seo_title, seo_description

Not:
- published_at bos birakilabilir ancak canli gecis oncesi yayin tarihinin doldurulmasi onerilir.
- canonical_url sadece kesin canonical karari varsa doldurulmalidir.
