# TASK-020

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-020`  
Baslangic: `2026-03-13 17:40`  
Bitis: `2026-03-13 18:40`

## Ozet
- Merge tren koordinasyonu icin lock hijyeni, kapanis kaniti kurali ve pre-pr cikti standardi netlestirildi.

## In Scope
- [x] `docs/TASK_LOCKS.md` aktif/kapali durumlari gercekle hizalandi.
- [x] `docs/NEXT_TASK.md` uzerinde tek kaynak 3 task koordinasyonu netlestirildi.
- [x] `docs/PR_FLOW_TR.md` kapanis kaniti zorunlulugu eklendi.
- [x] `scripts/pre-pr.ps1` quick mod fail/exit ciktisi stabilize edildi.
- [x] `docs/WORKLOG.md` kaydi eklendi.

## Lock Dosyalari
- `docs/tasks/TASK-020.md`
- `docs/NEXT_TASK.md`
- `docs/TASK_LOCKS.md`
- `docs/WORKLOG.md`
- `docs/PR_FLOW_TR.md`
- `scripts/pre-pr.ps1`

## Kabul Kriteri
- [x] Aktif tasklar ile gercek branch durumu uyumlu.
- [x] Kapanis kaniti olmayan task `closed` edilmemis.
- [x] `pre-pr -Mode quick` PASS.
