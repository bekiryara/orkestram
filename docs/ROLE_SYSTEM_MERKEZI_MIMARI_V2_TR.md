# Rol Sistemi Merkezi Mimari V2 (Deterministik)

Tarih: 2026-03-10

## 1) Hedef
1. Role/auth/panel davranisini iki appte tek kuralla yonetmek.
2. Kod tekrarini azaltmak, yeni rol eklemeyi config + test seviyesine cekmek.
3. Login/logout/hesabim akisini mobil uyumlu ve parity garantili hale getirmek.

## 2) Mimari Prensip
1. Kural tek kaynak: `config/admin_acl.php` (+ ileride `role_home_map`).
2. Auth zinciri deterministic:
   - session identity
   - basic header resolver
3. Yetki karari sadece middleware/policy katmaninda.
4. Controller rol karsilastirmasi yapmaz.
5. Iki appte ayni dosya seti + ayni test seti.

## 3) Fazlar

### Faz 1 - Session Auth Temeli (Tamamlandi)
1. Endpoint:
   - `GET /giris`
   - `POST /giris`
   - `POST /cikis`
   - `GET /hesabim`
2. Login sonrasi role-home redirect:
   - `listing_owner -> /owner`
   - `customer -> /customer`
   - `support_agent -> /support`
   - diger admin rolleri -> `/admin/pages`
3. `admin.basic`:
   - once session
   - sonra basic fallback
4. Test:
   - `PortalSessionAuthTest`

### Faz 2 - Ownership Policy (Plan)
1. Listing ve request ownership kurallarini Policy class'a tasima.
2. Controller icinde owner check kaldirma.
3. Ownership deny senaryolari icin feature test ekleme.

### Faz 3 - Hesabim Genisletme (Plan)
1. Profil guncelleme.
2. Sifre degistirme.
3. Son oturumlar/guvenlik logu (minimum metadata).

### Faz 4 - Merkezi Core Paketleme (Plan)
1. Ortak auth/ability/policy kodunu tek cekirdege tasima.
2. Iki appte sadece site-config ve tema farki birakma.
3. Parity checker script ile dosya ve route hash kontrolu.

## 4) Mobil Uyum Stratejisi
1. Ortak UI token dosyasi:
   - `resources/views/portal/partials/base-styles.blade.php`
2. Login + portal + hesabim ayni token setini kullanir.
3. Table ekranlari `table-wrap` ile yatay kaydirma destekler.
4. Aksiyonlar mobilde tam-genislik butona duser.
5. Nav mobilde yatay scroll ile kirilmasiz calisir.

## 5) Deterministik Kapanis Kriteri
1. Iki appte ayni auth/panel route seti.
2. `PortalSessionAuthTest` + rol test paketi iki appte PASS.
3. Login/logout/hesabim akislari mobilde kirilma olmadan calisir.
4. Sonraki adim ownership policy fazina gecilir.

## 6) Bu Tur Yapilanlar
1. Faz 1 kodu uygulandi.
2. Login/logout/hesabim ekranlari mobil uyumlu hale getirildi.
3. Portal ekranlarinda ortak responsive stil katmani devreye alindi.
4. Iki app parity testleri PASS:
   - 15 test, 47 assertion.
5. Header/footer ve role-home karar yapisi merkezi config + service katmanina alindi:
   - `config/role_home.php`
   - `config/navigation.php`
   - `App\Services\Ui\NavigationFactory`
   - `AppServiceProvider` view composer
