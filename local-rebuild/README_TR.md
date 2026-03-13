# Orkestram Yerel Sifirdan Kurulum

Bu klasor, `orkestram.net` ve `izmirorkestra.net` icin yerel gelistirme ortamini hazirlar.

## Hedef
- Iki siteyi lokalde ayri ayri gelistirmek
- WordPress dump dosyalarindan URL ve medya envanteri cikarmak
- Yeni sistemi adim adim sifirdan insa etmek

## Klasorler
- `apps/orkestram` -> Orkestram yeni uygulama kodu
- `apps/izmirorkestra` -> Izmir Orkestra yeni uygulama kodu
- `docker` -> Yerel container build dosyalari
- `..\scripts` -> Yardimci otomasyon komutlari
- `..\docs\exports` -> URL/medya envanteri ciktilari

## 1) URL/Medya Envanteri Cikar

PowerShell:

```powershell
Set-Location D:\orkestram\local-rebuild
..\scripts\extract-url-inventory.ps1 `
  -DumpPath ..\orkestram.net.sql.gz `
  -Domain orkestram.net `
  -OutDir ..\docs\exports\orkestram

..\scripts\extract-url-inventory.ps1 `
  -DumpPath ..\izmirorkestra.net.sql.gz `
  -Domain izmirorkestra.net `
  -OutDir ..\docs\exports\izmirorkestra
```

Uretilen dosyalar:
- `all-urls.txt`
- `media-urls.txt`
- `page-like-urls.txt`

## 2) Docker Ortamini Baslat

```powershell
Set-Location D:\orkestram\local-rebuild
docker compose up -d
```

Servisler:
- `http://localhost:8180` -> orkestram local site
- `http://localhost:8181` -> izmirorkestra local site
- `http://localhost:8188` -> Adminer

## 3) Yeni Kodlari Baslat

Uygulamalar Laravel tabaninda hazir. Ilk calisma icin migration + seed adimi:

```powershell
Set-Location D:\orkestram\local-rebuild\apps\orkestram
php artisan migrate --seed
```

Aynisini `apps/izmirorkestra` icin de yap.

## Notlar
- Bu asama canli siteyi degistirmez.
- Once lokalde bitirip sonra tek paket deployment yapacagiz.
