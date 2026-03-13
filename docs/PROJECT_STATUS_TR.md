# Proje Durumu (TR)

Tarih: 2026-03-11

## Canonical Mesaj Dokumani
1. Aktif mesaj mimarisi ve davranisi icin tek kaynak: `MESAJ_V1_TR.md`.
2. Aktif feedback (yorum + begeni) mimarisi icin tek kaynak: `FEEDBACK_V1_TR.md`.
3. Legacy engagement dokumanlari: `docs/archive/legacy-engagement/` altina tasindi (DEPRECATED).
4. Bu dosyadaki eski `etkilesim/engagement` maddeleri sadece tarihsel kayittir; aktif karar ve uygulama referansi degildir.
5. 2026-03-12 terminoloji migrasyonu:
   - aktif route tabani: `feedback` (legacy `engagements` pathlari kaldirildi)
   - aktif controller isimleri: `FeedbackController`
   - aktif test isimleri:
     - `ListingFeedbackFlowTest` (eski: `ListingEngagementFlowTest`)
     - `FeedbackModerationAccessTest` (eski: `EngagementModerationAccessTest`)
6. 2026-03-13 Feedback V1 test kilidi tamamlandi:
   - Docker feature test PASS (iki app):
     - `ListingFeedbackFlowTest` (6 test, 44 assertion)
     - `FeedbackModerationAccessTest` (2 test, 33 assertion)
   - Smoke PASS (`scripts/smoke-test.ps1 -App both`):
     - listing detayda `Yorumlar` icerik kontrolu
     - admin panelde `Feedbackler` icerik kontrolu
     - city-options duplicate kontrolu PASS

## Master Plan Durumu
1. Faz 0 (Plan/Hazirlik): Tamam
2. Faz 1 (Local Altyapi): Tamam
3. Faz 2 (Veri Modeli + Admin): Tamam
4. Faz 3 (Tema/Sablonlar): Kismi
5. Faz 4 (Teknik SEO): Kismi
6. Faz 5 (Icerik Tasima): Devam ediyor
7. Faz 6 (Redirect Final): Baslamadi (plan geregi en son)
8. Faz 7 (Canliya Alma/Stabilizasyon): Baslamadi

## Paralel Teknik Plan Durumu
1. Faz 1 (Guvenlik Sertlestirme): Tamam
2. Faz 2 (Test Kapsami): Kismi
3. Faz 3 (Release Sertlestirme): Acik
4. Faz 4 (Performans Baseline): Acik

## Son Tamamlananlar
0. Kategori ozellik sistemi Faz 1 omurgasi eklendi (iki app parity):
   - Yeni sozlesme dokumani: `docs/CATEGORY_ATTRIBUTE_CONTRACT_TR.md`
   - Yeni tablolar:
     - `category_attributes`
     - `listing_attribute_values`
   - Model iliskileri:
     - `Category::attributes()`
     - `Listing::attributeValues()`
   - Not: Bu fazda sadece schema/domain omurgasi var; admin UI ve public dinamik filtre Faz 2-3 kapsaminda acilacak.
0.1 Kategori ozellik sistemi Faz 2 admin CRUD ilk surumu eklendi (iki app parity):
   - Yeni admin route seti: `admin.category-attributes.*` (nested: `categories/{category}/attributes`)
   - Yeni admin controller: `CategoryAttributeController`
   - Yeni admin ekranlari:
     - `admin/category-attributes/index.blade.php`
     - `admin/category-attributes/form.blade.php`
   - Kategori listesine `Ozellikler` aksiyonu eklendi.
   - Not: Faz 2'nin bu adiminda sadece panel CRUD var; public dinamik filtre Faz 3 kapsaminda.
0.2 Kategori ozellik sistemi Faz 3 ilk entegrasyon eklendi (iki app parity):
   - Public `/ilanlar` ekraninda secili kategoriye bagli `is_filterable` ozellikler dinamik filtre alanlari olarak render edilir.
   - Dinamik filtre query formati: `attr_{key}`
   - Sorgu davranisi:
     - `listing_attribute_values.normalized_value` uzerinden birebir eslesme
     - kategori secimi yoksa dinamik alan render edilmez
   - Test kilidi:
     - `CategorySystemFlowTest::test_listing_filters_can_use_category_attribute_values`
1. Import template ve content QA dokumanlari guncellendi.
2. `url_mapping.csv` kalite etiketleri eklendi.
3. Admin Basic Auth + rate limit uygulandi.
4. Public/SEO ve admin erisim feature testleri eklendi.
5. Test DB izolasyonu sqlite `:memory:` olarak ayarlandi.
6. WSL tabanli local calisma duzeni aktif edildi (performans artisi).
7. Listing medya alani eklendi:
   - kapak gorsel
   - galeri
   - whatsapp/telefon
   - ozellik listesi
8. Detay sayfada galeri thumbnail tiklama ile ana gorsel degistirme eklendi.
9. Repo disiplini ve teknik borc kapisi dokumani eklendi (`REPO_DISCIPLINE_TR.md`).
10. Admin galeri UX gelistirildi:
   - tekil gorsel silme
   - surukle-birak siralama
