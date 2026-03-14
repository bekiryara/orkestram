# TASK-001

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-001`  
Baslangic: `2026-03-13 08:41`

## Ozet
- Multi-agent disiplin setini finalize et.

## In Scope
- [x] `TASK-AGENT-BOOTSTRAP` lock kaydini `closed` yapmak.
- [x] Tek aktif lock olarak `TASK-001` birakmak.
- [x] Gorev dosyasini template formatina duzeltmek.
- [ ] `pre-pr` quick dogrulamasini gecirmek.

## Out of Scope
- [ ] Uygulama ozellik kodlarinda fonksiyonel degisiklik.
- [ ] Yeni workflow veya yeni branch stratejisi eklemek.

## Lock Dosyalari
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/PROJECT_STATUS_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/TASK_LOCKS.md`
- `docs/tasks/_TEMPLATE.md`
- `scripts/start-task.ps1`

## Kabul Kriteri
- [x] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Branch sabit: `agent/codex/task-001`.
