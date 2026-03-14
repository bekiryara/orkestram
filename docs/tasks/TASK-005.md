# TASK-005

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-001`  
Baslangic: `2026-03-13 10:15`

## Ozet
- TASK-004 ile olusturulan baseline olcum ciktisini "aksiyon alinir" hale getir:
  - esik tanimi
  - drift/gerileme yorumu
  - tekrar-edilebilir olcum protokolu

## In Scope
- [x] `docs/PERFORMANCE_BASELINE_ACTION_PLAN_TR.md` olustur.
- [x] Endpoint bazli hedef esikler (cold/warm) tanimla.
- [x] "PASS/WARN/FAIL" karar kurallarini netlestir.
- [x] Haftalik tekrar olcum ve rapor update ritmini yaz.
- [x] Sonunda `pre-pr` PASS al.

## Out of Scope
- [ ] Runtime optimizasyon kodu.
- [ ] CI workflow degisikligi.
- [ ] `scripts/perf-baseline.ps1` degisikligi (TASK-004 kapsami).

## Lock Dosyalari
- `docs/tasks/TASK-005.md`
- `docs/PERFORMANCE_BASELINE_ACTION_PLAN_TR.md`

## Kabul Kriteri
- [x] TASK-004 raporundaki metrikler icin net esik tablosu var.
- [x] Karar kurali acik: ne zaman WARN, ne zaman FAIL.
- [x] Tekrarlanabilir olcum adimlari yazili.
- [x] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- TASK-004 ciktisi referans:
  - `docs/PERFORMANCE_BASELINE_REPORT_TR.md`
  - `scripts/perf-baseline.ps1`
- 2026-03-13 10:35: `docs/TASK_LOCKS.md` dosyasi TASK-002 lock kapsaminda oldugu icin lock tablosu bu turda bilincli olarak degistirilmedi.
- Dogrulama:
  - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
