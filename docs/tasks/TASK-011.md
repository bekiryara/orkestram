# TASK-011

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-011`  
Baslangic: `2026-03-13 11:30`

## Ozet
- Runtime izin sertlestirmesini operasyonel olarak kalici hale getir.

## In Scope
- [x] `scripts/dev-up.ps1` icine storage/bootstrap izin preflight adimi ekle.
- [x] `scripts/validate.ps1` icinde izin hatasini acik FAIL sebebi olarak raporla.
- [x] Uygulama adimlarini `docs/RUNTIME_PERMISSION_HARDENING_TR.md` ile dokumante et.
- [x] `pre-pr -Mode quick` PASS.

## Out of Scope
- [x] Business logic degisikligi.
- [x] UI degisikligi.

## Lock Dosyalari
- `docs/tasks/TASK-011.md`
- `scripts/dev-up.ps1`
- `scripts/validate.ps1`
- `docs/RUNTIME_PERMISSION_HARDENING_TR.md`

## Kabul Kriteri
- [x] Izin sorunu oldugunda deterministik FAIL mesaji var.
- [x] Startup adiminda kalici izin duzeltme dogrulanmis.
- [x] `pre-pr` PASS.

## Kapanis Notu
- `pre-pr -Mode quick` PASS (2026-03-13 12:24).
