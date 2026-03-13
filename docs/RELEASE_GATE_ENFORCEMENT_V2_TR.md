# Release Gate Enforcement V2 (TR)

Tarih: 2026-03-13  
Durum: Aktif

## Amac
- Release paketleme adiminda gate bypass riskini daraltmak.
- Sadece `release.ps1` zincirinden gelen cagriya guvenmek.

## Uygulanan Sertlestirme
1. `scripts/build-deploy-pack.ps1`
   - Yeni parametre: `-ReleaseContextToken`
   - `trusted caller` modu sadece su kosullarda aktif:
     - `ORKESTRAM_VALIDATE_GATE_PASSED=1`
     - `ORKESTRAM_RELEASE_CONTEXT=release.ps1`
     - `ORKESTRAM_RELEASE_APPROVED=HAZIR-YAYIN`
     - `ORKESTRAM_RELEASE_CONTEXT_TOKEN == -ReleaseContextToken`
   - Bu kosullar tutmazsa `validate gate` zorunlu calisir.
2. `scripts/release.ps1`
   - Onay kodu kontrolunden sonra tek-kullanimlik context token uretir.
   - `build-deploy-pack` cagrisina token'i hem env hem parametre ile iletir.
   - Cagri sonunda context env degiskenlerini temizler.

## Rollback
1. `scripts/build-deploy-pack.ps1` icindeki context kontrolunu kaldir.
2. `scripts/release.ps1` icindeki token/env aktarim bloklarini kaldir.

## Dogrulama
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```