11. Listing mimari refactor (Sprint 1) baslatildi:
   - FormRequest katmani (`StoreListingRequest`, `UpdateListingRequest`)
   - Service katmani (`ListingPayloadBuilder`, `ListingMediaService`)
   - `ListingController` orchestration yapisina sadeletildi.
12. Feature test kapisi guncellendi:
   - `AdminListingMediaFlowTest` eklendi (create/update medya akislari)
   - Tum Feature testleri PASS (6 test, 26 assertion).
13. Sprint 2 ilk adim uygulandi:
   - listing detay hero kompozisyonu iyilestirildi
   - CTA panel hiyerarsisi duzenlendi
   - galeri lightbox eklendi
   - detay endpoint smoke kontrolu PASS (`/ilan/grup-moda` 200)
14. Sprint 2 ikinci adim uygulandi:
   - lightbox klavye kontrolu eklendi (`Esc`, `ArrowLeft`, `ArrowRight`)
   - lightbox onceki/sonraki butonlari eklendi
   - detay endpoint smoke kontrolu tekrar PASS (`/ilan/grup-moda` 200)
15. Sprint 2 final polish tamamlandi:
   - detay icerik alani blok yapisina alindi
   - ozet alani vurgulu karta cevrildi
   - ozellikler listesi kartli yapiya cevrildi
16. Sprint 3 baslatildi:
   - site bazli tema ayarlari `config/site_themes.php` dosyasina tasindi
   - `PublicController` config tabanli site meta okumaya gecti
   - smoke kontrolu PASS (`8180` ve `8181` ana sayfa 200)
17. Sprint 3 tema token ayrimi derinlestirildi:
   - tipografi, hero grad, kart/pill/btn tokenlari site bazli ayrildi
   - `site-izmir` icin ayri gorsel dil aktif
   - izmir route parity tamamlandi (`/`, `/ilanlar`, `/ilan/izmir-bandosu` 200)
18. Sprint 3 tamamlandi:
   - site bazli metin tonu tokenlari eklendi (`listing_*`, `contact_*`)
   - iki domainde route + detay smoke kontrolu PASS.
19. Strateji guncellendi:
   - Gercek ilan cekirdegi (5-6 ilan) once bitirilecek
   - Sehir/listing sayfa doldurma ve genis icerik operasyonu en sona alinacak.
20. QA kapisi sertlestirildi:
   - `scripts/smoke-test.ps1` admin auth + public route + listing detay kontrolleriyle guncellendi
   - iki app smokeda PASS (8180/8181)
21. Redirect guvenligi sertlestirildi:
   - `ApplyRedirectRules` icin hedef validasyonu eklendi (self-loop/invalid scheme korumasi)
   - invalid `http_code` durumunda 301 fallback
22. Redirect davranisi testle kilitlendi:
   - `RedirectRulesTest` eklendi (5 senaryo)
   - unknown path redirect akisinin calismasi icin `Route::fallback(...)` eklendi
   - Feature test PASS (11 test, 35 assertion)
23. Kategori/taksonomi omurga plani dokumante edildi:
   - `CATEGORY_TAXONOMY_PLAN_TR.md` (vertical + kategori + alt kategori + konum + servis alani)
   - Faz A/B/C/D teslim yapisi netlestirildi.
24. Deterministik rol/izin (RBAC) katmani eklendi:
   - `admin.basic` middleware coklu hesap + rol destekleyecek sekilde genisletildi (`ADMIN_ACCOUNTS_JSON`).
   - Yeni `ability` middleware eklendi (config tabanli yetki kontrolu, if/else birikimi yok).
   - Route bazli izinler aktif edildi:
     - `pages.manage`
     - `listings.manage`
     - `city_pages.manage`
   - Rol matrisi `config/admin_acl.php` ile merkezilestirildi.
   - Feature test eklendi: `RoleAbilityAccessTest` (editor/viewer yetki senaryolari).
   - Iki app parity dogrulandi: `AdminAccessTest` + `RoleAbilityAccessTest` PASS.
24. Genel sistem uyum/risk degerlendirmesi yapildi:
   - `SYSTEM_ALIGNMENT_REVIEW_TR.md` eklendi
   - kritik bulgu: `izmirorkestra` feature test parity acigi (`redirects` tablosu kaynakli FAIL)
25. P0 parity acigi kapatildi:
   - `izmirorkestra` tarafina eksik feature testler eklendi (`AdminListingMediaFlowTest`, `RedirectRulesTest`)
   - `ExampleTest` `RefreshDatabase` ile hizalandi
   - iki app feature suite PASS: 11 test, 35 assertion
26. Runtime 500 olayi cozuldÃ¼:
   - neden: `storage/framework/views` yazma izin hatasi
   - cozum: iki appte `storage` + `bootstrap/cache` sahiplik/izin duzeltildi, cache temizlendi
   - smoke tekrar PASS (8180/8181)
27. WSL performans kilidi kalici hale getirildi:
   - `scripts/dev-up.ps1` WSL-first compose calismasina alindi
   - windows->WSL kod senkron adimi eklendi (rsync)
   - mount source guard eklendi (yanlis `D:\\` mount durumunda FAIL)
   - dogrulama: container mount kaynaklari `/home/bekir/...`
