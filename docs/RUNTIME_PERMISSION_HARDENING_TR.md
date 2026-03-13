# Runtime Permission Hardening (TR)

Tarih: 2026-03-13

## Amac
1. `storage/framework/views` ve `bootstrap/cache` yazma izin sorunlarini startup adiminda kalici olarak kapatmak.
2. Izin kaynakli hatalarda belirsiz fail yerine net hata mesaji uretmek.

## Uygulanan Degisiklikler
1. `scripts/dev-up.ps1`:
   - Yeni adim: runtime izin preflight
   - Her hedef container icin su adimlar zorunlu:
     - gerekli klasorleri olustur
     - `www-data:www-data` sahipligi ver
     - `ug+rwX` izinlerini uygula
     - `storage/framework/views` ve `bootstrap/cache` yazilabilirligini test et
   - Test basarisizsa deterministik FAIL:
     - `Runtime izin preflight FAIL: <container>`
2. `scripts/validate.ps1`:
   - Docker API izin hatasi icin net neden/yonlendirme mesaji eklendi.
   - Runtime izin hatasi (`storage/framework/views`, `bootstrap/cache`) icin net neden/yonlendirme mesaji eklendi.

## Operasyon Akisi
1. Standart startup:
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App both
```
2. Dogrulama:
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both -Mode quick
```

## Beklenen Sonuc
1. Startup sonunda iki container icin de:
   - `[dev-up] runtime-permissions OK <container>`
2. Izin sorunu varsa:
   - `validate` adiminda acik neden mesaji gorulur.

## Not
1. Bu turda business logic veya UI degisikligi yok.
2. Kapsam sadece runtime izin sertlestirme ve hata mesaji netlestirme.
