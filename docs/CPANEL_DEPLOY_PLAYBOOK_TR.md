# cPanel Deploy Playbook (TR)

Bu dokuman, "lokalde calisti ama cPanel'de calismadi" riskini minimuma indirmek icin zorunlu adimlari toplar.

## 1) Kesin Ortam Notlari

Dogrulanan document root:
1. `orkestram.net -> /public_html`
2. `izmirorkestra.net -> /izmirorkestra.net`

Hosting notu:
- PHP 8.3 aktif.
- `ext-xml` acilmiyorsa Plan B uygulanacak (vendor dahil deploy).

## 2) Preflight Checklist (Deploy Oncesi)

1. cPanel PHP:
   - PHP `8.3+`
   - Acik eklentiler: `bcmath, ctype, curl, fileinfo, mbstring, openssl, pdo, pdo_mysql, tokenizer, zip`
   - `xml` su an gecici `WARN` (gecisi durdurmayan uyari). Uretimde hedef: `xml` de acik.
2. DB:
   - DB olusturuldu
   - DB user olusturuldu
   - user DB'ye yetkili
3. Dosya yolu:
   - Domain dogru root'a bakiyor
4. Yedek:
   - Dosya yedegi alindi
   - DB yedegi alindi
5. Gecis kurali:
   - Redirectler kapali (faz 6'ya kadar)

## 3) Paketleme Stratejisi

Plan A:
- Sunucuda composer/migrate calisabilir ortam varsa standart deploy.

Plan B (oncelikli guvenli yol):
- Lokalde `composer install` tamamlanir.
- `vendor` dahil paket olusturulur.
- cPanel'e hazir zip yuklenir.
- Sunucuda composer gerekmeyebilir.

## 4) Deploy Adimlari (cPanel)

1. Hedef root klasore `deploy.zip` yukle.
2. `Extract` yap.
3. `.env` duzenle:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://domain`
   - `DB_*` alanlari dogru
   - `APP_KEY` dolu
4. Izinler:
   - `storage/` yazilabilir
   - `bootstrap/cache/` yazilabilir
5. `public` giris yapisi:
   - Domain root -> Laravel `public` mantigi dogru olacak
   - `.htaccess` aktif
6. Localden cPanel'e cikmadan once:
   - `deploy-guard.ps1 -Profile predeploy-check` PASS
   - `smoke-test.ps1` PASS

## 5) Zorunlu Smoke Test

Deploy sonrasi yayin acmadan:
1. Ana sayfa aciliyor mu
2. 3 kritik hizmet sayfasi aciliyor mu
3. Bir form post calisiyor mu
4. 404 sayfasi duzgun mu
5. Hata logunda kritik exception var mi

PASS olmadan yayina gecilmez.

## 6) Yayin Sonrasi Ilk 24 Saat

1. 404 log takibi
2. Form teslim takibi
3. Performance kontrol (TTFB/LCP)
4. Search Console hata kontrolu

## 7) Yaygin Hata ve Cozum

1. `500 Internal Server Error`
   - `.env`/izin/.htaccess kontrol
2. `Class not found` / autoload sorunu
   - paket eksik; vendor dahil tekrar deploy
3. `No application encryption key has been specified`
   - `.env` icinde `APP_KEY` eksik
4. `SQLSTATE` baglanti hatasi
   - DB host/user/password/database yanlis
5. URL calismiyor, sadece ana sayfa aciliyor
   - `mod_rewrite/.htaccess` sorunu

## 8) Rollback (Acil Geri Donus)

1. Yeni dosyalari kaldir, eski dosya yedegini geri yukle
2. Eski DB yedegini geri yukle
3. Domain root degisti ise eskiye al
4. Hata sebebini logdan teyit etmeden tekrar deploy etme

## 9) Operasyon Kurali

1. Once sistem stabil acilacak.
2. Sonra icerik tasima tamamlanacak.
3. En son redirectler aktif edilecek.
