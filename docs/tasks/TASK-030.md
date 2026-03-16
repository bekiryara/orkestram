# TASK-030

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-030`  
Baslangic: `2026-03-15 01:05`

## Ozet
- Koordinator ajanin belge yazma/okuma sirasinda karakter kacis hatalarini azaltmak icin mevcut disipline uygun "minimal degisim + satir bazli duzenleme" kuralini resmi hale getirme.

## In Scope
- [x] `docs/REPO_DISCIPLINE_TR.md` icine satir-bazli belge duzenleme disiplini ekleme
- [x] `docs/NEXT_TASK.md`, `docs/TASK_LOCKS.md`, `docs/WORKLOG.md` uzerinde resmi task kaydi ve kapanis
- [x] `pre-pr -Mode quick` PASS kaniti

## Out of Scope
- [ ] Runtime kod degisikligi
- [ ] Container/DB operasyonu

## Lock Dosyalari
- `docs/tasks/TASK-030.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Repo disiplin dokumaninda yeni kural acikca yazili.
- [x] Task kaydi ve lock kaydi tutarli.
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` => PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- `git fetch --all --prune` bu turda ag erisimi nedeniyle fail.