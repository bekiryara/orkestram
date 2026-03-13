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

## Faz 2 (Guard Fail-Fast)
- `scripts/deploy-guard.ps1` profile bazli runtime writable kontrolu uygular.
- Kontrol edilen runtime path'ler:
  - `storage/framework/views`
  - `bootstrap/cache`
- Path eksikse veya yazilabilir degilse guard deterministik `FAIL` verir.

## Rollout
1. `dev-up` ile lokal ortam acilir.
2. `pre-pr -Mode quick` calistirilir.
3. Gerekirse tekil guard kontrolu:
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\deploy-guard.ps1 -Profile predeploy-check -Domain orkestram.net -EnvFilePath D:\orkestram\local-rebuild\apps\orkestram\.env -DeployPackPath D:\orkestram\local-rebuild\apps\orkestram
```

## Rollback
1. `scripts/deploy-guard.policy.json` icinde `enforce_runtime_writable_paths` degerini gecici olarak `false` yap.
2. Runtime path listesinde sorunlu patikayi gecici olarak kaldir.
3. `pre-pr -Mode quick` tekrar calistir, sorunun kaynagini kalici cozumle duzelt, sonra policy'yi tekrar sikilastir.

