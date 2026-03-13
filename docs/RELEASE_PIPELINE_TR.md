# Release Pipeline (TR)

Bu akis localde PASS almadan deploy paketi uretmez.

## 1) Tek Komut Akis

```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\release.ps1 -App both
```

Ne yapar:
1. `dev-up.ps1` -> container + `deploy-guard.ps1 -Profile local-check`
2. `validate.ps1 -Mode quick` -> feature test + smoke gate
3. `smoke-test.ps1` -> temel URL testleri
4. `build-deploy-pack.ps1` -> deploy zip uretimi

Gate kurali:
1. `validate` PASS olmadan release akisi paket adimina gecmez.
2. `build-deploy-pack.ps1` dogrudan cagrildiginda da kendi `validate` gate'ini calistirir.

## 2) Scriptler ve Profiller

`deploy-guard.ps1`:
1. `local-check`:
   - local gelistirmede gereksiz kirmaz
   - xml eksigi `WARN`
2. `predeploy-check`:
   - production kurallari zorunlu
   - kritik sapmada `FAIL` (exit 1)

`smoke-test.ps1`:
1. `/`, `/admin/pages`, `/this-should-404` testlerini calistirir
2. timeout icin retry mekanizmasi vardir

## 3) Hizli Build Kullanimi

`build-deploy-pack.ps1` hizlandirildi:
1. `robocopy /MT` paralel kopya
2. `Compress-Archive -CompressionLevel Fastest`
3. `7z` varsa otomatik kullanabilme
4. opsiyonel `-SkipVendor`
5. zorunlu `validate gate` (release zinciri disinda da devrede)

Ornekler:

```powershell
# Tek site (onerilen hizli akis)
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\build-deploy-pack.ps1 -App orkestram -Compressor auto

# Iki site birden
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\build-deploy-pack.ps1 -App both -Compressor auto

# Vendor haric (sadece sunucuda composer kullanilacaksa)
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\build-deploy-pack.ps1 -App orkestram -Compressor auto -SkipVendor
```

Not:
1. En stabil hiz icin `-App both` yerine tek tek build onerilir.
2. Build surerken ayni anda agir test/import/calismalar yapilmaz.

## 4) Uretilen Cikti

Konum:
1. `D:\orkestram\deploy\orkestram_<timestamp>\app.zip`
2. `D:\orkestram\deploy\izmirorkestra_<timestamp>\app.zip`

Her release klasorunde:
1. `app.zip`
2. `.env.production.example`
3. `DEPLOY_INFO.txt`

## 5) cPanel Yukleme Ozeti

1. Domain docroot klasorune `app.zip` yukle ve cikar.
2. `.env.production.example` dosyasini `.env` olarak kopyala.
3. `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_*`, `APP_KEY` alanlarini doldur.
4. `storage` ve `bootstrap/cache` yazma izinlerini dogrula.
5. `predeploy-check` PASS olmadan canli acma.

## 6) Icerik Import (CSV)

Template dosyalari:
1. `D:\orkestram\docs\import-pages.template.csv`
2. `D:\orkestram\docs\import-listings.template.csv`
3. `D:\orkestram\docs\import-city-pages.template.csv`

Komut ornekleri:

```powershell
docker exec orkestram-local-web sh -lc "cd /var/www/html && php artisan content:import pages /var/www/html/storage/app/import-pages.csv --site=orkestram.net --published"
docker exec orkestram-local-web sh -lc "cd /var/www/html && php artisan content:import listings /var/www/html/storage/app/import-listings.csv --site=orkestram.net --published"
docker exec orkestram-local-web sh -lc "cd /var/www/html && php artisan content:import city-pages /var/www/html/storage/app/import-city-pages.csv --site=orkestram.net --published"
```
