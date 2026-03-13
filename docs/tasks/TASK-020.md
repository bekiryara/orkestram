# TASK-020

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-020`  
Baslangic: `2026-03-13 17:40`

## Ozet
- Multi-agent merge trenini teknik borcsuz kapat: lock hijyeni + gate enforcement + kapanis kaniti standardi.

## In Scope
- [ ] `docs/TASK_LOCKS.md` aktif/kapali durumlarini gercek hale getir.
- [ ] `docs/NEXT_TASK.md` uzerinden tek kaynak 3 task koordinasyonunu netlestir.
- [ ] `docs/PR_FLOW_TR.md` icine "kapanis kaniti zorunlu" kuralini net yaz.
- [ ] `scripts/pre-pr.ps1` quick mod ciktisini net fail/exit koduyla stabilize et (gerekirse).
- [ ] `docs/WORKLOG.md` kaydini ekle.

## Out of Scope
- [ ] UI redesign.
- [ ] Yeni endpoint/model.

## Lock Dosyalari
- `docs/tasks/TASK-020.md`
- `docs/NEXT_TASK.md`
- `docs/TASK_LOCKS.md`
- `docs/WORKLOG.md`
- `docs/PR_FLOW_TR.md`
- `scripts/pre-pr.ps1`

## Kabul Kriteri
- [ ] Aktif tasklar ile gercek branch durumu uyumlu.
- [ ] Kapanis kaniti olmayan task `closed` edilmemis.
- [ ] `pre-pr -Mode quick` PASS.