28. WSL migration scripti standardize edildi:
   - `scripts/wsl-migrate-project.ps1` default user/path netlestirildi
   - ayni rsync exclusion kurallariyla hizalandi
29. Kategori sistemi Faz A uygulandi (iki app):
   - `main_categories` + `categories` tablolari eklendi
   - `listings` tablosuna `site_scope` ve `category_id` eklendi
   - 97 aktif kategori + 9 ana kategori deterministic seed ile DB'ye yazildi
   - admin kategori CRUD ekranlari eklendi (`/admin/categories`)
   - ilan formuna kategori secimi + iki site kapsam secimi eklendi
   - public listing sorgulari `site_scope=both` gorunurlugunu destekler hale getirildi
30. Kategori yonetim UI polish yapildi:
   - admin sayfalamada buyuk ok/sv g sorunu giderildi (ozel compact pagination)
   - kategori formuna kapak gorsel yukleme + mevcut gorsel onizleme + kaldirma secenegi eklendi
   - kategori liste ekraninda kapak thumbnail gorunumu eklendi
31. Kategori landing fazi baglandi:
   - yeni route: `/hizmet/{slug}`
   - kategoriye bagli ilan listeleme aktif
   - ilansiz veya index kapali kategoride `noindex, follow` robots metasi uygulanir
29. Rol sistemi icin repo disipliniyle uyumlu deterministik plan eklendi:
   - `ROLE_SYSTEM_DETERMINISTIC_PLAN_TR.md`
   - admin + musteri/ilan sahibi rolleri icin fazli uygulama ve kabul kriterleri netlestirildi
30. Rol sistemi Faz A/C ilk uygulamasi tamamlandi (iki app parity):
   - `config/admin_acl.php` rol matrisi genisletildi:
     - `listing_owner`, `customer`, `support_agent`
   - Yeni panel route gruplari eklendi:
     - `/owner/*`
     - `/customer/*`
     - `/support/*`
   - Ability tabanli erisim kilidi aktif:
     - `owner.*`, `customer.*`, `support.*`
   - Feature test eklendi:
     - `CustomerOwnerRoleAccessTest`
   - Dogrulama:
     - iki appte `AdminAccessTest + RoleAbilityAccessTest + CustomerOwnerRoleAccessTest` PASS
     - 7 test, 21 assertion
31. Rol sistemi Faz B (DB role modeli) tamamlandi:
   - Yeni model/tablo omurgasi:
     - `roles`
     - `role_user`
     - `users.username`, `users.is_active`
   - Middleware auth akisi resolver servisine tasindi:
     - `AdminIdentityResolver` (json account + db auth + legacy fallback)
   - `ADMIN_DB_AUTH_ENABLED=true` ile DB tabanli basic auth aktiflenebilir hale getirildi.
   - Role seeding eklendi:
     - `RoleSeeder`
   - Migrationlar idempotent hale getirildi (mevcut kolon/tablo varsa fail etmez).
   - DB role auth testi eklendi:
     - `AdminDbRoleAuthTest`
   - Dogrulama:
     - iki appte role test paketi PASS
     - 8 test, 24 assertion
32. Rol sistemi Faz C/D kritik akis hatasi kapatildi (iki app parity):
   - Kapanan bug:
     - `CustomerOwnerRoleAccessTest` icinde `customer_requests.user_id` FK ihlali (JSON account akisinda `admin_user_id=0` yaziliyordu).
   - Uygulanan cozum:
     - `CustomerDashboardController::store()` user id pozitif degilse `null` kaydediyor.
     - `CustomerDashboardController::requests()` DB user baglantisi olmayan customer icin bos liste davranisina gecirildi.
   - Runtime notu:
     - Calisan local containerlarin mount kaynagi WSL (`/home/bekir/orkestram/local-rebuild/...`) oldugu icin duzeltme runtime kaynaginda da uygulanip dogrulandi.
   - Dogrulama:
     - iki appte hedef test paketi PASS:
       - `AdminAccessTest`
       - `RoleAbilityAccessTest`
       - `CustomerOwnerRoleAccessTest`
       - `AdminDbRoleAuthTest`
      - `OwnerCustomerDbFlowTest`
      - Sonuc: 10 test, 30 assertion, iki appte de PASS
33. Owner panel ilan olusturma akisi eklendi (iki app parity):
   - Yeni owner endpointleri:
     - `GET /owner/listings/create`
     - `POST /owner/listings`
   - Davranis:
     - ilan `draft` olarak olusur
     - `owner_user_id` giris yapan DB owner kullanicisina baglanir
     - site bazli unique slug deterministic uretilir
   - Portal:
     - owner ilan listesi ekranina `Yeni Ilan` aksiyonu eklendi
   - Test:
     - `OwnerCustomerDbFlowTest::test_owner_can_create_listing_from_owner_panel`
   - Dogrulama:
     - hedef role paketinde iki appte PASS
     - Sonuc: 11 test, 33 assertion
34. Rol sistemi Faz 1 (Login/Hesabim) kapsam plani netlestirildi:
   - Session auth + role redirect + hesap ekrani akisi `ROLE_SYSTEM_DETERMINISTIC_PLAN_TR.md` icine Faz F olarak eklendi.
   - Uygulama sirasinda basic auth fallback korunacak sekilde ilerleme karari alindi.
