# E2E Acceptance Gate (TR)

## Amac
- Kritik akislarin her iki appte zorunlu olarak calismasini garanti etmek.
- Hedef akislar:
  - Ilan/hizmet sayfasi erisimi
  - Musteri -> ilan sahibi -> destek operasyon akisi
  - Admin panel erisimi

## Gate Paketi
`scripts/validate.ps1` icinde her container icin su testler zorunlu calisir:
- `EndToEndRoleJourneyTest`
- `PublicAndSeoRoutesTest`
- `AdminAccessTest`

## Kapsam
- `orkestram-local-web`
- `izmirorkestra-local-web`

## Komut
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both -Mode quick
```

## Beklenen Cikti
- Her test adimi icin `[validate] OK -> ...`
- Son satir: `[validate] PASS`

## Not
- Bu gate fail olursa `pre-pr` da fail olur ve commit/push oncesi bloklar.
