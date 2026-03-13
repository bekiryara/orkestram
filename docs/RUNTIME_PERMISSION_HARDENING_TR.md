# Runtime Permission Hardening (TR)

## Amac
- Container baslangicinda `storage` ve `bootstrap/cache` izinlerini kalici ve deterministik hale getirmek.
- Izin kaynakli gizli hatalari erken FAIL ile yakalamak.

## Yapilanlar
- `scripts/dev-up.ps1`
  - `Ensure-RuntimePermissions` adimi eklendi.
  - Her hedef container icin:
    - Gerekli klasorler olusturulur.
    - `www-data:www-data` sahipligi uygulanir.
    - `ug+rwX` izinleri uygulanir.
    - Yazma testi ile dogrulama yapilir.
- `scripts/validate.ps1`
  - Docker API izin hatasi icin acik FAIL nedeni eklendi.
  - Runtime path izin hatasi (`storage/framework/views`, `bootstrap/cache`) icin acik FAIL nedeni eklendi.

## Operasyon
- Ortami ayaga kaldirma:
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App both
```

- Hizli dogrulama:
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Beklenen Sonuc
- `dev-up` icinde her container icin:
  - `[dev-up] runtime-permissions OK <container>`
- `pre-pr` sonucunda:
  - `[pre-pr] PASS`