35. Rol sistemi Faz 1 (Login/Hesabim) tamamlandi (iki app parity):
   - Yeni endpointler:
     - `GET /giris`
     - `POST /giris`
     - `POST /cikis`
     - `GET /hesabim`
   - Davranis:
     - Session tabanli login aktif
     - Rol bazli deterministic redirect aktif:
       - `listing_owner -> /owner`
       - `customer -> /customer`
       - `support_agent -> /support`
       - admin/editor/viewer -> `/admin/pages`
     - `admin.basic` middleware session kimligi -> basic auth fallback sirasiyla calisiyor.
   - Test:
     - `PortalSessionAuthTest` eklendi
   - Dogrulama:
     - iki appte genis rol paketi PASS
     - Sonuc: 15 test, 47 assertion
36. Rol sistemi merkezi mimari v2 plani ve mobil uyum standardi eklendi:
   - `ROLE_SYSTEM_MERKEZI_MIMARI_V2_TR.md` olusturuldu.
   - login/logout/hesabim + portal nav/table/form ekranlari ortak responsive stil katmanina alindi.
   - tekrar etmeyen UI token yapisi iki appte parity ile uygulandi.
37. Login/logout/panel ekranlari mobil uyum sertlestirildi (iki app parity):
   - ortak stil parcasi eklendi:
     - `resources/views/portal/partials/base-styles.blade.php`
   - login (`/giris`) ve hesabim (`/hesabim`) ekranlari ortak tokenlarla yeniden duzenlendi.
   - owner/customer/support tablo ekranlari `table-wrap` ile mobilde kirilmasiz hale getirildi.
   - Dogrulama:
     - iki appte rol + session auth paketi PASS
     - Sonuc: 15 test, 47 assertion
38. Header/footer + giris/cikis navigasyonu deterministik merkez yapisina alindi:
   - Role-home mapping config'e tasindi (`role_home.php`).
   - Header menu kurallari config'e tasindi (`navigation.php`).
   - View composer + navigation factory ile public/portal menuleri tek kaynaktan uretilir hale getirildi.
   - Controller icindeki role-home hardcode yapisi kaldirildi.
   - Iki app parity + test paketi PASS (15 test, 47 assertion).
39. Faz 2 uygulama plani dokumante edildi:
   - `ROLE_SYSTEM_PHASE2_EXECUTION_PLAN_TR.md` eklendi.
   - teklif formu basit kalacak sekilde account + owner/support aksiyon kapsami netlestirildi.
40. Rol sistemi Faz 2 account/owner/support aksiyonlari tamamlandi (iki app parity):
   - Hesabim:
     - profil guncelleme (`POST /hesabim/profil`)
     - sifre degistirme (`POST /hesabim/sifre`)
   - Owner:
     - ilan duzenleme + status guncelleme
     - lead status/not guncelleme
   - Support:
     - request status/not guncelleme + filtre
   - Kod tekrarini azaltan ortak servisler:
     - `PortalContext`
     - `OwnerResourceAccess`
   - DB:
     - `customer_requests.internal_note` migration + model fillable
   - Test:
     - `AccountManagementTest`
     - `OwnerPanelActionsTest`
     - `SupportRequestManageTest`
   - Dogrulama:
     - iki appte artisan boot kontrolu + migrate + yeni feature testler PASS
     - endpoint smoke: `8180` ve `8181` ana sayfa `200`
41. Deterministik etkilesim cekirdegi eklendi (iki app parity):
   - Yeni domain tablolari: `listing_likes`, `listing_feedback`
   - Musteri endpointleri:
     - `POST /customer/engagements/like`
     - `POST /customer/engagements`
     - `GET /customer/engagements`
   - Owner endpointleri:
     - `GET /owner/engagements`
     - `POST /owner/engagements/{feedback}/status`
   - Ability tokenlari:
     - `customer.engagement.create|view`
     - `owner.engagement.view|manage`
   - Test:
     - `ListingEngagementFlowTest` eklendi (like idempotency + message/reply akisi)
42. Etkilesim UI bootstrap disipliniyle eklendi (iki app parity):
   - Yeni portal sayfalari:
     - `/customer/engagements`
     - `/owner/engagements`
   - `Hesabim` ekranina `Etkilesimler` sekmesi eklendi.
   - Owner dashboard metriklerine etkilesim sayaci + hizli aksiyon baglantilari eklendi.
   - Customer dashboard form alanlari bootstrap siniflariyla hizalandi.
43. WSL mount drift riski kapatildi ve dogrulama tekrarlandi:
   - Windows -> WSL rsync senkronu yeniden calistirildi.
   - Container icinde test dosya icerigi dogrudan dogrulandi.
   - `ListingEngagementFlowTest` iki appte de 4 test / 34 assertion PASS.
   - Regresyon paketi iki appte de PASS:
     - `CustomerOwnerRoleAccessTest`
     - `ListingEngagementFlowTest`
     - `OwnerCustomerDbFlowTest`
     - `PortalSessionAuthTest`
     - `SupportRequestManageTest`
     - Toplam: 18 test / 95 assertion.
