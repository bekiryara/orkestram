# TASK-055

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-055`  
Baslangic: `2026-03-17 00:23`

## Ozet
- Media runtime restore kaliciligi ve smoke gate sertlestirmesi

## In Scope
- [x] `scripts/dev-up.ps1` icinde runtime permission + mount source kontrolunu worktree uyumlu hale getirmek
- [x] `scripts/deploy-guard.ps1` icindeki deploy-pack scan patlamasini kapatmak
- [x] `scripts/smoke-test.ps1` icine admin listing thumb media URL kontrolu eklemek

## Out of Scope
- [ ] Uygulama blade/controller media mantigini degistirmek
- [ ] Canonical repo disindaki ayri stacklere mudahale etmek

## Lock Dosyalari
- `docs/tasks/TASK-055.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `scripts/dev-up.ps1`
- `scripts/deploy-guard.ps1`
- `scripts/smoke-test.ps1`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] `dev-up.ps1 -App both -LinuxProjectRoot /home/bekir/orkestram-k` local runtime zincirini hata vermeden tamamlar
- [x] `smoke-test.ps1` admin listing thumb URL icin `200` bekler ve iki appte PASS verir
- [x] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Legacy media veri restore isleri git-izlemeli degil; bu task script/gate sertlestirmesini kapsadi
- `git fetch --all --prune` icindeki bozuk local remotes ayri temizlik konusu olarak kalir
- Kanit: `dev-up PASS`, `smoke-test -App both PASS`, `pre-pr -Mode quick PASS`

