# TASK-042

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-042`  
Baslangic: `2026-03-16 14:05`
Kapanis: `2026-03-16 14:24`

## Ozet
- Koordinator cevap formatini, teslim kanit paketini, task acma sirasini ve runtime hijyen checklistini tek standarda baglamak.

## In Scope
- [x] Koordinator yeni is kararlama cevabini sabit 4 satir formatina baglamak.
- [x] Ajan kapanis kanit paketini 4 komut + `pre-pr PASS` standardina cekmek.
- [x] Yeni task acma sirasini belge ve script seviyesinde mekanik hale getirmek.
- [x] Runtime kisa hijyen checklistini eklemek.

## Out of Scope
- [ ] Uygulama feature gelistirmesi
- [ ] Runtime temizligi veya stale worktree bakimi

## Lock Dosyalari
- `AGENTS.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `scripts/start-task.ps1`
- `docs/tasks/TASK-042.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Koordinator cevap sablonu tum ana belgelerde ayni formatta tanimlidir.
- [x] Teslim kanit paketi `git branch --show-current`, `git branch -vv`, `git status --short`, `pre-pr PASS` olarak sabitlenmistir.
- [x] `start-task.ps1` task dosyasi -> lock -> NEXT_TASK -> branch sirasini uygular.
- [x] Runtime kisa hijyen checklisti belgelerde yer alir.
- [x] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `scripts/start-task.ps1` artik minimum task dosyasi, lock satiri ve NEXT_TASK kaydini yazar; aktif lock varken yeni task acmaz.
