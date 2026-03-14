# TASK-021

Durum: `DOING`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-021`  
Baslangic: `2026-03-13 17:54`

## Ozet
- Iki appte smoke + acceptance testlerini toparlayip UI/shell degisikliklerinden sonra regresyon olmadigini kilitle.

## In Scope
- [ ] `validate` ve kritik feature test paketlerini iki appte calistir.
- [ ] Failure varsa minimal fix + tekrar test.
- [ ] Sonuclari `docs/E2E_ACCEPTANCE_GATE_TR.md` notlarina isle.
- [ ] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Yeni ozellik ekleme.
- [ ] UI redesign.

## Lock Dosyalari
- `docs/tasks/TASK-021.md`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`
- `scripts/validate.ps1`
- `docs/E2E_ACCEPTANCE_GATE_TR.md`

## Kabul Kriteri
- [ ] Tum kritik endpoint/test akislari PASS.
- [ ] Iki app parity bozulmamis.
- [ ] `pre-pr` PASS.
