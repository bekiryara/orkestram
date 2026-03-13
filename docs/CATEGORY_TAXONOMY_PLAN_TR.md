# Kategori ve Taksonomi Omurga Plani (TR)

Tarih: 2026-03-09

Amac:
1. Muzik gruplari / organizasyon / mekanlar yapisini tek omurgada toplamak.
2. Konum ve servis alani kavramlarini ayri ve temiz modellemek.
3. Admin uzerinden kod degistirmeden kategori yonetimi saglamak.

## Kapsam (MVP)

1. Vertical:
   - `muzik-gruplari`
   - `organizasyon`
   - `mekanlar`
2. Kategori (vertical'a bagli)
3. Alt kategori (kategoriye bagli, opsiyonel)
4. Konum hiyerarsisi:
   - il > ilce
5. Servis alani (ilanin hizmet verdigi bolgeler, coklu secim)
6. Etiket/ozellik (opsiyonel filtre ve rozet)

## Veri Modeli (MVP)

1. `verticals`
   - `name`, `slug`, `description`, `is_active`, `sort_order`
2. `categories`
   - `vertical_id`, `name`, `slug`
   - `short_description`, `description`
   - `cover_image`
   - `seo_title`, `seo_description`
   - `is_active`, `sort_order`
3. `subcategories`
   - `category_id`, `name`, `slug`, `description`, `cover_image`
   - `is_active`, `sort_order`
4. `locations`
   - `parent_id` (null = il), `name`, `slug`, `type` (`city|district`)
   - `is_active`, `sort_order`
5. `service_areas`
   - `listing_id`, `location_id`
6. `tags`
   - `name`, `slug`, `description`, `is_active`
7. Iliski tablolari:
   - `listing_categories`
   - `listing_subcategories` (opsiyonel)
   - `listing_tags`

## Ilan Kurallari

1. Zorunlu:
   - 1 vertical
   - 1 kategori
   - 1 primary konum (ilanin bulundugu il/ilce)
2. Opsiyonel:
   - alt kategori
   - servis alanlari (coklu)
   - etiketler
3. Kategori silme:
   - ilana bagliysa hard delete yok, pasife cek.

## Admin Kategori Yonetimi

1. Vertical CRUD
2. Kategori CRUD
   - aciklama + kapak gorsel + SEO alanlari
3. Alt kategori CRUD
4. Etiket CRUD
5. Siralama ve aktif/pasif yonetimi
6. Slug cakisma kontrolu

## URL ve SEO Kurallari (MVP)

1. Detay:
   - `/ilan/{slug}`
2. Liste:
   - `/ilanlar`
   - filtre parametreleri: `vertical`, `category`, `city`, `district`
3. Kategori landing (faz 2):
   - `/hizmet/{kategori-slug}`
   - `/hizmet/{kategori-slug}/{sehir}`
4. Bos landing sayfalari:
   - `noindex, follow`

## Fazli Uygulama

## Faz A - Omurga ve DB
1. Migration + model iliskileri.
2. Seed ile 3 vertical + temel kategoriler.
3. Enum/if-else yerine data-driven yapi.

Kabul:
1. Migration temiz calisir.
2. Iliskiler testten gecer.

## Faz B - Admin Panel
1. Vertical/kategori/alt kategori/etiket yonetimi.
2. Kategori aciklamasi ve gorsel yukleme.
3. Ilan formuna taxonomy secimi.

Kabul:
1. Admin kategori ekle/duzenle/pasife cek akisi sorunsuz.
2. Ilana taxonomy baglama calisir.

## Faz C - Frontend ve Filtre
1. Liste filtreleri taxonomy ile calisir.
2. Kartta kategori + konum + servis alani rozetleri gorunur.
3. Kategori landing ilk versiyon.

Kabul:
1. Filtre sonuc dogrulugu test edilir.
2. Mobil/desktop gorunum stabil.

## Faz D - Final SEO ve Operasyon (En Son)
1. URL mapping finali.
2. Landing icerik doldurma.
3. 301 kurallari final kontrol.

Kabul:
1. Canli oncesi QA raporu PASS.
2. Deploy kilidi kurallari korunur.