44. Uctan uca rol yolculugu (E2E) testle kilitlendi:
   - Yeni test: `EndToEndRoleJourneyTest`
   - Akis:
     - customer login -> talep + etkilesim olusturma
     - owner login -> etkilesim yanitlama + lead guncelleme
     - support login -> request kapatma
     - admin login -> admin ekran erisimi
   - Duzeltme:
     - admin login redirect beklentisi `/admin/pages` olarak kilitlendi.
   - Dogrulama:
     - iki appte regression paketi PASS
     - Toplam: 19 test / 126 assertion.
45. Listing detay -> etkilesim giris akisi bootstrap disipliniyle tamamlandi:
   - Listing detay CTA paneline etkileÅŸim aksiyonlari eklendi:
     - begeni
     - yorum
     - soru
     - mesaj
   - Guest akisinda login `next` parametresi ile hedef sayfaya donus aktif.
   - `customer/engagements` sayfasinda secili ilan/kind prefill destegi eklendi.
   - Auth ekranlari (`/giris`, `/kayit`) `next` parametresini korur hale getirildi.
   - Iki app parity + dogrulama:
     - `PortalSessionAuthTest`
     - `ListingEngagementFlowTest`
     - `EndToEndRoleJourneyTest`
     - Toplam: 12 test / 95 assertion, iki appte PASS.
46. Etkilesim UX acik kalemleri backlog'a alindi:
   - `NEXT_STEPS_TR.md` icinde etkilesim UX polish ve runtime izin sertlestirme maddeleri eklendi.
   - `UI_ROLLOUT_PLAN_V1_TR.md` icinde etkilesim girisleri ve kabul kriterleri genisletildi.
   - Sonraki hedef: owner/customer etkilesim ekranlarinda aksiyon yogunlugunu sadeleÅŸtirme + admin/support merkezi moderasyon fazi.
47. Etkilesim UX 5-adim uygulama fazi tamamlandi (iki app parity):
   - Plan dokumani:
     - `docs/archive/legacy-engagement/ENGAGEMENT_UX_EXECUTION_PLAN_TR.md` (DEPRECATED)
   - Tamamlananlar:
     - listing detay CTA hiyerarsi sadeletme (primary: mesaj, secondary: yorum/soru/begeni)
     - customer engagements secim odakli form (slug yazma zorunlulugu kaldirildi)
     - owner engagements satir-ici hizli guncelleme
     - alert/empty-state/status-badge standardizasyonu (bootstrap uyumlu)
     - support + admin merkezi engagements moderasyon ekranlari
   - Yeni endpointler:
     - `GET /support/engagements`
     - `POST /support/engagements/{feedback}/status`
     - `GET /admin/engagements`
     - `POST /admin/engagements/{feedback}/status`
   - Yeni test:
     - `EngagementModerationAccessTest`
   - Dogrulama:
     - `PortalSessionAuthTest`
     - `ListingEngagementFlowTest`
     - `EndToEndRoleJourneyTest`
     - `SupportRequestManageTest`
     - `EngagementModerationAccessTest`
     - iki appte PASS.
