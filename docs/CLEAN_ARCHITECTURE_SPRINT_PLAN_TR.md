# Clean Architecture Sprint Plani (TR)

Tarih: 2026-03-09

Amac:
1. Controller sisligini azaltmak.
2. If/else karmasini servis katmanina toplamak.
3. Tekrar eden kural/parsing kodunu merkezilestirmek.

## Sprint 1 - Listing Domain Refactor

Durum: Tamam (2026-03-09)

Hedef:
1. `ListingController` sadece orchestration yapsin.
2. Validasyon FormRequest katmanina tasinsin.
3. Medya ve input normalization servislerde toplansin.

Yapilacaklar:
1. FormRequest
   - `StoreListingRequest`
   - `UpdateListingRequest`
2. Service
   - `ListingPayloadBuilder` (slug/phone/features normalization)
   - `ListingMediaService` (cover + galeri islemleri)
3. Controller sadeleme
   - `validated()` ve upload helperlari kaldirilacak.
   - Service + Request dependency ile calisacak.

Kabul kriteri:
1. Admin create/update akisi bozulmadan calisir.
2. Galeri siralama/silme davranisi korunur.
3. Kod satir karmasinda gozle gorulur azalma olur.
4. Feature test PASS:
   - `AdminListingMediaFlowTest`

## Sprint 2 - Listing UI Polish

Durum: Tamam (2026-03-09)

Hedef:
1. Detay sayfa hero hiyerarsisini finallemek.
2. Galeri etkilesimini lightbox ile genisletmek.
3. CTA konumlandirmasini mobil/desktop optimize etmek.

Gerceklesen:
1. Detay hero kompozisyonu + CTA panel hiyerarsisi tamamlandi.
2. Lightbox eklendi, klavye (`Esc`, `ArrowLeft`, `ArrowRight`) destekli.
3. Ozellikler alani kartli yapiya alindi.

## Sprint 3 - Izmir Theme Split

Durum: Tamam (2026-03-09)

Hedef:
1. Site bazli tema tokenlarini configten yonetmek.
2. Blade tarafinda daginik kosullari azaltmak.

Ilk adim:
1. `config/site_themes.php` eklendi.
2. `PublicController::siteMeta()` config tabanli hale getirildi.
3. Izmir route parity tamamlandi:
   - `/`, `/ilanlar`, `/ilan/{slug}` rotalari 200.
4. Site bazli metin tonu config tokenlariyla ayrildi:
   - `listing_label`, `listing_lead`, `listing_cta`
   - `contact_heading`, `contact_lead`
5. Tema token ayrimi derinlestirildi:
   - tipografi, hero grad, kart/pill/btn tokenlari.

## Sprint 4 - QA Kapisi ve Release Sertlestirme

Durum: Kismi Tamam (2026-03-09)

Hedef:
1. Feature test:
   - listing create/update + media
   - galeri order/silme
2. Smoke teste admin medya senaryosu eklemek.
3. Release hattinda PASS kapisini netlestirmek.

Gerceklesen:
1. `scripts/smoke-test.ps1` deterministic hale getirildi:
   - public: `/`, `/ilanlar`, `robots.txt`, `sitemap.xml`, `404`
   - listing detay: canli 200 bir `/ilan/{slug}` kontrolu
   - admin: authsiz `401`, auth ile `200` (`/admin/pages`, `/admin/listings`, `/admin/city-pages`)
2. `both` app smokeda PASS (8180 + 8181).

Acik Kalan:
1. Release paket adiminda smoke PASS bagimliliginin raporlanmasi.

## Sprint 5 - Icerik ve URL Final (En Son)

Durum: Acik

Hedef:
1. Gercek 5-6 ilanin final icerigini girmek.
2. Son asama sayfa doldurma ve URL esleme finalini yapmak.

## Kapanis Kurali

1. Her sprint sonunda:
   - `PROJECT_STATUS_TR.md` guncelle
   - ilgili plan dosyasina durum yaz
2. Kullanici onayi olmadan deploy adimi acilmaz.
