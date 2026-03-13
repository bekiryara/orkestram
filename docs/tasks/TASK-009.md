# TASK-009

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-009`  
Baslangic: `2026-03-13 10:40`

## Ozet
- Service area fallback daraltma planini operasyonel ve uygulanabilir checklist formatina cevir.

## In Scope
- [x] `docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md` dokumanini net adimlara bol.
- [x] Her adim icin risk ve rollback notu ekle.
- [x] Dogrulama/adim-sonu kontrol satirlari ekle.
- [x] `pre-pr -Mode quick` PASS al.

## Out of Scope
- [ ] Runtime kod degisikligi.
- [ ] DB migration uygulamasi.

## Lock Dosyalari
- `docs/tasks/TASK-009.md`
- `docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md`

## Kabul Kriteri
- [x] Plan checklist formatinda ve uygulanabilir.
- [x] Risk + rollback notlari net.
- [x] `pre-pr` PASS.

## Sonuc
- `docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md` operasyonel checklist formatina cevrildi.
- Her fazda adim/risk/rollback/dogrulama satirlari netlestirildi.
- `pre-pr -Mode quick` PASS alindi.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```
