# PR Akisi (TR)

Amac: Main branch'e sadece kontrollu, testli ve izlenebilir degisim gitsin.

## Standart Akis
1. Gorev sec:
   - `docs/NEXT_TASK.md`
2. Branch ac:
   - `git checkout -b agent/<ajan>/<task-id>`
3. Remote hizasini dogrula:
   - `git remote -v`
   - `git branch -vv`
   - Beklenen: `origin = https://github.com/bekiryara/orkestram.git`
   - Worktree'de local WSL repo gerekiyorsa `canonical = /home/bekir/orkestram`
3. Degisiklik yap + commit
4. PR oncesi zorunlu:
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
5. Push:
   - `git push -u origin agent/<ajan>/<task-id>`
6. GitHub PR ac:
   - base: `main`, compare: `agent/<ajan>/<task-id>`
7. `ci-gate` PASS olmadan merge yok
8. Merge sonrasi:
   - `docs/WORKLOG.md` ve `docs/PROJECT_STATUS_TR.md` guncelle

## Pull/Fetch Kurali
1. Gunluk senkron:
   - `git fetch origin --prune`
2. Kendi branch'in guncellenecekse:
   - `git pull --ff-only origin agent/<ajan>/<task-id>`
3. `main` ile hizalama gerekiyorsa:
   - `git fetch origin --prune`
   - `git merge --ff-only origin/main` veya ekip kararina uygun kontrollu rebase
4. `windows-mirror` remote'u ile pull/push yapilmaz.

## Kapanis Kaniti Zorunlu (Yeni)
1. Task `closed` isaretlemeden once su 3 kanit zorunludur:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
2. Bu kanitlardan biri eksikse:
   - Task lock satiri `active` kalir.
   - PR acilsa bile merge edilmez.
3. Koordinator, `docs/TASK_LOCKS.md` ve `docs/NEXT_TASK.md` uyumsuzlugunu ayni turda duzeltir.

## Yasaklar
1. Main'e direkt push
2. Test calistirmadan PR acma
3. PR template'i bos gecme