48. Owner hesap dongusu kapatildi, hibrit akis modeline gecis icin ara faz tamamlandi (iki app parity):
   - Not:
     - Bu faz eski karar setidir; aktif referans plani `docs/archive/legacy-engagement/HESABIM_OWNER_AKIS_PLAN_TR.md` (DEPRECATED) dosyasidir.
   - Uygulananlar:
     - Header `Hesabim` linki owner rolde `/owner` hedefine alindi (role-aware nav).
     - `/panel` owner icin `/owner` yonlendirmesine cekildi.
     - `Hesabim` owner rolde sadeledi: sadece `Profil` + `Guvenlik`.
     - `/owner/engagements` sayfasi ara fazda etkilesim odakli tek ekran modeliyle calisti:
       - ustte `Ilanlarima Gelenler` (hizli durum/yanit)
       - altta `Benim Gonderdiklerim` (owner'in musteri gibi actigi kayitlar)
       - gelen kayitlarda satir bazli Bootstrap modal detay.
   - Dogrulama:
     - `PortalSessionAuthTest` PASS (iki app)
     - `ListingEngagementFlowTest` PASS (iki app)
49. Hesabim + Owner hibrit akisina gecis (deterministik, iki app parity):
   - Plan dokumani:
     - `docs/archive/legacy-engagement/HESABIM_OWNER_AKIS_PLAN_TR.md` (DEPRECATED)
   - Uygulananlar:
     - role-home hedefleri owner/customer/support icin `/hesabim` olarak birlestirildi.
     - Header `Hesabim` linkinde owner'a ozel `/owner` override kaldirildi.
     - `Hesabim` ekraninda owner kisiti kaldirildi; owner icin de `Genel Bakis / Taleplerim / Etkilesimler / Profil / Guvenlik` sekmeleri acildi.
     - `Hesabim > Genel Bakis` alanina owner icin birincil aksiyon eklendi: `Ilan Yonetimine Gec` (`btn-primary`, hedef `/owner`).
     - Rol label standardi uygulandi: UI'da owner rolu `Ilan Sahibi` olarak gosterilmeye baslandi.
   - WSL/Calisma notu:
     - Windows->WSL senkronu `scripts/dev-up.ps1 -App both` ile alindi.
     - Senkron sonrasi kritik regression paketi ve smoke tekrar kosuldu.
   - Dogrulama:
     - `PortalSessionAuthTest` PASS (iki app)
     - `ListingEngagementFlowTest` PASS (iki app)
     - `EndToEndRoleJourneyTest` PASS (iki app)
     - `scripts/smoke-test.ps1` PASS (`orkestram` + `izmirorkestra`)
50. Owner panel label + Bootstrap gorunum hizalama turu tamamlandi (iki app parity):
   - Uygulananlar:
     - `Owner Dashboard`/`Owner Leadler` gibi etiketler Turkce standarda cekildi (`Ilan Yonetimi`, `Gelen Talepler`, `Ilanlarim`).
     - owner dashboard/listings/leads/engagements gorunumleri `card shadow-sm`, `table-responsive`, `table table-striped` ve buton varyantlariyla bootstrap disiplinine yaklastirildi.
     - owner engagements ust donus aksiyonu final dil modeline gore guncellendi.
   - Dogrulama:
     - `OwnerPanelActionsTest` PASS (iki app)
     - `PortalSessionAuthTest` PASS (iki app)
     - `ListingEngagementFlowTest` PASS (iki app)
     - `EndToEndRoleJourneyTest` PASS (iki app)
     - `scripts/smoke-test.ps1` PASS (iki app)
51. UI label standardi merkezi sozluge baglandi:
   - Yeni dokuman:
      - `UI_LABEL_SOZLUGU_TR.md`
   - Kapsam:
      - rol label standardi (`listing_owner` => `Ilan Veren`)
      - `hesabim` / `owner` / `customer` gorunur metin standardi
      - yasakli varyant listesi (`Owner Dashboard`, `Owner Leadler`, vb.)
   - Uygulanan ek duzeltmeler:
      - owner ilan form basliklari `Yeni Ilan` ve `Ilan Duzenle` olarak tekillestirildi.
52. Hesabim + Owner ana planina birebir hizalama turu uygulandi (iki app parity):
   - Aktif plan:
     - `docs/archive/legacy-engagement/HESABIM_OWNER_AKIS_PLAN_TR.md` (DEPRECATED)
   - Uygulananlar:
      - `/hesabim` sekmeleri plana gore genisletildi:
        - `Genel Bakis`, `Taleplerim`, `Mesajlarim`, `Yorumlarim`, `Profilim`, `Guvenlik`
      - owner gecis aksiyonu korundu:
        - `Ilan Yonetimine Gec` (`btn-primary`) -> `/owner`
      - `/owner` paneli ayni UI iskeletine tasindi (hesabim ile hizali):
        - ortak sol menu omurgasi + kart/tablo/buton hiyerarsisi
        - menu: `Ilanlarim`, `Isler / Talepler`, `Ilan Mesajlari`, `Yorumlar`, `Sahiplik ve Yetki Ayarlari`
     - yeni owner endpoint/sayfa:
       - `GET /owner/settings`
       - `portal/owner/settings.blade.php`
     - eski label beklentisi testte yeni plana cekildi:
       - `ListingEngagementFlowTest` icinde `Etkilesim Merkezi` -> `Ilan Mesajlari`
   - Dogrulama:
     - `PortalSessionAuthTest` PASS (iki app)
     - `OwnerPanelActionsTest` PASS (iki app)
     - `ListingEngagementFlowTest` PASS (iki app)
     - `EndToEndRoleJourneyTest` PASS (iki app)
    - Runtime notu:
      - `/owner/listings` 500 kok nedeni `storage/framework/views` yazma izniydi.
      - cozum: container icinde `storage` + `bootstrap/cache` izin duzeltmesi + `view:clear` + `cache:clear`.
53. Portal labellari merkezi `lang` katmanina baglandi (iki app parity):
   - Yeni dosyalar:
     - `local-rebuild/apps/orkestram/lang/en/portal.php`
     - `local-rebuild/apps/orkestram/lang/tr/portal.php`
     - `local-rebuild/apps/izmirorkestra/lang/en/portal.php`
     - `local-rebuild/apps/izmirorkestra/lang/tr/portal.php`
   - Blade entegrasyonu:
     - `/hesabim` sekme/baslik/rol metinleri `portal.*` anahtarlarina tasindi
     - `/owner` menu ve ana baslik/donus metinleri `portal.*` anahtarlarina tasindi
     - `/customer` baslik ve buton metinleri `portal.*` anahtarlarina tasindi
   - Not:
     - Bu turda sadece label merkeziyetine odaklanildi; business logic degismedi.
   - Dogrulama:
     - dosya hash parity PASS (`apps/orkestram` == `apps/izmirorkestra`)
     - otomatik test: ortamda `php` komutu bulunamadigi icin calistirilamadi
54. Label standardi kapanis dogrulamasi tamamlandi:
   - `UI_LABEL_SOZLUGU_TR.md` ile portal menu/baslik label uyumu tekrar tarandi.
   - `Sorular/Sorularim` label'larinin aktif menu setinde olmadigi dogrulandi.
   - `portal.php` + kritik portal blade dosyalari icin iki app hash parity PASS.
   - Not:
     - Bu turda kod davranisi degismedi; sadece kapanis dogrulamasi ve dokuman kilidi yapildi.
55. Frontend listing karti parity refactoru yapildi:
   - Yeni ortak partial:
     - `resources/views/frontend/partials/listing-card.blade.php` (`izmirorkestra`)
   - `izmirorkestra` tarafinda kart render noktalari ortak partiala tasindi:
     - `frontend/home.blade.php`
     - `frontend/service-category.blade.php`
   - Etki:
     - kart metni (`Baslangic`) ve alan yerlesimi `orkestram` ile hizalandi
     - home + kategori kartlarinda tekrar eden markup azaltildi
    - Dogrulama:
      - partial dosya hash parity PASS (`orkestram` == `izmirorkestra`)
      - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
    - Not:
      - Dogrulama Docker uzerinden alindi (validate quick both + smoke).

## Guncel Net Durum (2026-03-12)
1. Kategori/lokasyon akisi deterministic adres modeline tasindi (iki app parity).
2. `listings.city_id` amaci net:
   - serbest metin sehir bilgisini degil, snapshot'tan gelen location dictionary kaydini referanslar.
   - `city` / `district` string alanlari backward-compatibility icin ID'den turetilir.
3. Admin + owner ilan girisi hizalandi:
   - il / ilce / mahalle select zinciri
   - cadde, sokak, dis kapi no, ic kapi no alanlari ayri alanlar olarak zorunlu.
4. Validasyon deterministic kilide alindi:
   - ilce secilen ile ait olmali
   - mahalle secilen il + ilceye ait olmali.
5. Parity acigi kapatildi:
   - `izmirorkestra` tarafinda eksik kalan `2026_03_11_200000_add_location_ids_and_address_fields_to_listings.php` migrationi eklendi ve senklendi.
6. Dogrulama sonucu:
   - `AdminListingMediaFlowTest` PASS (iki app)
   - `OwnerCustomerDbFlowTest` PASS (iki app)
   - smoke (`scripts/smoke-test.ps1 -App both`) PASS.
7. Public location filtre davranisi tamamlandi:
   - `/ilanlar` ekraninda sehir seciminde ilce listesi anlik dolduruluyor (cascading select).
   - `city_id/district_id` bazli filtre aktif; eski `city/district` query parametreleri geriye uyumlu.
8. Sonraki faz baslatma karari:
   - Service area deterministic fazi birinci oncelige alindi.
   - hedef: `listing_service_areas` tablosunu ID tabanina tasiyip coverage motorunu tam deterministic yapmak.
9. Service area deterministic fazi tamamlandi (iki app parity):
   - `listing_service_areas` tablosuna `city_id` + `district_id` alanlari eklendi.
   - migration ile mevcut satirlar icin ID backfill yapildi (eslesen kayitlar normalize edildi).
   - coverage sorgusu ID-oncelikli hale getirildi; string fallback kontrollu korundu.
   - `CategorySystemFlowTest` genisletildi (`listings filter prefers location ids when available`).
   - Dogrulama:
     - `CategorySystemFlowTest` PASS (iki app)
     - `AdminListingMediaFlowTest` PASS
     - `OwnerCustomerDbFlowTest` PASS (iki app)
     - smoke (`scripts/smoke-test.ps1 -App both`) PASS
10. Ownership policy fazi tamamlandi (iki app parity):
   - owner kaynak sahiplik kontrolu route middleware katmanina tasindi (`owner.owns`).
   - yeni policy servisi: owner -> listing/lead/feedback sahiplik karari tek noktada toplandi.
   - owner controller'larinda satir-ici `assertOwns*` tekrarlarÄ± kaldirildi.
   - Dogrulama:
     - `OwnerPanelActionsTest` PASS (iki app)
     - `OwnerCustomerDbFlowTest` PASS (iki app)
     - `ListingEngagementFlowTest` PASS
     - smoke (`scripts/smoke-test.ps1 -App both`) PASS
11. Hesabim + Owner hibrit akis fazi aktif:
   - varsayilan giris merkezi `/hesabim`.
   - owner operasyonu `Ilan Yonetimine Gec` aksiyonu ile `/owner` paneline gecis modeliyle ilerliyor.
   - dogrulama: kritik rol/engagement regression + smoke iki appte PASS.
12. Hesabim + Ilan Yonetimi final label/menu hizalama turu tamamlandi (iki app parity):
   - `/owner` menude `Genel Bakis` ilk sekme olarak kalici hale getirildi (`/owner` root ile ayni sayfa).
   - owner panel ana basligi `Ilan Yonetimi` olarak tekillestirildi.
   - donus aksiyonu `Hesap Paneline Don` olarak standartlastirildi.
   - `listing_owner` gorunur rol etiketi `Ilan Veren` olarak standarda alindi.
   - owner genel bakis metrikleri ilan/talep/etkilesim kirilimlariyla genisletildi.
   - dashboard icindeki tekrar eden hizli-yonlendirme butonlari kaldirildi.
13. Owner Ilan Mesajlari sohbet inbox modeli aktif edildi:
   - aktif referans plan: `MESAJ_V1_TR.md`
   - `Ilan Mesajlari` ekrani inbox/sohbet duzenine alindi (sol konusmalar + sag detay)
   - `Hizli Kaydet` dili kaldirildi, ana aksiyon `Gonder` olarak standardize edildi
   - mesaj ekraninda cift tablo ayrimi kaldirildi (`Ilanlarima Gelenler / Benim Gonderdiklerim`)
14. Dead code cleanup v2 tamamlandi (iki app parity):
   - `/messages` endpointi render yerine deterministic olarak `/hesabim?tab=messages` akisine yonlendirildi.
   - `portal/messages/index.blade.php` kaldirildi; tek mesaj omurgasi `portal.messages.center-content` oldu.
   - owner dashboard metric degisken adlandirmasi normalize edildi (`engagementCount` -> `feedbackCount`).
   - kalan legacy metinler temizlendi (`Etkilesim` -> yeni terminoloji).
   - Dogrulama:
     - `FeedbackModerationAccessTest` PASS (iki app)
     - `ListingFeedbackFlowTest` PASS (iki app)
     - `scripts/smoke-test.ps1 -App both` PASS
15. Release gate sertlestirme tamamlandi:
   - `scripts/release.ps1` icine zorunlu `validate` gate adimi eklendi.
   - `scripts/build-deploy-pack.ps1` icine zorunlu `validate` gate adimi eklendi.
   - release -> pack zincirinde cift dogrulamayi engellemek icin release tarafinda pack cagrisinda `-SkipValidate` standardi eklendi.
   - Dogrulama:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both` PASS
16. Release gate hardening + CI job tamamlandi:
   - `SkipValidate` bayraklari kaldirildi; manuel gate atlama yolu kapatildi.
   - release -> pack zinciri env tabanli trusted-caller modeliyle guvenli hale getirildi.
   - `.github/workflows/validate-gate.yml` eklendi (`self-hosted, windows` runner'da `validate -App both`).
   - Dogrulama:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both -Mode quick` PASS
17. PR akisi standardizasyonu tamamlandi:
   - `.github/pull_request_template.md` ile PR acilis formati zorunlu hale getirildi.
   - `scripts/pre-pr.ps1` eklendi (`ci-gate` + `validate` quick/full).
   - `docs/PR_FLOW_TR.md` ile branch->PR->merge adimlari dokumante edildi.
   - `docs/REPO_DISCIPLINE_TR.md` icine `pre-pr` zorunlu kapisi baglandi.
   - Dogrulama:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
18. Security gate (2. check) tamamlandi:
   - `scripts/security-gate.ps1` eklendi (potansiyel secret/key deseni taramasi).
   - `.github/workflows/security-gate.yml` eklendi (push + PR tetikleme).
   - `scripts/pre-pr.ps1` icine security adimi baglandi.
   - Dogrulama:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\security-gate.ps1` PASS
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
19. Listing kart standardi tek partiala alindi (iki app parity):
   - Ortak kart partiali eklendi:
     - `resources/views/frontend/partials/listing-card.blade.php` (`orkestram`)
     - `resources/views/frontend/partials/listing-card.blade.php` (`izmirorkestra`)
   - Kart render noktalarinda tek kaynak kullanimi aktif:
     - `frontend/home.blade.php`
     - `frontend/listings.blade.php`
     - `frontend/service-category.blade.php`
   - Home + kategori akislarinda kart ozellikleri (`cardAttributesByListing`) controller tarafinda aktiflendi.
   - Dogrulama:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both` PASS
20. Multi-agent task lock disiplini kalicilastirildi:
   - Yeni dokumanlar:
     - `docs/MULTI_AGENT_RULES_TR.md`
     - `docs/TASK_LOCKS.md`
     - `docs/tasks/_TEMPLATE.md`
   - Repo disiplini baglantisi:
     - `docs/REPO_DISCIPLINE_TR.md` icine task-lock + branch standardi eklendi
   - Dogrulama:
     - `Get-Content D:\orkestram\docs\MULTI_AGENT_RULES_TR.md` PASS
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
22. Disiplin tutarlilik turu tamamlandi:
   - Kayip gorunen `scripts/start-task.ps1` dosyasi repo icine yeniden geri alindi.
   - Script parse + pre-pr kapisi tekrarlandi.
   - Dogrulama:
     - `powershell -NoProfile -Command "[scriptblock]::Create((Get-Content 'D:\orkestram\scripts\start-task.ps1' -Raw)) | Out-Null; 'OK'"` PASS
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS

## Siradaki 5 Is
1. Teklif formu Faz 2 (zengin alanlar, validasyon, owner tarafi takip paneli) planlamak.
2. Validate gate workflow'unu zorunlu merge politikasiyla bagla (branch protection / required checks).
2. Branch protection'ta `security-gate` check'ini da required olarak etkinlestir.
3. Performans baseline olcumu (cold/warm endpoint raporu) ve yavaslama kaynagi raporu.
4. Service area string fallback daraltma fazi:
   - backfill eslesmeyen kayitlari raporla
   - yazma katmaninda tam ID zorunluluguna gecis plani cikar.
5. Hesabim + Owner hibrit akis Faz 2:
   - owner/customer menu labellarini Turkce standart sozlukte tekille.
   - `/owner` panel baslik/sekme adlarini `Ilan Sahibi` diliyle hizala.
   - son UI temizlik/polish + responsive son kontrol turunu tamamla.

