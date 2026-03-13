# TASK-016

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-016`  
Baslangic: `2026-03-13 13:20`  
Bitis: `2026-03-13 14:45`

## Ozet
- Iki appte kritik uctan uca akis testleri acceptance gate'e zorunlu baglandi.

## In Scope
- [x] Iki appte kritik E2E feature test senaryolari iyilestirildi.
- [x] `scripts/validate.ps1` icine acceptance test paketi zorunlu gate olarak baglandi.
- [x] `docs/E2E_ACCEPTANCE_GATE_TR.md` olusturuldu.
- [x] `pre-pr -Mode quick` PASS.

## Lock Dosyalari
- `docs/tasks/TASK-016.md`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`
- `scripts/validate.ps1`
- `docs/E2E_ACCEPTANCE_GATE_TR.md`

## Kabul Kriteri
- [x] Ilan/hizmet/admin kritik akis testleri gate paketinde.
- [x] Her iki appte ayni gate paketi calisir.
- [x] `pre-pr` PASS.
