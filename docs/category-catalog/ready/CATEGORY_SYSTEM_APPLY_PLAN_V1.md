# Category System Apply Plan V1

Tarih: 2026-03-10
Durum: Uygulandi (local, iki app)

## Kaynak Planlarla Hizalama
- docs/CATEGORY_TAXONOMY_PLAN_TR.md
- docs/category-catalog/ready/FINAL_CATEGORY_ACTIVE_READY_V2.txt

## Kilit Karar
- Tum guclu kategoriler ACTIVE settedir.
- Active kaynak CSV: `categories_active_ready_v2.csv` (97 satir)
- Passive havuz V2: bos

## Uygulama Adimlari
1. `main_categories` seed:
   - muzik-ekipleri
   - etkinlik-organizasyon
   - sahne-dekor-ekipman
   - fotograf-ve-video
   - etkinlik-mekanlari
   - ikram-ve-catering
   - gelin-arabasi-ve-transfer
   - guzellik-ve-hazirlik
   - davetiye-hediyelik-cicek
2. `categories` import:
   - Dosya: `categories_active_ready_v2.csv`
   - Kural: `category_slug` UNIQUE
   - Kural: `status = active`
3. Slug canonical kontrol:
   - Import oncesi ve sonrasi duplicate = 0
4. Admin panel kontrol:
   - Tum aktif kategoriler listede
   - Ana kategori baglantilari dogru
5. Frontend kontrol:
   - Kategori listeleme
   - Filtreleme
   - Landing route baglantisi

## Coklu Site Gorunurluk Kurali (Iki App)
1. Ilanlar `site_scope` ile yonetilir:
   - `orkestram`
   - `izmirorkestra`
   - `both`
2. Bir kategori bir sitede sadece o siteye ait yayinlanmis ilan varsa gorunur.
3. Kategori listesi/sayaci sorgulari:
   - `site_scope IN (current_site, 'both')`
   - `status = published`
4. Kategoride ilan yoksa:
   - menu/listede gizlenir
   - kategori URL'si indexlenmez (`noindex, follow`)
5. Bu kural iki appte de ayni sekilde calisir (tema farkli, davranis ayni).

## Deterministik Kabul Kriteri
- Active satir sayisi: 97
- Duplicate slug: 0
- Bos slug: 0
- Active/Passive overlap: 0 (V2 ile saglandi)

## Not
- V1 backlog dosyalari `archive/` klasorune alinmistir.
- Bundan sonra yeni kategori ekleme sadece yeni versiyonla yapilir (v2, v3...).
- DB dogrulama:
  - `main_categories`: 9
  - `categories`: 97


