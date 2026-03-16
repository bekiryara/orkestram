# TASK-034

Durum: `TODO`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-034`  
Baslangic: `2026-03-15 07:10`

## Ozet
- Paralel ajanlar icin dosya cakismasiz lock matrisi ve gorev dagitim standardini olusturmak.

## In Scope
- [ ] A/B/C ajanlari icin lock matrisini olusturmak (dosya deseni bazli).
- [ ] Cakismazlik kurali yazmak (ayni dosya iki aktif taskta yasak).
- [ ] Koordinatorun lock dagitim/degisim yetki sinirlarini netlestirmek.
- [ ] Task kapanisinda lock closure checklisti eklemek.

## Out of Scope
- [ ] Kod refactor
- [ ] Test senaryosu genisletme
- [ ] Runtime konfigurasyon degisikligi

## Lock Dosyalari
- `docs/tasks/TASK-034.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_LOCK_MATRIX_TR.md`

## Kabul Kriteri
- [ ] A/B/C lock matrisi uygulanabilir tablo olarak yazilir.
- [ ] Cakisma oldugunda uygulanacak karar agaci yazilir.
- [ ] Koordinator devralma/proxy kurali net olur.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Hedef: paralel ajanlari hizlandirirken merge/lock krizi cikmamasini garanti etmek.

